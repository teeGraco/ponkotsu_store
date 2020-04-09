<?php

use Illuminate\Database\Seeder;

class ReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reviews = [
            [
                'good_id' => 1,
                'user_id' => 2,
                'message' => '従来の釘を使った筆記とは違い、とても書きやすいです！',
                'rating' => 5,
            ],
            [
                'good_id' => 1,
                'user_id' => 1,
                'message' => 'コンセプトはよいのですが、黒鉛が消耗したときに削るのが面倒です。自動化できないでしょうか？',
                'rating' => 4,
            ],
            [
                'good_id' => 1,
                'user_id' => 1,
                'message' => 'ところでこのページってphpで動いてるんですか？怖いなぁ…',
                'rating' => 4,
            ],
            [
                'good_id' => 2,
                'user_id' => 1,
                'message' => '画像のアスペクト比がおかしいです。どうやってるんですか？御札はありがたく飾らせていただいてます。',
                'rating' => 1,
            ],
            [
                'good_id' => 7,
                'user_id' => 2,
                'message' => '買ったはいいものの、吹いても息が通り抜けるだけで音が出ませんでした。故障しているじゃないですか？',
                'rating' => 1,
            ],
            [
                'good_id' => 11,
                'user_id' => 2,
                'message' => 'この人はCTFで暗号が自称強いらしいです。',
                'rating' => 3,
            ],
        ];
        foreach ($reviews as $review) {
            \App\Review::create($review);
        }
    }
}
