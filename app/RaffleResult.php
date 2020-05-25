<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RaffleResult
 * @package App
 *
 * @property int id
 * @property int raffle_id
 * @property int user_id
 * @property int balls
 * @property int money
 * @property string prize
 * @property string status
 */
class RaffleResult extends Model
{
    const STATUS_IN_QUEUE = 'В очереди';
    const STATUS_PENDING_DECISION = 'Ожидает решения'; //Есть результат, ждем решения пользователя
    const STATUS_TRANSLATED_TO_BALLS = 'Переведено в балы'; //Решение. Переведено в баллы
    const STATUS_REFUSAL_PRIZE = 'Отказ от приза'; //Решение. Отказ от приза
    const STATUS_AWAITING_SUBMISSION = 'Ожидает отправки'; //Решение. Приз. Ожидает отправки
    const STATUS_SENT = 'Отправлено'; //Приз. Отправлено
    const STATUS_ACCRUED = 'Начислено'; //Решение. Забирает себе деньги
    const STATUS_ENROLLED = 'Зачислено'; //Деньги отправлены в банк

    public function getAllStatuses()
    {
        return [
            static::STATUS_IN_QUEUE, static::STATUS_PENDING_DECISION, static::STATUS_TRANSLATED_TO_BALLS,
            static::STATUS_REFUSAL_PRIZE, static::STATUS_AWAITING_SUBMISSION, static::STATUS_SENT,
            static::STATUS_ACCRUED, static::STATUS_ENROLLED, ];
    }
}
