<?php

namespace App\Services;

use App\RaffleResult;
use App\Repositories\RaffleResultRepository;
use App\Repositories\UserAccountRepository;
use App\UserAccount;
use Illuminate\Support\Facades\Http;

class SendMoneyServices
{
    protected $accountRepository;
    protected $raffleResultRepository;

    public function __construct(UserAccountRepository $accountRepository, RaffleResultRepository $raffleResultRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->raffleResultRepository = $raffleResultRepository;
    }


    /**
     * Отправка денег в банк
     *
     * @param $url
     * @param $rows
     * @return int
     */
    public function send($url, $rows)
    {
        $raffleResult = $this->raffleResultRepository->getFirsRows($rows);

        $count = 0;
        foreach ($raffleResult as $result) {
            $userAccount = $this->accountRepository->find($result->user_id);
            $response = $this->sendRequest($userAccount, $result, $url);
            if ($response) {
                $result->status = RaffleResult::STATUS_ENROLLED;
                $result->save();
                $count++;
                continue;
            }
            //todo добавить логирование
        }

        return $count;
    }

    /**
     * Непосредственная отправка денег в банк
     *
     * @param UserAccount $userAccount
     * @param RaffleResult $raffleResult
     * @param $url
     * @return bool
     */
    protected function sendRequest(UserAccount $userAccount, RaffleResult $raffleResult, $url)
    {
        $response = Http::get(
            $url,
            [
                'bankId' => $userAccount->bank_id,
                'number' => $userAccount->number,
                'money' => $raffleResult->money,
            ]
        );

        return $response->ok();
    }
}
