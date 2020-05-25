<?php

namespace App\Repositories;

use App\Raffle;

class RaffleRepository
{
    /**
     * Поиск пользователя по id
     *
     * @param $id
     * @return Raffle|null
     */
    public function find($id): ?Raffle
    {
        return Raffle::query()->find($id);
    }
}
