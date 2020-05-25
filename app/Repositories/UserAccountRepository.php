<?php

namespace App\Repositories;


use App\UserAccount;

class UserAccountRepository
{
    /**
     * @param $id
     * @return UserAccount|null
     */
    public function find($id): ?UserAccount
    {
        return UserAccount::query()->find($id);
    }
}
