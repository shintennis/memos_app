<?php

try {
        $db = new PDO('mysql:dbname=memos_db;host=localhost;','root', 'root');
    } catch(PDOException $e){
        echo 'DB接続エラー : ' . $e->getMessage();
    }
?>
