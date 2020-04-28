<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Good;
use \App\Review;
use \App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Log;
use Validator;
use DOMDocument;
use DOMXPath;
use Image;

class GoodsController extends Controller
{
    /**
     * 商品の詳細画面のViewを返す
     *
     * @param  mixed $request
     * @param  int $id 商品ID
     * @return \Illuminate\View\View
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

        $good = Good::where('id', $id)->firstOrFail();
        return view('goods', ['id' => $good->id]);
    }

    /**
     * すべての商品の情報を返す
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {商品のリスト}
     */
    public function all(Request $request)
    {
        $goods = DB::table('goods')->select('id', 'thumbnail', 'price', 'title', 'description')->get();
        return response()->json(['goods' => $goods]);
    }

    /**
     * 商品からkeywordを含む文字列を検索し、ord順に並び替える:
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {商品のリスト}
     */
    public function search(Request $request)
    {
        $goods = Good::where("title", "LIKE", "%" . $request->keyword . "%")->select('id', 'thumbnail', 'price', 'title', 'description')->orderByRaw("price " . $request->ord)->get();
        return response()->json(['goods' => $goods]);
    }
    
    /**
     * postReview 商品に対してレビューを投稿する
     *
     * @param  mixed $request
     * @return \Illuminate\Http\JsonResponse {}
     */
    public function postReview(Request $request)
    {
        $sessionId = $request->cookie('session_id');
        if (is_null($sessionId)) {
            return response()->json(['ログインが必要です'], 403);
        }
        $session = Redis::get($sessionId);
        if (!$session) {
            return response()->json(['ログインが必要です'], 403);
        }

        $validator = Validator::make($request->all(), [
            'good_id' => 'required|integer',
            'message' => 'required',
            'rating' => 'required|integer|min:0|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'リクエストが不正です'], 400);
        }

        $userId = json_decode($session)->userid;
        $goodId = $request->input('good_id');
        $message = htmlspecialchars($request->input('message'), ENT_QUOTES);
        $rating = $request->input('rating');

        if (!Good::where('id', $goodId)->exists()) {
            return response()->json(['message' => '商品が存在しません'], 404);
        }

        $review = Review::create(['good_id' => $goodId, 'user_id' => $userId, 'message' => $message, 'rating' => $rating]);
        $review->save();

        return response()->json([]);
    }
    
    /**
     * 商品の詳細情報を返す(URLが含まれている場合、OpenGraphprotocolを解決し、画像を埋め込む)
     *
     * @param  mixed $request
     * @param  mixed $id 商品ID
     * @return \Illuminate\Http\JsonResponse {商品の詳細情報}
     */
    public function detail(Request $request, $id)
    {
        $good = Good::where('id', $id)->select('id', 'thumbnail', 'price', 'title', 'description')->firstOrFail();
        $reviews = Review::select(['name','message','icon','rating','reviews.updated_at'])->where('good_id', $good->id)->leftJoin('users', 'reviews.user_id', '=', 'users.id')->orderBy('reviews.updated_at', 'desc')->get();        
        foreach ($reviews as $review) {
            if (preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $review->message, $result) !== false) {
                foreach ($result[0] as $url) {
                    # URLの存在確認
                    $response = @file_get_contents($url, NULL, NULL, 0, 1);
                    if ($response === false) {
                        break;
                    }

                    $doc = new DOMDocument;
                    libxml_use_internal_errors(true);
                    $doc->loadHTMLFile($url); // https://pgmemo.tokyo/data/archives/1569.html エラーを無視する
                    libxml_clear_errors();
                    $xpath = new DOMXPath($doc);
                    $res = $xpath->query('//meta[@property="og:image"]/@content');
                    if (count($res) == 0) {
                        continue;
                    }
                    $imageURL = $res[0]->value;
                    $b64image = base64_encode(file_get_contents($imageURL));
                    $review['ogImageUrl'] = $b64image;
                    break;
                }
            }
        }
        return response()->json(['id' => $good->id, 'title' => $good->title, 'desc' => $good->description, 'thumbnail' => $good->thumbnail, 'price' => $good->price, 'reviews' => $reviews]);
    }
}
