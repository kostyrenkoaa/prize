<?php

namespace App\Http\Controllers;

use App\Services\RaffleService;

class HomeController extends Controller
{
    public function index(RaffleService $service)
    {
        $dateInRaffle = json_encode($service->getDateInRaffle(1));
        return view('home', compact('dateInRaffle'));
    }
}
