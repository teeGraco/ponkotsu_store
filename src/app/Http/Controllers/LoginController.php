<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

use Log;

class LoginController extends Controller
{
    /**
     * ログイン画面のViewを返す
     *
     * @param  mixed $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        $session = Redis::get($sessionId);
        if ($session) {
            return redirect('/');
        }
        return view('login');
    }

    /**
     * ログイン用APIユーザーとパスワードを検証する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {session_id: セッションID, admin: admin権限か否か}
     */
    public function login(Request $request)
    {
        // Log::Debug("name:" . $request->name);
        // Log::Debug("password:" . $request->password);
        try {
            $user = User::whereRaw("name = '" . $request->name . "' AND password = '" . md5($request->password) . "'")->firstOrFail();
            $sessionId = random_int(0, 2 ** 32);
            Redis::set($sessionId, json_encode(['userid' => $user->id]));
            return response()->json(['session_id' => $sessionId, 'admin' => $user->admin]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'ユーザー名またはパスワードが間違っています'], 400);
        }
        return response()->json(['message' => '何かがおかしいです。しばらく時間を開けて、再度接続してみてください。'], 500);
    }
}
