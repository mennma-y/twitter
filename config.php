<?php
//エラー表示
ini_set('display_errors',1);
//日本時間に設定する
date_default_timezone_set('Asia/Tokyo');
//URL/ディレクトリ設定
define('HOME_URL','http://localhost/twitter/Twitterclone/');
//データベースの接続
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASSWORD','root');
define('DB_NAME','twitter');