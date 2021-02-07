<?php
session_start();

$_SESSION = array();

if (ini_get('session.use_cookies')) {
    //cookieの情報を削除
    $params = session_get_cookie_params();
    setcookie(session_name() . '', time() - 42000, 
        $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

//セッションを完全に削除
session_destroy();

//セッションに保存されてあったメールアドレスも削除
setcookie('email', '', time() - 3600);

header('Location: login.php');
exit();
?>