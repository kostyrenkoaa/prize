<?php

namespace App\Services;

use App\Raffle;
use App\Repositories\RaffleRepository;
use App\Repositories\RaffleResultRepository;

class RaffleService
{
    /**
     * @var RaffleRepository
     */
    protected $raffleRepository;

    /**
     * @var RaffleResultRepository
     */
    protected $raffleResultRepository;

    public function __construct(RaffleRepository $raffleRepository, RaffleResultRepository $raffleResultRepository)
    {
        $this->raffleRepository = $raffleRepository;
        $this->raffleResultRepository = $raffleResultRepository;
    }

    public function getDateInRaffle($id)
    {
        $raffle = $this->raffleRepository->find($id);
        if (empty($raffle)) {
            return [];
        }
        return [
            'money' => $this->getMoney($raffle),
            'prizes' => $this->preparePrizeData($this->getPrizeData($raffle)),
        ];
    }

    /**
     * @param $id
     * @return array
     */
    public function getDataForAddPrize($id)
    {
        $raffle = $this->raffleRepository->find($id);
        if (empty($raffle)) {
            return [];
        }

        return [
            'money' => $this->getMoney($raffle),
            'prizes' => $this->getPrizeData($raffle),
            'raffle' => $raffle,
        ];
    }

    public function getCoefPrize($raffleId, $prizeName)
    {
        $raffle = $this->raffleRepository->find($raffleId);
        if (empty($raffle)) {
            return 0;
        }

        $prizes = json_decode($raffle->prizes, true);

        foreach ($prizes as $prize) {
            if ($prize['name'] == $prizeName) {
                return $prize['balls'];
            }
        }

        return 0;
    }

    protected function getMoney(Raffle $raffle)
    {
        $count = $raffle->money_count;
        $allByRaffleId = $this->raffleResultRepository->getAllRaffleWithMoney($raffle->id);
        if (empty($allByRaffleId) || empty($allByRaffleId->count())) {
            return $count;
        }

        foreach ($allByRaffleId as $RaffleResult) {
            if (empty($RaffleResult->money)) {
                continue;
            }
            $count -= $RaffleResult->money;
        }

        return $count;
    }

    protected function getPrizeData($raffle)
    {
        $prizes = json_decode($raffle->prizes, true);
        if (empty($prizes)) {
            return [];
        }

        $allByRaffleId = $this->raffleResultRepository->getAllRaffleWithPrize($raffle->id);
        if (empty($allByRaffleId) || empty($allByRaffleId->count())) {
            return $prizes;
        }

        $countsInResult = [];
        foreach ($allByRaffleId as $prizeInBD) {
            $key = $prizeInBD->prize;
            if (empty($countsInResult[$key])) {
                $countsInResult[$key] = 0;
            }
            $countsInResult[$key]++;
        }

        foreach ($prizes as $keyPrize => $prize) {
            $name = $prize['name'];
            if (empty($countsInResult[$name])) {
                continue;
            }
            $prizes[$keyPrize]['count'] -= $countsInResult[$name];
        }

        return $prizes;
    }

    protected function preparePrizeData($prizes)
    {
        $result = [];
        foreach ($prizes as $prize) {
            $result[] = [
                'name' => $prize['name'],
                'count' => $prize['count'],
            ];
        }

        return $result;
    }
}
