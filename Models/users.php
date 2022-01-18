<?php

////////////////////////
////ユーザーデータの管理
//////////////////////



function createUser(array $data) 
{
    //DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //接続エラーがある場合->処理中止
    if($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli->connect_error ."\n";

        exit;
    }

    //新規登録のSQLクエリを作成する
    $query ='INSERT INTO users (email,name,nickname,password) VALUES (?,?,?,?)';

    //プリペアードステートメントに作成したクエリを登録
    $statement =$mysqli->prepare($query);

    //パスワードをハッシュ値に変換する
    $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);

    //クエリのプレースホルダ（?の部分）にカラム値を紐付け
    $statement ->bind_param('ssss',$data['email'],$data['name'],$data['nickname'],$data['password']);

    //クエリを実行
    $response =$statement->execute();

    //実行に失敗した場合
    if($response === false){
        echo 'エラーメッセージ:'.$mysqli->error."\n";
    }

    $statement->close();
    $mysqli->close();

    return $response;   
}


/////////　ユーザー情報を取得（ログインチェック）の関数

function findUserAndCheckPassword(string $email , string $password):array
{
    //DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //接続エラーがある場合->処理中止
    if($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli->connect_error ."\n";

        exit;
    }

    // 入力値をエスケープ
    $email = $mysqli->real_escape_string($email);

    //SQLクエリを作成
    //外部からのリクエストは何が入ってくるかわからないため、必ず、エスケープしたものをクオートで囲む  
    $query ='SELECT * FROM users WHERE email ="'.$email.'"';

    //クエリ実行
    $result = $mysqli->query($query);

    //クエリ実行に失敗した時->return
    if(!$result){
        echo "エラーメッセージ:".$mysqli->error."\n";
        $mysqli->close();
        return false;
    }

    //ユーザー情報を取得
    $user =$result->fetch_array(MYSQLI_ASSOC); 
    
    


    //ユーザー情報が存在しない時->return
    if(!$user){
        $mysqli->close();
        return false;
    }

    //パスワードの不一致->return
    if(!password_verify($password,$user['password'])){
        $mysqli->close();
        return false;
    }
    $mysqli->close();

    return $user;

}

//ユーザー情報を1件取得
function findUser(int $user_id,int $login_user_id=null)
{
    //DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    //接続エラーがある場合->処理中止
    if($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli->connect_error ."\n"; 

        exit;
    }

    //エスケープ
    $user_id = $mysqli->real_escape_string($user_id); //表示対象のユーザーID
    $login_user_id = $mysqli->real_escape_string($login_user_id); //今ログインしているID　 

    //SQLクエリを作成（検索）
    $query = <<<SQL
        SELECT
            U.id,
            U.name,
            U.nickname,
            U.email,
            U.image_name,
            -- フォロー中の数
            (SELECT COUNT(1) FROM follows WHERE status = 'active' AND follow_user_id = U.id) AS follow_user_count,
            -- フォローワーの数
            (SELECT COUNT(1) FROM follows WHERe status = 'active' AND followed_user_id = U.id) AS followed_user_count,
            -- ログインユーザーがフォローしている場合、フォローIDが入る
            F.id AS follow_id
        FROM
            users AS U
            LEFT JOIN
                follows AS F ON F.status = 'active' AND F.followed_user_id = '$user_id' AND F.follow_user_id = '$login_user_id'
        WHERE
            U.status = 'active' AND U.id = '$user_id'
    SQL;
        
        

    

    //クエリの実行
    $result = $mysqli->query($query);
    if($result){
        $response = $result->fetch_array(MYSQLI_ASSOC);
    }else{
        $response = false;
        echo 'エラーメッセージ:'.$mysqli->error."\n";
    }

    $mysqli->close();

    return $response;

}


function updateUser(array $data)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
 
    // 更新日時を保存データに追加
    $data['updated_at'] = date('Y-m-d H:i:s');
 
    // パスワードがある場合->ハッシュ値に変換
    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
 
    // ------------------------------------
    // SQLクエリを作成（更新）
    // ------------------------------------
    // SET句のカラムを準備
    $set_columns = [];
    foreach ([
        'name', 'nickname', 'email', 'password', 'image_name', 'updated_at'
    ] as $column) {
        // 入力があれば、更新の対象にする
        if (isset($data[$column]) && $data[$column] !== '') {
            $set_columns[] = $column . ' = "' . $mysqli->real_escape_string($data[$column]) . '"';
        }
    }
 
    // クエリ組み立て
    $query = 'UPDATE users SET ' . join(',', $set_columns);
    $query .= ' WHERE id = "' . $mysqli->real_escape_string($data['id']) . '"';
 
    // ------------------------------------
    // 戻り値を作成
    // ------------------------------------
    // クエリを実行
    $response = $mysqli->query($query);
 
    // SQLエラーの場合->エラー表示
    if ($response === false) {
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
 
    // ------------------------------------
    // 後処理
    // ------------------------------------
    // DB接続を開放
    $mysqli->close();
 
    return $response;
}