<?php

namespace App\Http\Controllers;

use App\User;
use App\Article;
use App\Good;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Facades\Redis;

class TopController extends Controller
{    
    /**
     * トップ画面のViewを返す
     *
     * @param  mixed $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sessionId = $request->cookie('session_id');

        if(is_null($sessionId)){
            return redirect('login');
        }
        $session = Redis::get($sessionId);
        if(!$session){
            return redirect('login');
        }

        return view('top');
    }
}
