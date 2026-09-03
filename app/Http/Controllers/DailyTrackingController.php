<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DailyTrackingController extends Controller
{
    public function index()
    {
        return view('operasional.daily-tracking');
    }

    public function show(string $divisi)
    {
        return view('operasional.daily-tracking-game', ['divisi' => $divisi]);
    }
}
