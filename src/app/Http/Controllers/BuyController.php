<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Log;
use App\User;
use App\Good;
use App\History;
use App\Coupon;

class BuyController extends Controller
{
    /**
     * 購入画面のViewを返す
     *
     * @param  mixed $request
     * @param  int $id 商品のID
     * @return \Illuminate\View\View 購入画面のView
     */
    public function index(Request $request, $id)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return redirect('login');
        }
        $session = Redis::get($sessionId);
        if (!$session) {
            return redirect('login');
        }
        return view('buy', ["goodId" => $id]);
    }

    /**
     * 購入金額を計算する(このロジックは正しいものとしてください)
     *
     * @param  int $price 商品の元の価格
     * @param  int $count 購入個数
     * @param  int $discount 割引率
     * @return int 合計金額
     */
    private function calc($price, $count, $discount)
    {
        return ceil($price * $count * (100 - $discount) / 100);
    }

    /**
     * 商品を購入する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {status: 購入是非, balance: 購入後のユーザーの残高, price: 購入合計価格}
     */
    public function buy(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $sessionId = $request->cookie('session_id');
            if (is_null($sessionId)) {
                return redirect('login');
            }
            $session_json = Redis::get($sessionId);
            if (!$session_json) {
                return redirect('login');
            }
            $session = json_decode($session_json);
            $id_session_store = $session->{'userid'};

            $userId = $request->input('id');
            if ($userId != $id_session_store) {
                return response()->json(['status' => false, 'message' => 'ユーザIDが不正です']);
            }

            $count = $request->input('count');
            if ($count <= 0) {
                return response()->json(['status' => false, 'message' => '負数を入力はできません']);
            }

            $coupon = new Coupon();
            $discount = $request->input('discount');
            if ($discount != 0 and !$coupon->hasCoupon($discount)) {
                return response()->json(['status' => false, 'message' => 'クーポンの入力に異常があります']);
            }

            $goodId = $request->input('good_id');
            $user = User::where('id', $userId)->firstOrFail();
            $good = Good::where('id', $goodId)->firstOrFail();
            $price = $this->calc($good->price, $count, $discount);
            if ($user->balance < $price) {
                return response()->json(['status' => false, 'message' => '残高が不足しています']);
            }
            $user->balance -= $price;
            $user->save();
            $hisotry = History::create(['user_id' => $userId, 'good_id' => $goodId, 'count' => $count, 'price' => $price]);
            $hisotry->save();
            $user->save();
            return response()->json(['status' => true, 'balance' => $user->balance, 'price' => $price]);
        });
    }

    /**
     * dryBuy 商品の合計金額を計算する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse { price: 購入合計価格 }
     */
    public function dryBuy(Request $request)
    {
        $goodId = $request->input('good_id');
        $count = $request->input('count');
        $discount = $request->input('discount');
        $good = Good::where('id', $goodId)->firstOrFail();
        $price = $this->calc($good->price, $count, $discount);
        return response()->json(['price' => $price]);
    }
}
