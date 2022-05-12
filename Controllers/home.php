<?php
///////////////////////////////////////
// ホームコントローラー
///////////////////////////////////////

// 設定を読み込み
include_once '../config.php';
// 便利な関数を読み込み
include_once '../util.php';

//ツイートデータ操作モデルを読み込む
include_once '../Models/tweets.php';

//フォローデータ操作モデルを読み込む
include_once '../Models/follows.php';

// ログインチェック
$user = getUserSession();
if(!$user){
    //ログインしていない
    header('Location: '.HOME_URL.'Controllers/sign-in.php'); 
    exit;
}

//自分がフォローしているID一覧
$following_user_ids = findFollowingUserIds($user['id']); 

//自分のツイートも表示するために自分のIDも追加
$following_user_ids[]=$user['id'];   

//表示用変数
$view_user = $user;   

//ツイート一覧
$view_tweets = findTweets($user,null,$following_user_ids); 
//     [
//         'user_id' => 1,
//         'user_name' => 'taro',
//         'user_nickname' => '太郎',
//         'user_image_name' => 'sample-person.jpg',
//         'tweet_body' => '今プログラミングをしています。',
//         'tweet_image_name' => null,
//         'tweet_created_at' => '2021-03-15 14:00:00',
//         'like_id' => null,
//         'like_count' => 0,
//     ],
//     [
//         'user_id' => 2,
//         'user_name' => 'jiro',
//         'user_nickname' => '次郎',
//         'user_image_name' => null,
//         'tweet_body' => 'コワーキングスペースをオープンしました！',
//         'tweet_image_name' => 'sample-post.jpg',
//         'tweet_created_at' => '2021-03-14 14:00:00',
//         'like_id' => 1,
//         'like_count' => 1,
//     ],
// ];
// 画面表示
include_once '../Views/home.php';  