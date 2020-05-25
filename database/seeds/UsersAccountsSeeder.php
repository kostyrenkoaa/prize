<?php

use \App\User;
use \App\UserAccount;
use Illuminate\Database\Seeder;
use \Faker\Factory;

class UsersAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserAccount::query()->insert($this->getData());
    }

    protected function getData()
    {
        $faker = Factory::create();

        $data = [];
        foreach (User::all() as $userData) {
            $data[] = [
                'user_id' => $userData->id,
                'bank_id' => $faker->swiftBicNumber,
                'number' => $faker->bankAccountNumber,
            ];
        }

        return $data;
    }
}
