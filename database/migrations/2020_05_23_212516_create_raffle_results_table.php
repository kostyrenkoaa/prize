<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaffleResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raffle_results', function (Blueprint $table) {
            $table->id();
            $table->integer('raffle_id')
                ->comment('Определяет номер розыгрыша');
            $table->integer('user_id')
                ->comment('Определяет кто выйграл');
            $table->integer('balls')
                ->default(0)
                ->comment('Количество выйгранных балов');
            $table->integer('money')
                ->default(0)
                ->comment('Количество выйгранных денег, в базовой валюте');
            $table->string('prize')
                ->default('')
                ->comment('Приз который выйграл пользователь');
            $table->enum(
                'status',
                [
                    'В очереди', //Ожидает результата
                    'Ожидает решения', //Есть результат, ждем решения пользователя
                    'Переведено в балы', //Решение. Переведено в баллы
                    'Отказ от приза', //Решение. Отказ от приза
                    'Ожидает отправки', //Решение. Приз. Ожидает отправки
                    'Отправлено', //Приз. Отправлено
                    'Начислено', //Решение. Забирает себе деньги
                    'Зачислено' //Деньги отправлены в банк
                ]
            )->default('В очереди')
                ->comment('Определяет что выйграл');

            $table->timestamps();
            $table->index(['raffle_id', 'user_id', 'status', 'prize']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('raffle_results');
    }
}
