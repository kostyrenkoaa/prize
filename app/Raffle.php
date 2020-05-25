<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Raffle
 * @package App
 *
 * @property int id
 * @property string info
 * @property string date_start
 * @property string date_end
 * @property string prizes
 * @property int prize_coef
 * @property int money_count
 * @property int money_max
 * @property int money_min
 * @property int money_coef
 * @property int ball_max
 * @property int ball_min
 * @property int ball_coef
 */
class Raffle extends Model
{
    public $timestamps = false;

    public function results()
    {
        $this->hasMany(RaffleResult::class, 'raffle_id');
    }
}
