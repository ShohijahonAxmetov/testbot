<?php

namespace App\Http\Controllers\telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        dd($request->all());
    }
}
