<?php
///////////////////////////////////////
// サーチコントローラー
///////////////////////////////////////

include_once '../config.php';
include_once '../util.php';

//ツイートモデルを読み込み
include_once '../Models/tweets.php';

//ログインチェック
$user = getUserSession();
if (!$user) {
    // ログインしていない
    header('Location: ' . HOME_URL . 'Controllers/sign-in.php');  
    exit;  
}

// 検索キーワード
$keyword = null;
if(isset($_GET['keyword'])){
    $keyword = $_GET['keyword']; 
}


//表示用の変数
$view_user = $user;
$view_keyword = $keyword;

$view_tweets = findTweets($user,$keyword); 


//画面表示
include_once '../Views/search.php'; 