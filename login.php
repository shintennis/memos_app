<?php
// $cookieParams = session_get_cookie_params();
// $cookieParams[samesite] = "None";
// session_set_cookie_params($cookieParams);
// header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');

session_start();
require('db_connect.php');
// require('function.php');

if ($_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
    $email = $_POST['email'];
    
    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        // $dbh = dbConnect();
        $login = $db->prepare('SELECT * FROM members WHERE email= :email AND password= :password');
        $login->execute(array(
            ':email' => $_POST['email'],
            ':password' => sha1($_POST['password'])
        ));
        $member = $login->fetch(PDO::FETCH_ASSOC);

        
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

<?php include('head.php'); ?>
<?php include('header.php'); ?>
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
                    <small class="form-text text-muted"><p class="smail-error">＊メールアドレスとパスワードを入力してください</p></small>
                <?php endif; ?>
                <?php if ($error['login'] === 'failed'): ?>
                    <small class="form-text text-muted"><p class="smail-error">＊ログインに失敗しました。正しく入力してください</p></small>
                <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1">・パスワード<span class="required">必須</span></label>
                    <input type="password" name="password" class="form-control" value="<?php print(htmlspecialchars($_POST['password'], ENT_QUOTES)); ?>">
                </div>
                <div class="form-troup">
                    <a href="send_mail.php" style="text-decoration: none;">パスワードを忘れた時</a>
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