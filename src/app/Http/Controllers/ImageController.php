<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Log;
use Storage;

class ImageController extends Controller
{    
    /**
     * 画像を取得する
     *
     * @param  mixed $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse 画像のバイナリファイル
     */
    public function index(Request $request){
        $path = public_path() . "/storage/". $request->input('file');

        if(!exif_imagetype($path)){
            return response()->json(['message' => '引数が不正です'], 400);
        }

        if(!file_exists($path)){
            return response()->json(['message' => 'ファイルが存在しません'], 404);
        }
        if($request->has('x') && $request->has('y')){
            $x = $request->x;
            $y = $request->y;
            if($x <= 0 || $x > 2048 || $y <= 0 || $y > 2048){
                return response()->json(['message' => 'ファイルサイズが大きすぎます'], 404);
            }
            $tmpPath = public_path() . "/storage/tmp/".uniqid().'.png';
            $cmd = escapeshellcmd('convert '.$path.' -resize '.$x.'x'.$y.'! '.$tmpPath);
            exec($cmd);
            return response()->file($tmpPath);
        }
        return response()->file($path);
    }
}
