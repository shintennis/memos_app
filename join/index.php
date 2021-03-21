<?php
session_start();

require('../db_connect.php');

//エラーチェック
if (!empty($_POST)) {

    $username = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $pass_re = $_POST['password_re'];
    $fileName = $_FILES['image']['name'];

    $err_msg = array();

    if($username === '') {
        $error['name'] = 'blank';
    }
    if($email === '') {
        $error['email'] = 'blank';
    }
    if(!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", $email)) {
        $error['email'] = 'no_email';
    }
    if(strlen($pass) < 4) {
        $error['password'] = 'no_password';
    }
    if(strlen($pass_re) < 4) {
        $error['password_re'] = 'no_password';
    }
    if($pass === '') {
        $error['password'] = 'blank';
    }
    if($pass_re === '') {
        $error['password_re'] = 'blank';
    }
    if($pass !== $pass_re) {
        $error['password_re'] = 'nomatch';
    }
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);

        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
            $error['image'] = 'type';
        }
    }

    //アカウントの重複チェック
    if(empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=? ');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();

        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }



    //写真ファイルのチェック
    if(empty($error)) {
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_img/' . $image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();     
    }
}

if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION)) {
    $_POST = $_SESSION['join'];
}

?>


<!DOCTYPE html>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
    integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <title>会員登録</title>
</head>
<body>
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" alt="#">
            会員登録
        </a>
    </nav>
    <main>
        <div class="card">
            <form class="card-body" action="" method="POST" enctype="multipart/form-data">
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        会員登録 
                        <a href="../login.php" class="login_tag" style="text-decoration: none; float: right;">ログイン</a>
                    </div>
                    
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">・ニックネーム<span class="required">必須</span></label>
                    <input type="text" name="name" class="form-control" value="<?php print(htmlspecialchars($username));?>">
                    <?php if ($error['name'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊ニックネームを入力してください</p></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・メールアドレス<span class="required">必須</span></label>
                    <input type="text" name="email" class="form-control" value="<?php print(htmlspecialchars($email));?>">
                    <?php if ($error['email'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊メールアドレスを入力してください</p></small>
                    <?php elseif ($error['email'] === 'duplicate'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊指定しているメールアドレスは既に使われています</p></small>
                    <?php elseif ($error['email'] === 'no_email'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊E-mailの形式で入力してください</p></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・パスワード(4文字以上)<span class="required">必須</span></label>
                    <input type="password" name="password" class="form-control" value="<?php print(htmlspecialchars($pass));?>">
                    <?php if ($error['password'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワードを入力してください</p></small>
                    <?php elseif ($error['password'] === 'no_password'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード4文字以上で入力してください</p></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・パスワード確認用(4文字以上)<span class="required">必須</span></label>
                    <input type="password" name="password_re" class="form-control" value="<?php print(htmlspecialchars($pass_re));?>">
                    <?php if ($error['password_re'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード確認用を入力してください</p></small>
                    <?php elseif ($error['password_re'] === 'nomatch'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワードが一致していません。</p></small>
                    <?php elseif ($error['password_re'] === 'no_password'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード4文字以上で入力してください</p></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・写真などを選択してください</label>
                    <input type="file" name="image" size="35" value="test">
                <?php if ($error['image'] === 'type'): ?>
                    <small class="form-text text-muted">＊写真などは「.gif」「.jpg」「.png」の画像を指定してください</small>
                <?php elseif (!empty($error)): ?>
                    <small class="form-text text-muted"><p class="smail-error">＊もう一度画像を指定してください</p></small>
                <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">入力内容を確認する</button>
            </form>
        </div>
    </main>
</body>
</html>
