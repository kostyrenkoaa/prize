<?php

namespace App\Console\Commands;

use App\Repositories\RaffleResultRepository;
use App\Services\SendMoneyServices;
use Illuminate\Console\Command;

class SendMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'money:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправка денег';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(SendMoneyServices $services)
    {
        $url = env('URL_WEB_HOOK', '');
        $countResult = $services->send($url);
        echo PHP_EOL . 'Удачно выполненных запросов: ' . $countResult . PHP_EOL;
    }
}
