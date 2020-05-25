<?php

use Faker\Generator;
use Illuminate\Database\Seeder;
use \Faker\Factory;
use \App\Raffle;

class RafflesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Raffle::query()->insert($this->getData());
    }

    protected function getData()
    {
        $faker = Factory::create('ru_RU');
        return [
            'info' => $faker->title,
            'date_start' => $faker->date($format = 'Y-m-d H:i', $max = 'now'),
            'date_end' => $faker->dateTimeBetween('+1 days', '+2 days')->format('Y-m-d H:i'),
            'prizes' => $this->getPrizesData($faker),
            'prize_coef' => rand(5, 10),
            'money_count' => rand(1000, 10000),
            'money_max' => rand(100, 200),
            'money_min' => rand(201, 500),
            'money_coef' => rand(5, 10),
            'ball_coef' => rand(7, 11),
            'ball_max' => rand(10, 20),
            'ball_min' => rand(6, 10),
        ];
    }

    protected function getPrizesData(Generator $faker)
    {
        $data = [];
        $count = rand(1, 3);

        for ($i = 1; $i <= $count; $i++) {
            $data[] = [
                'name' => $faker->colorName,
                'coef' => rand(1, 100),
                'balls' => rand(10, 12),
                'count' => rand(1, 3),
            ];
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
