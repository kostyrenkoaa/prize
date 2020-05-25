<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAccount
 * @package App
 *
 *
 * @property int user_id
 * @property string bank_id
 * @property string number
 */
class UserAccount extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'bank_id', 'number',
    ];
}
