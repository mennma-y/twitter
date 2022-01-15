<?php
///////////////
///便利な関数
/////////////////

//指定した日時からどれだけ経過したかを取得する

function convertToDayTimeAgo(string $datetime):string
{
    $unix =strtotime($datetime);
    $now =time();
    $diff_sec =$now - $unix;

    if($diff_sec < 60){
        $time = $diff_sec;
        $unit = '秒前';
    }elseif($diff_sec < 3600){
        $time =$diff_sec/60;
        $unit ='分前';
    }elseif($diff_sec < 86400){
        $time =$diff_sec/3600;
        $unit ='時間前';
    }elseif($diff_sec<2764800){
        $time =$diff_sec/86400;
        $unit ='日前';
    }else{
    
        if(date('Y')!==date('Y',$unix)){
            $time = date('Y年n月j日',$unix);
        }else{
            $time =date('n月j日',$unix); 
        }
        return $time;

    }
    return (int)$time.$unit;
}

//画像のファイル名から画像のURLを生成する
function buildImagePath(string $name = null, string $type):string
{
    if ($type === 'user' && !isset($name)) {
        return HOME_URL.'Views/img/icon-default-user.svg';
    }

    return HOME_URL.'Views/img_uploaded/'.$type.'/'.htmlspecialchars($name);
}