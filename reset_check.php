<?php
session_start();
require('db_connect.php');
var_dump($_SESSION['pass_reset']);

//セッション情報を確認
if(!isset($_SESSION['pass_reset'])) {
    header('Location: index.php');
    exit();
}

//パスワードをアップデート
if (!empty($_POST)) {
    try {
        $reset = $db->prepare('UPDATE members SET password = :password, password_re = :password_re, modified=NOW() WHERE id = :id');
        $reset->execute(array(
            ':id' => $_SESSION['pass_reset']['id'],
            ':password' => sha1($_SESSION['pass_reset']['password']),
            ':password_re' => sha1($_SESSION['pass_reset']['password_re']),
        ));
        $result = $reset->fetch();
        unset($_SESSION['pass_reset']);
        header('Location: thanks.php');
        exit();
    } catch(Exception $e) {
        echo "NG:" . $getmessage();
    }
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
                        パスワードリセット
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">・パスワード</label>
                    <p>【表示されません】</p>
                </div>
                <input class="btn btn-outline-success" type="button" value="訂正する" onclick="window.location ='pass_reset.php?action=rewrite'" />
                |  <button type="submit" class="btn btn-outline-primary">登録する</button>
            </form>
        </div>
    </main>
</body>
</html>