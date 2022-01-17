<?php
////////////////////////
///サインインコントローラー
///////////////////////

//設定を読み込み
include_once('../config.php');
//便利な関数
include_once('../util.php');

include_once('../Models/users.php');

//ログイン結果
$try_login_result = null;

//メールアドレスとパスワードが入力されている場合
if(isset($_POST['email'])&&isset($_POST['password'])){  
    //ログインチェック実行
    $user =findUserAndCheckPassword($_POST['email'],$_POST['password']);  

    //ログインに成功した場合
    if($user){
        //ユーザーをセッションに保存
        saveUserSession($user);
        //ホーム画面に遷移
        header('Location: '.HOME_URL.'Controllers/home.php');   
        exit;
    }else{
        //ログイン結果を失敗にする
        $try_login_result = false;

    }

    
    
}




//表示用の変数
$view_try_login_result = $try_login_result;


// 画面
include_once ('../Views/sign-in.php');

