<?php
session_start();
require('../db_connect.php');

if(!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}

if (!empty($_POST)) {
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?,
    picture=?, created=NOW() ');
    $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        sha1($_SESSION['join']['password']),
        $_SESSION['join']['image']
    ));
    unset($_SESSION['join']);

    header('Location: thanks.php');
    exit();
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
            MEMO
        </a>
    </nav>
    <main>
        <div class="card">
            <form class="card-body" action="" method="POST">
                <input type="hidden" name="action" value="submit" />
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        会員登録
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">・ニックネーム<span class="required">必須</span></label>
                    <p>
                        <?php print(htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES)); ?>
                    </p>
                </div>
                <div class="form-group">
                    <label for="email">・メールアドレス<span class="required">必須</span></label>
                    <p>
                    <?php print(htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES)); ?>
                    </p>
                </div>
                <div class="form-group">
                    <label for="password">・パスワード<span class="required">必須</span></label>
                    <p>【表示されません】</p>
                </div>
                <div class="form-group">
                    <label for="picture">・選択した写真など</label>
                    <?php if ($_SESSION['join']['image'] != ''): ?>
                    <img src="../member_img/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES)) ?>">
                    <?php else: ?>
                        <img src="../userIcon/user-icon.png">
                    <?php endif; ?>
                </div>
                <a class="btn btn-outline-success" href="index.php?action=rewrite">訂正する</a>  |  <button type="submit" class="btn btn-outline-primary">登録する</button>
            </form>
        </div>
    </main>
</body>
</html>