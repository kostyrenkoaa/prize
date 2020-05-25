<?php

namespace App\Services;

use App\Raffle;
use App\RaffleResult;
use App\Repositories\RaffleResultRepository;
use App\Repositories\UserRepository;
use App\User;
use Illuminate\Support\Facades\Auth;

class RaffleResultServices
{
    const COEF_BALL = 'prizeBall';
    const COEF_MONEY = 'prizeMoney';
    const COEF_PRIZE = 'prizePrize';

    const COEF_MONEY_BALLS = 2;

    protected $raffleService;
    protected $raffleResultRepository;
    protected $userRepository;

    public function __construct(
        RaffleService $raffleService,
        RaffleResultRepository $raffleResultRepository,
        UserRepository $userRepository
    ) {
        $this->raffleService = $raffleService;
        $this->raffleResultRepository = $raffleResultRepository;
        $this->userRepository = $userRepository;
    }

    public function acceptPrize($status, $resultId, $userId)
    {
        $raffleResult = $this->raffleResultRepository->findInRevival($resultId, $userId);
        if (empty($raffleResult)) {
            return [
                'status' => 'error',
                'msg' => 'Не найден результат(',
            ];
        }

        if (!in_array($status, $raffleResult->getAllStatuses())) {
            return [
                'status' => 'error',
                'msg' => 'Не найден результат для изменения(',
            ];
        }

        $this->addBallsWithChangeStatus($raffleResult, $status);

        $raffleResult->status = $status;
        $raffleResult->save();

        return [
            'status' => 'ok',
            'msg' => 'Ваше решение принято',
        ];
    }

    protected function AddBallsWithChangeStatus(RaffleResult $raffleResult, $status)
    {
        $balls = $this->getCountBallsForChangeStatus($raffleResult, $status);
        if (empty($balls)) {
            return;
        }

        $this->userRepository->addBalls($raffleResult->user_id, $balls);
    }

    protected function getCountBallsForChangeStatus(RaffleResult $raffleResult, $status)
    {
        if ($status == RaffleResult::STATUS_TRANSLATED_TO_BALLS) {
            return $raffleResult->money * static::COEF_MONEY_BALLS;
        }

        if ($status == RaffleResult::STATUS_REFUSAL_PRIZE) {
            return $this->raffleService->getCoefPrize($raffleResult->raffle_id, $raffleResult->prize);
        }

        return 0;
    }

    public function getResult($resultId, $userId)
    {
        $raffleResult = $this->raffleResultRepository->find($resultId);

        if (empty($raffleResult)) {
            return ['error' => 'Не найден результат'];
        }

        if ($raffleResult->user_id != $userId) {
            return ['error' => 'Ошибка доступа'];
        }

        return [
            'status' => $raffleResult->status,
            'prizes' => [
                'money' => $raffleResult->money,
                'prize' => $raffleResult->prize,
                'balls' => $raffleResult->balls,
            ]
        ];
    }

    public function createPrize($raffleId): RaffleResult
    {
        $raffleResult = new RaffleResult();
        $raffleResult->user_id = Auth::id();
        $raffleResult->raffle_id = $raffleId;
        $raffleResult->save();
        return $raffleResult;
    }

    public function addPrize($userId, $resultId)
    {
        //$userId Для проверок. Например для ограничения количества участия в конкурсах
        $raffleResult = $this->raffleResultRepository->find($resultId);
        if (empty($raffleResult) || $raffleResult->status != RaffleResult::STATUS_IN_QUEUE) {
            return;
        }

        $dataForAddPrize = $this->raffleService->getDataForAddPrize($raffleResult->raffle_id);
        $dataForAddPrize['prizes'] = $this->getLastsPrizes($dataForAddPrize['prizes']);

        /** @var string $prizeType Ожидаемые значения prizeBallAdder prizeMoneyAdder prizePrizeAdder*/
        $prizeType = $this->getPrizType($dataForAddPrize) . 'Adder';
        if (!method_exists($this, $prizeType)) {
            throw new \Exception('Не определена функция ' . $prizeType);
        }

        $this->$prizeType($raffleResult, $dataForAddPrize);
        $raffleResult->save();
    }

    protected function prizeBallAdder(RaffleResult $raffleResult, $dataForAddPrize)
    {
        /** @var Raffle $raffle */
        $raffle = $dataForAddPrize['raffle'];
        $user = $this->userRepository->find($raffleResult->user_id);

        if (empty($user)) {
            throw new \Exception('Пользователь для добавления не найден. raffleResult.id ' . $raffleResult->id);
        }
        $balls = rand($raffle->ball_min, $raffle->ball_max);

        $this->userRepository->addBalls($raffleResult->user_id, $balls);

        $raffleResult->status = RaffleResult::STATUS_TRANSLATED_TO_BALLS;
        $raffleResult->balls = $balls;
    }

    protected function prizeMoneyAdder(RaffleResult $raffleResult, $dataForAddPrize)
    {
        $realMoney = $dataForAddPrize['money'];
        /** @var Raffle $raffle */
        $raffle = $dataForAddPrize['raffle'];
        $max = $raffle->money_max;
        if ($max > $realMoney) {
            $max = $realMoney;
        }
        $min = $raffle->money_min;
        if ($min > $realMoney) {
            $min = $realMoney;
        }

        $money = rand($min, $max);
        $raffleResult->money = $money;
        $raffleResult->status = RaffleResult::STATUS_PENDING_DECISION;
    }

    protected function prizePrizeAdder(RaffleResult $raffleResult, $dataForAddPrize)
    {
        /** @var Raffle $raffle */
        $raffle = $dataForAddPrize['raffle'];

        $prizeKey = array_rand($dataForAddPrize['prizes']);
        $raffleResult->prize = $dataForAddPrize['prizes'][$prizeKey]['name'];
        $raffleResult->status = RaffleResult::STATUS_PENDING_DECISION;
    }


    protected function getPrizType($dataForAddPrize)
    {
        $coefficients = $this->getCoefficients($dataForAddPrize);
        if (empty($coefficients[static::COEF_MONEY]) && empty($coefficients[static::COEF_PRIZE])) {
            return static::COEF_BALL;
        }

        $min = min($coefficients);
        if (empty($min)) {
            $min = 1;
        }

        $max = max($coefficients);

        $controlCoef = rand($min, $max);

        $coefficientsWithControl = array_filter(
            $coefficients,
            function ($coefficient) use ($controlCoef) {
                return $coefficient >= $controlCoef;
            }
        );

        return array_rand($coefficientsWithControl);
    }

    protected function getCoefficients($dataForAddPrize)
    {
        /** @var Raffle $raffle */
        $raffle = $dataForAddPrize['raffle'];
        $moneyCoef = $raffle->money_coef;
        if (empty($dataForAddPrize['money'])) {
            $moneyCoef = 0;
        }

        $prizeCoef = $raffle->prize_coef;
        if (empty($dataForAddPrize['prizes'])) {
            $prizeCoef = 0;
        }

        $ballCoef = $raffle->ball_coef;

        return [
            static::COEF_MONEY => $moneyCoef,
            static::COEF_PRIZE => $prizeCoef,
            static::COEF_BALL =>$ballCoef
        ];
    }

    protected function getLastsPrizes($prizes)
    {
        foreach ($prizes as $keyPrize => $prize) {
            if ($prize['count'] > 0) {
                continue;
            }
            unset($prizes[$keyPrize]);
        }

        return $prizes;
    }
}
