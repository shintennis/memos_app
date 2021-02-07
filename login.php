<?php
session_start();
require('db_connect.php');

if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
    $email = $_POST['email'];
    
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=? ');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password'])
        ));
        $member = $login->fetch();

        if ($member) {
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            if ($_POST['save'] === 'on') {
                setcookie('email', $_POST['email'], time()+60*60*24*14);
            }

            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
    integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>ログイン</title>
</head>
<body>
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="#">
            ログイン
        </a>
    </nav>
    <main>
        <div class="card">
            <form class="card-body" action="" method="POST" enctype="multipart/form-data">
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        ログイン
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">・メールアドレス<span class="required">必須</span></label>
                    <input type="text" name="email" class="form-control" value="<?php print(htmlspecialchars($email, ENT_QUOTES)); ?>">
                <?php if ($error['login'] === 'blank'): ?>
                    <small class="form-text text-muted">＊メールアドレスとパスワードを入力してください</small>
                <?php endif; ?>
                <?php if ($error['login'] === 'failed'): ?>
                    <small class="form-text text-muted">＊ログインに失敗しました。正しく入力してください</small>
                <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">・パスワード<span class="required">必須</span></label>
                    <input type="password" name="password" class="form-control" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
                </div>
                <div class="form-troup">
                    <input id="save" type="checkbox" name="save" value="on">
                    <label for="save">次回からは自動的にログインする</label>
                </div>
                <button type="submit" class="btn login">ログインする</button>
            </form>
        </div>
    </main>
</body>
</html>