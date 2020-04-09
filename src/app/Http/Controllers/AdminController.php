<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Redis;

class AdminController extends Controller
{
    
    /**
     * index 管理者画面のviewを返す
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
        $id = json_decode($session)->userid;
        $user = User::where('id', $id)->firstOrFail();
        if(!$user->admin) {
            return redirect('login');
        }
        $users = User::all();
        return view('admin', ['users' => $users]);
    }
}
