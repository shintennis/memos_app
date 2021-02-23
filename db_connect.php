<?php
    try {
        $db = new PDO('mysql:dbname=memos_db;host=memos-web.chte87d6gkzh.ap-northeast-1.rds.amazonaws.com;charset=utf8;port=3306',
        'memos_db', 'Sinteni1126');
    } catch(PDOException $e){
        echo 'DB接続エラー : ' . $e->getMessage();
    }
?>
