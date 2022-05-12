<?php

//設定読み込み
include_once '../config.php';
//便利な関数の読み込み
include_once '../util.php';

//ユーザーモデルの読み込み
include_once '../Models/users.php';

//ツイートモデルの読み込み
include_once '../Models/tweets.php';  

// ------------------------------------
// ログインチェック
// ------------------------------------
$user = getUserSession();
if (!$user) {
    // ログインしていない
    header('Location:' . HOME_URL . 'Controllers/sign-in.php');   
    exit;
}

//ユーザー情報の変更(ログインしているユーザー)
//ニックネームとユーザー名とメールアドレスが入力されている場合 
if(isset($_POST['nickname'])&&isset($_POST['name'])&&isset($_POST['email'])){
    $data =[
        'id'=>$user['id'], 
        'nickname'=>$_POST['nickname'],
        'name'=>$_POST['name'],
        'email'=>$_POST['email']
    ];
    //パスワードが入力されている場合
    if(isset($_POST['password']) && $_POST['password']!==''){
        $data['password'] = $_POST['password'];
    }
    //ファイルがアップロードされていた場合->画像をアップロード
    if(isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])){ 
        $data['image_name'] = uploadImage($user,$_FILES['image'],'user');  
    }
    //更新を実行し、成功した場合
    if(updateUser($data)){
        //更新後のユーザー情報をセッションに保存する  
        $user = findUser($user['id']);
        saveUserSession($user);
        //リロード
        header('Location:'.HOME_URL.'Controllers/profile.php'); 
        exit;
    }

}

//表示するユーザーIDを取得(デフォルトはログインユーザーなので1行目に定義する)
$requestedUserId = $user['id']; //初期値としてログインユーザーのIDを定義 
if(isset($_GET['user_id'])){    
    $requestedUserId =$_GET['user_id'];  
}




//表示用の変数(ユーザー情報)
$view_user = $user;
//表示用の変数(プロフィール詳細)　第一引数に表示するユーザーのIDを入れて、第二引数にはログインしている自分のユーザーIDを入れる。
//ログインしているユーザーが表示対象のユーザーをフォローしているかどうかを判断するために、第二引数に入れている。
//第一引数がログイン中の自分のIDの場合は第二引数はnullとなる。

$view_requested_user = findUser($requestedUserId,$user['id']);   

//ツイート一覧
$view_tweets = findTweets($user,null,[$requestedUserId]); 



//画面表示
include_once '../Views/profile.php';
