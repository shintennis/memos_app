<?php
require('../../my_db_info.dat');

try {
        $db = new PDO('mysql:dbname=$db_name;host=$db_host;charset=utf8;','$db_user', '$db_pass');
    } catch(PDOException $e){
        echo 'DB接続エラー : ' . $e->getMessage();
    }
?>
