<?php
    try {
        $db = new PDO('mysql:dbname=memos_db;host=localhost;charset=utf8;unix_socket=/var/lib/mysql/mysql.sock',
        'root', 'Sinteni_1126');
    } catch(PDOException $e){
        echo 'DB接続エラー : ' . $e->getMessage();
    }
?>
