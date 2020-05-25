<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use \App\User;
use \Faker\Factory;

class UsersSeeder extends Seeder
{
    const USER_DATA = [
        [
            'email' => 'admin@ro.ru',
            'password' => '12345678',
            'is_admin' => '1',
        ],
        [
            'email' => 'user@ro.ru',
            'password' => '12345678',
            'is_admin' => '0',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->insert($this->getData());
    }

    protected function getData()
    {
        $faker = Factory::create('ru_RU');

        $data = [];
        foreach (static::USER_DATA as $userData) {
            $data[] = [
                'name' => $faker->name,
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'is_admin' => $userData['is_admin'],
                'address' => $faker->address
            ];
        }

        return $data;
    }
}
