<?php

namespace App\Repositories;

use App\RaffleResult;
use \Illuminate\Database\Eloquent\Builder;

class RaffleResultRepository
{
    /**
     * @param $id
     * @return RaffleResult|null
     */
    public function find($id): ?RaffleResult
    {
        return RaffleResult::query()->find($id);
    }

    /**
     * @param $id
     * @param $userId
     * @return RaffleResult|null
     */
    public function findInRevival($id, $userId): ?RaffleResult
    {
        return RaffleResult::query()
            ->where('id', '=',  $id)
            ->where('status', '=', RaffleResult::STATUS_PENDING_DECISION)
            ->where('user_id', '=', $userId)
            ->first();
    }

    /**
     * @param $raffleId
     * @return RaffleResult[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllRaffleWithMoney($raffleId)
    {
        return RaffleResult::query()
            ->where('raffle_id', '=', $raffleId)
            ->where(function (Builder $builder) {
                $builder->where('status',  RaffleResult::STATUS_PENDING_DECISION)
                ->orWhere('status',  RaffleResult::STATUS_ACCRUED)
                ->orWhere('status',  RaffleResult::STATUS_ENROLLED);
            })
            ->where('money', '!=', 0)
            ->get();
    }

    /**
     * @param $raffleId
     * @return RaffleResult[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllRaffleWithPrize($raffleId)
    {
        return RaffleResult::query()
            ->where('raffle_id', '=', $raffleId)
            ->where(function (Builder $builder) {
                $builder->where('status',  RaffleResult::STATUS_PENDING_DECISION)
                ->orWhere('status',  RaffleResult::STATUS_AWAITING_SUBMISSION)
                ->orWhere('status',  RaffleResult::STATUS_SENT);
            })
            ->where('prize', '!=', '')
            ->get();
    }

    /**
     * @param int $rows
     * @return RaffleResult[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getFirsRows($rows = 10)
    {
        return RaffleResult::query()->where('status', '=', RaffleResult::STATUS_ACCRUED)
            ->where('money', '!=', 0)
            ->limit(10)
            ->get();
    }
}
