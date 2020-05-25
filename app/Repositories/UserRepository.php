<?php

namespace App\Repositories;

use App\User;

class UserRepository
{
    /**
     * @param $id
     * @return User|null
     */
    public function find($id): ?User
    {
        return User::query()->find($id);
    }

    public function addBalls($id, $countBalls)
    {
        User::query()->find($id)->increment('balls', $countBalls);
    }
}
