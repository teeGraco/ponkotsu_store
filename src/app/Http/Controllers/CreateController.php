<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Storage;
use Mail;
use Validator;
use Log;

class CreateController extends Controller
{    
    /**
     * ユーザー作成画面のViewを返す
     *
     * @param  mixed $request
     * @return \Illuminate\View\View 購入画面のView
     */
    public function index(Request $request)
    {
        return view('create');
    }
    
    /**
     * ユーザー作成API
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {}
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'リクエストが不正です'], 400);
        }

        try {
            $user = new User($request->all());
            $user->balance = 100000;
            $user->password = md5($user->password);
            $user->coupons = serialize([1]);
            $user->icon = null;
            $user->email = mb_strtolower($user->email);

            $data = [];

            if ($request->icon instanceof UploadedFile) {
                if(!$request->icon->isValid()){
                    return response()->json(['message' => 'ファイルが無効です。ファイルの容量が大きすぎる可能性があります。'], 400);
                }
                $orgFilename = $request->icon->getClientOriginalName();
                $orgFilename = pathinfo($orgFilename)['filename'] . '.' . $request->icon->extension(); # 嘘をついてるかもしれないので拡張子をつけ直す
                $filename = $request->icon->storeAs('public/users', uniqid().'_'.$orgFilename); # そのファイルが持つ本来の拡張子を最後につける
                $user->icon = pathinfo($filename)['basename'];
            }

            $user->save();

            Mail::send('emails.welcome', $data, function ($message) use ($user) {
                $message->to($user->email)->subject('ぽんこつストアにようこそ！');
            });
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['message' => 'そのメールアドレスまたはユーザー名が存在しています。'], 400);
        }

        return response()->json([]);
    }
}
