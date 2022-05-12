<?php
///////////////////////////////////////
// 通知データを処理
///////////////////////////////////////
 
/**
 * 通知を作成
 *
 * @param array{received_user_id:int,sent_user_id:int,message:string} $data
 * @return int|false
 */
function createNotification(array $data)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }
 
    // ------------------------------------
    // SQLクエリを作成
    // ------------------------------------
    // 新規登録のクエリを作成
    $query = 'INSERT INTO notifications (received_user_id, sent_user_id, message) VALUES (?, ?, ?)';
    $statement = $mysqli->prepare($query);
 
    // プレースホルダに値をセット
    $statement->bind_param('iis', $data['received_user_id'], $data['sent_user_id'], $data['message']);
 
    // ------------------------------------
    // 戻り値を作成
    // ------------------------------------
    // クエリを実行し、SQLエラーでない場合
    if ($statement->execute()) {
        // 戻り値用の変数にセット：インサートID（notifications.id）
        $response = $mysqli->insert_id;
    } else {
        // 戻り値用の変数にセット：失敗
        $response = false;
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
 
    // ------------------------------------
    // 後処理
    // ------------------------------------
    // DB接続を開放
    $statement->close();
    $mysqli->close();
 
    return $response;
}


function findNotifications(int $user_id)
{
    // DB接続
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($mysqli->connect_errno) {
        echo 'MySQLの接続に失敗しました。：' . $mysqli->connect_error . "\n";
        exit;
    }

    $user_id = $mysqli->real_escape_string($user_id);

    $query = <<<SQL
        SELECT
            N.id AS notification_id,
            N.message AS notification_message,
            U.name AS user_name,
            U.nickname AS user_nickname,
            U.image_name AS user_image_name
            
        FROM
            notifications AS N
            JOIN
                users AS U ON U.id = N.sent_user_id AND U.status = 'active'
        WHERE
            N.status = 'active' AND N.received_user_id = '$user_id'
        ORDER BY
            N.created_at DESC
        LIMIT 50
    SQL;


    

    // ------------------------------------
    // 戻り値を作成
    // ------------------------------------
    // クエリを実行し、SQLエラーでない場合
    if ($result = $mysqli->query($query)) {
        // 全件取得
        $notifications = $result->fetch_all(MYSQLI_ASSOC);
        
    } else {
        $notifications = false;
        echo 'エラーメッセージ：' . $mysqli->error . "\n";
    }
 
    return  $notifications;
}