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

// ツイート一覧を取得  useridsはユーザーID一覧
function findTweets(array $user, string $keyword = null, array $user_ids = null)
{
    //DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);   

    //接続エラーがある場合->処理停止
    if($mysqli->connect_errno){
        echo 'MySQLの接続に失敗しました。:'.$mysqli->connect_error."\n";
        exit;
    }

    // ログインユーザーIDをエスケープする
    $login_user_id = $mysqli->real_escape_string($user['id']);     

    // 検索のSQLクエリを作成
    
    $query = <<<SQL
        SELECT
            T.id AS tweet_id,
            T.status AS tweet_status,
            T.body AS tweet_body,
            T.image_name AS tweet_image_name,
            T.created_at AS tweet_created_at,
            U.id AS user_id,
            U.name AS user_name,
            U.nickname AS user_nickname,
            U.image_name AS user_image_name,
            L.id AS like_id,
            (SELECT COUNT(*) FROM likes WHERE status = 'active' AND tweet_id = T.id) AS like_count
        FROM
            tweets AS T
            JOIN
            users AS U ON U.id = T.user_id AND U.status = 'active'
            LEFT JOIN
            likes AS L ON L.tweet_id = T.id AND L.status = 'active' AND L.user_id = '$login_user_id'
        WHERE
            T.status = 'active'
    SQL;

    //検索キーワードが入力されていた場合
    if(isset($keyword)){
        //エスケープ
        $keyword =$mysqli->real_escape_string($keyword);

        //ツイート主のニックネーム、ユーザー名、本文から部分一致検索
        $query .= ' AND CONCAT(U.nickname, U.name, T.body) LIKE "%'. $keyword .'%"'; 
    }

     // ユーザーIDが指定されている場合
     if (isset($user_ids)) {
        foreach ($user_ids as $key => $user_id) {
            $user_ids[$key] = $mysqli->real_escape_string($user_id);
        }
        $user_ids_csv = '"' . join('","', $user_ids) . '"';
        // ユーザーID一覧に含まれるユーザーで絞る
        $query .= ' AND T.user_id IN (' . $user_ids_csv . ')';
    }
    //新しい順に並び替え
    $query .='ORDER BY T.created_at DESC';
    //表示件数を50件までにする
    $query .=' LIMIT 50';

    //クエリ実行
    $result = $mysqli->query($query);

    if($result){
        //データを配列で受け取る
        $response = $result->fetch_all(MYSQLI_ASSOC);
    }else{
        $response = false;
        echo 'エラーメッセージ :'.$mysqli->error."\n";
    }

    $mysqli->close();  

    return $response;

}

