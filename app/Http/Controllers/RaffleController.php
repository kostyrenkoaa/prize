<?php

namespace App\Http\Controllers;

use App\Jobs\AddPrize;
use App\Services\RaffleResultServices;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class RaffleController extends Controller
{
    /**
     * Стартует воркер для розыгрыша приза
     *
     * @param RaffleResultServices $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPrize(RaffleResultServices $service)
    {
        $raffleResult = $service->createPrize(1); // Хардкод. Должны получать с фронта и проверять возможность получения
        if (empty($raffleResult->id)) {
            return response()->json(['status' => 'error']);
        }

        AddPrize::dispatch($raffleResult->user_id, $raffleResult->id);

        return response()->json([
            'status' => 'IN_QUEUE',
            'resultId' => $raffleResult->id,
        ]);
    }

    /**
     * Возвращает результат работы воркера по получению приза
     *
     * @param Request $request
     * @param RaffleResultServices $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResult(Request $request, RaffleResultServices $service)
    {
        $resultId = $request->get('resultId');
        $raffleResult = $service->getResult($resultId, Auth::id()); //todo убрать зависимость
        if (!empty($raffleResult['error'])) {
            return response()->json($raffleResult, 500);
        }

        return response()->json($raffleResult);
    }

    /**
     * Выбор пользователя после получения денег или приза
     *
     * @param Request $request
     * @param RaffleResultServices $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptPrize(Request $request, RaffleResultServices $service)
    {
        $resultId = $request->get('resultId');
        $status = $request->get('status');
        $response = $service->acceptPrize($status, $resultId, Auth::id()); //todo убрать зависимость
        return response()->json($response);
    }
}
