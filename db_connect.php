<?php
    try {
        $db = new PDO('mysql:dbname=memos_db;host=10.0.10.10;charset=utf8;unix_socket=/var/lib/mysql/mysql.sock',
        'root', 'Sinteni_1126');
    } catch(PDOException $e){
        echo 'DB接続エラー : ' . $e->getMessage();
    }
?>
