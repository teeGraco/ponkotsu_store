<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Log;
use Illuminate\Support\Str;
use App\User;
use Exception;
use Mail;
use Validator;

class ForgetPasswordController extends Controller
{    
    /**
     * パスワードを忘れたViewを返す
     *
     * @return \Illuminate\View\View パスワードを忘れたView
     */
    public function index()
    {
        return view('forget');
    }

    /**
     * ユーザーIDを暗号化してメールに送信する    
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {}
     */
    public function sendToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email', 
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'リクエストが不正です'], 400);
        }

        $email = mb_strtolower($request->input('email'));
        if (User::where('email', $email)->exists()) {
            $user = User::where('email', $email)->firstOrFail();
            // 仕様: userIDは10進数15桁以内である
            $plaintext = str_pad($user->id, 15, 0, STR_PAD_LEFT);
            $key = config('app.key');
            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }
            // 2^128/10^15 = 3*10^23 だから適当なトークンが一致してしまうことは考えにくい
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
            $ciphertext = base64_encode($iv . $ciphertext_raw);
            Mail::send(array('text' => 'emails.forget'), ['token' => $ciphertext, 'name' => $user->name], function ($message) use ($user) {
                $message->to($user->email)->subject('パスワード再発行');
            });
            return response()->json([], 200);
        }
        return response()->json([], 200); # ユーザーのメールアドレスは個人情報なので存在是非に関わらず200を返す
    }
    
    /**
     * トークンからユーザーIDを抽出する
     *
     * @param  string $token
     * @return int ユーザーID (トークンが異常な時: -1)
     */
    public function getIdFromToken($token)
    {
        $ciphertext = base64_decode($token);
        $iv = substr($ciphertext, 0, 16);
        $ciphertext_raw = substr($ciphertext, 16);
        $key = config('app.key');
        if (Str::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        try {
            $plaintext = openssl_decrypt($ciphertext_raw, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
        } catch (Exception $e) {
            return -1;
        }
        if (ctype_digit($plaintext)) {
            $id = intval($plaintext);
            if (User::where('id', $id)->exists()) {
                return $id;
            }
        }
        return -1;
    }
    
    /**
     * トークンが正しいものかどうか確認する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {}
     */
    public function validateToken(Request $request)
    {
        $token = $request->input('token');
        if ($this->getIdFromToken($token) < 0) {
            return response()->json(['message' => '何かがおかしいようです もう一度トークンを入力してみてください'], 400);
        } else {
            return response()->json([]);
        }
    }
    
    /**
     * トークンを検証し、パスワードを再設定する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {}
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required', 
            'password' => 'required', 
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'リクエストが不正です'], 400);
        }

        $token = $request->input('token');
        $id = $this->getIdFromToken($token);
        if ($id < 0){
            return response()->json(['message' => '何かがおかしいようです もう一度やり直してみてください'], 400);
        } 
        DB::transaction(function () use ($id, $request) {
            $user = User::where('id', $id)->firstOrFail();
            $user->password = md5($request->password);
            $user->save();
        });
        return response()->json([]);
    }
}
