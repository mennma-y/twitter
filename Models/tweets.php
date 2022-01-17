<?php
////////////////////////
// ツイートデータを処理
/////////////////////

function createTweet(array $data)
{ 
    //DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 

    //接続エラーがある場合->処理停止
    if($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli->connect_error."\n";
        exit;
    }

    //新登録のSQLクエリを作成する
    $query = 'INSERT INTO tweets (user_id, body, image_name) VALUE(?,?,?)';

    //プリペアードステートメントにクエリを登録する
    $statement = $mysqli->prepare($query);

    //プレースホルダにカラム値を紐付け
    $statement->bind_param('iss', $data['user_id'], $data['body'], $data['image_name']);

    // クエリ実行
    $response = $statement->execute();

    if($response === false){
        echo 'エラーメッセージ:'.$mysqli->error."\n";
    }

    //DB解放
    $statement->close();
    $mysqli->close();

    return $response;  


}

