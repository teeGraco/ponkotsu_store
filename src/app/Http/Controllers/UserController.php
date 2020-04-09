<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\History;
use App\Coupon;
use Illuminate\Support\Facades\Redis;
use Log;

class UserController extends Controller
{    
    /**
     * ユーザーページのviewを返す
     *
     * @param  mixed $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('user');
    }
    
    /**
     * ユーザー一覧情報を返す(管理者限定)
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {ユーザー一覧}
     */
    public function all(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $session = Redis::get($sessionId);
        if (!$session) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $userId = json_decode($session)->userid;
        $user = User::where('id', $userId)->firstOrFail();
        if (!$user->admin) {
            return response()->json(['message' => '管理者権限が必要です'], 403);
        }

        $users = User::all();
        return response()->json($users);
    }
    
    /**
     * ログインしているユーザー自身の情報を返す
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {ユーザー情報}
     */
    public function get(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $session = Redis::get($sessionId);
        if (!$session) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $userId = json_decode($session)->userid;
        $reqId = null;
        if ($request->has('id')) {
            $reqId = intval($request->id);
        } else {
            $reqId = $userId;
        }

        $user = User::where('id', $reqId)->firstOrFail();
        $coupons = unserialize($user->coupons);
        $couponDetail = Coupon::select('id', 'discount', 'description')->wherein('id', $coupons)->get();
        return response()->json(["id" => $user->id, "email" => $user->email, "name" => $user->name, "balance" => $user->balance, "coupons" => $coupons, "couponDetails" => $couponDetail, "admin" => $user->admin, "icon" => $user->icon]);
    }
    
    /**
     * ユーザーの購入履歴を返す
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {ユーザーの購入履歴}
     */
    public function getPurchaseHistory(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $session = Redis::get($sessionId);
        if (!$session) {
            return response()->json(['message' => 'ログインが必要です'], 403);
        }
        $userId = json_decode($session)->userid;
        $user = User::where('id', $userId)->firstOrFail();
        $history = History::where('user_id', $user->id)->leftJoin('goods', 'histories.good_id', '=', 'goods.id')->select('histories.id', 'goods.title', 'histories.good_id', 'histories.updated_at', 'histories.price', 'histories.count')->get();
        return response()->json(["history" => $history]);
    }
}
