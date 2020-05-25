<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRafflesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raffles', function (Blueprint $table) {
            $table->id();
            $table->string('info')
                ->comment('Содержит информацию о розаграше');
            $table->dateTime('date_start')
                ->comment('Определяет старт розыграша');
            $table->dateTime('date_end')
                ->comment('Определяет окончание розыграша');
            $table->json('prizes')
                ->comment('Содержит массив объектов с ключами name, coef, balls, count');
            $table->integer('prize_coef')
                ->comment('Коефициет выпадания приза');
            $table->integer('money_count')
                ->comment('Сумма денег в розыграше');
            $table->integer('money_max')
                ->comment('Максимальная сумма выйграша одного человека');
            $table->integer('money_min')
                ->comment('Минимальная сумма выйграша одного человека');
            $table->integer('money_coef')
                ->comment('Коефициет выпадания денег');
            $table->integer('ball_max')
                ->comment('Максимальная сумма баллов одного человека');
            $table->integer('ball_min')
                ->comment('Минимальная сумма баллов одного человека');
            $table->integer('ball_coef')
                ->comment('Коефициет выпадания баллов');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raffles');
    }
}
