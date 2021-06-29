<?php
session_start();
require('db_connect.php');

//セッション情報を確認
if(!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}

//チェックした情報をDBにinsert
if (!empty($_POST)) {
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, password_re=?,
    picture=?, created=NOW() ');
    $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        sha1($_SESSION['join']['password']),
        sha1($_SESSION['join']['password_re']),
        $_SESSION['join']['image']
    ));
    unset($_SESSION['join']);
    header('Location: thanks.php');
    exit();
}

?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
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
                    <?php if ($_SESSION['join']['image'] !== 'usericon.png'): ?>
                        <img src="member_img/<?php print(htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES));  ?>">
                    <?php else: ?>
                        <img src="userIcon/user-icon.png">
                    <?php endif; ?>
                </div>
                <input class="btn btn-outline-success" type="button" value="訂正する" onclick="window.location ='join_index.php?action=rewrite'" />
                |  <button type="submit" class="btn btn-outline-primary">登録する</button>
            </form>
        </div>
    </main>
</body>
</html>