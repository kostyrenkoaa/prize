<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(
    [
        "middleware" => ["auth"]
    ],
    function () {
        Route::get('/home', [
            'uses' => 'HomeController@index',
            'as' => 'home'
        ]);

        Route::get('/raffle', [
            'uses' => 'RaffleController@index',
            'as' => 'raffle'
        ]);

        Route::post('/api/rafles/getPrize', [
            'uses' => 'RaffleController@getPrize',
            'as' => 'raffle'
        ]);

        Route::post('/api/rafles/getResult', [
            'uses' => 'RaffleController@getResult',
            'as' => 'getResult'
        ]);

        Route::post('/api/rafles/acceptPrize', [
            'uses' => 'RaffleController@acceptPrize',
            'as' => 'acceptPrize'
        ]);
    }
);
