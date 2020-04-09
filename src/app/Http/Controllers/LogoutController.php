<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LogoutController extends Controller
{    
    /**
     * ログアウトする
     *
     * @param  mixed $request
     * @return void
     */
    public function index(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return redirect('login');
        }
        Redis::del($sessionId);
        return redirect('login');
    }
}
