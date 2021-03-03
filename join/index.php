<?php
session_start();

require('../db_connect.php');

if (!empty($_POST)) {

    if($_POST['name'] === '') {
        $error['name'] = 'blank';
    }
    if($_POST['email'] === '') {
        $error['email'] = 'blank';
    }
    if(strlen($_POST['password']) < 4) {
        $error['password'] = 'no_password';
    }
    if($_POST['password'] === '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
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
        $path = '/var/www/html/memos_app/member_img/';
        if(!file_exists($path)) {
            mkdir($path, 0777);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $path . $image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();     
    } else {
        echo ("dekitenaiyo");
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
        <a class="navbar-brand" href="#">
            会員登録
        </a>
    </nav>
    <main>
        <div class="card">
            <form class="card-body" action="" method="POST" enctype="multipart/form-data">
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        会員登録
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">・ニックネーム<span class="required">必須</span></label>
                    <input type="text" name="name" class="form-control" value="<?php print(htmlspecialchars($_POST['name']));?>">
                    <?php if ($error['name'] === 'blank'): ?>
                        <small class="form-text text-muted">＊ニックネームを入力してください</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・メールアドレス<span class="required">必須</span></label>
                    <input type="text" name="email" class="form-control" value="<?php print(htmlspecialchars($_POST['email']));?>">
                    <?php if ($error['email'] === 'blank'): ?>
                        <small class="form-text text-muted">＊メールアドレスを入力してください</small>
                    <?php elseif ($error['email'] === 'duplicate'): ?>
                        <small class="form-text text-muted">＊指定しているメールアドレスは既に使われています</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・パスワード<span class="required">必須</span></label>
                    <input type="password" name="password" class="form-control" value="<?php print(htmlspecialchars($_POST['password']));?>">
                    <?php if ($error['password'] === 'blank'): ?>
                        <small class="form-text text-muted">＊パスワードを入力してください</small>
                    <?php elseif ($error['password'] === 'no_password'): ?>
                        <small class="form-text text-muted">＊パスワード4文字以上で入力してください</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・写真などを選択してください</label>
                    <input type="file" name="image" size="35" value="test">
                <?php if ($error['image'] === 'type'): ?>
                    <small class="form-text text-muted">＊写真などは「.gif」「.jpg」「.png」の画像を指定してください</small>
                <?php elseif (!empty($error)): ?>
                    <small class="form-text text-muted">＊もう一度画像を指定してください</small>
                <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">入力内容を確認する</button>
            </form>
        </div>
    </main>
</body>
</html>
