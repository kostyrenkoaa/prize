<?php

namespace App\Http\Controllers;

use App\Jobs\AddPrize;
use App\Services\RaffleResultServices;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class RaffleController extends Controller
{
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

    public function getResult(Request $request, RaffleResultServices $service)
    {
        $resultId = $request->get('resultId');
        $raffleResult = $service->getResult($resultId, Auth::id());
        if (!empty($raffleResult['error'])) {
            return response()->json($raffleResult, 500);
        }

        return response()->json($raffleResult);
    }

    public function acceptPrize(Request $request, RaffleResultServices $service)
    {
        $resultId = $request->get('resultId');
        $status = $request->get('status');
        $response = $service->acceptPrize($status, $resultId, Auth::id());
        return response()->json($response);
    }
}
