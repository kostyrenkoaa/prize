<?php

namespace App\Http\Controllers;

use App\Services\RaffleService;

class HomeController extends Controller
{
    /**
     * Вывод стартовой страницы приложения
     *
     * @param RaffleService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(RaffleService $service)
    {
        $dateInRaffle = json_encode($service->getDateInRaffle(1));
        return view('home', compact('dateInRaffle'));
    }
}
