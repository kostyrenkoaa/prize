<?php

namespace Tests\Unit;

use App\RaffleResult;
use App\Repositories\RaffleRepository;
use App\Repositories\RaffleResultRepository;
use App\Repositories\UserRepository;
use App\Services\RaffleResultServices;
use App\Services\RaffleService;
use PHPUnit\Framework\TestCase;

class RaffleResultServicesTest extends TestCase
{

    /**
     * @dataProvider getDataForTestGetCountBallsForChangeStatus
     *
     * @param $money
     * @param $expected
     * @throws \ReflectionException
     */
    public function testGetCountBallsForChangeStatus($money, $expected)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getMockBuilder(UserRepository::class)
            ->getMock();
        /** @var RaffleResultRepository $raffleResultRepository */
        $raffleResultRepository = $this->getMockBuilder(RaffleResultRepository::class)
            ->getMock();
        /** @var RaffleService $raffleService */
        $raffleService = $this->getMockBuilder(RaffleService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $raffleResultServices = new RaffleResultServices($raffleService, $raffleResultRepository, $userRepository);

        $raffleResult = new RaffleResult();
        $raffleResult->money = $money;
        $status = RaffleResult::STATUS_TRANSLATED_TO_BALLS;

        $method = new \ReflectionMethod($raffleResultServices, 'getCountBallsForChangeStatus');
        $method->setAccessible(true);
        $result = $method->invoke($raffleResultServices, $raffleResult, $status);

        $this->assertEquals($expected, $result);
    }

    public function getDataForTestGetCountBallsForChangeStatus()
    {
        return [
            [10, 20],
            [11, 22],
            [10000, 20000],
        ];
    }
}
