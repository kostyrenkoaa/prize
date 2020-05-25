<?php

namespace App\Jobs;

use App\Services\RaffleResultServices;
use App\Services\RaffleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddPrize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $resultId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $resultId)
    {
        $this->userId = $userId;
        $this->resultId = $resultId;
    }

    /**
     * Execute the job.
     *
     * @param RaffleResultServices $services
     * @return void
     */
    public function handle(RaffleResultServices $services)
    {
        try {
            $services->addPrize($this->userId, $this->resultId);
        } catch (\Exception $exception) {
            file_put_contents('LogErrors', $exception->getMessage(), FILE_APPEND);
        }
    }
}
