<?php
    session_start();
    require('db_connect.php');

    if (!empty($_POST)) {
        $email = $_POST['email'];
        
        if ($_POST['email'] !== '') {
            $login = $db->prepare('SELECT * FROM members WHERE email= :email');
            $login->execute(array(':email' => $_POST['email']));
            $member = $login->fetch(PDO::FETCH_ASSOC);
            if ($member) {
                $_SESSION['pass_reset'] = $member;
                var_dump($_SESSION['pass_reset']);
                header('Location: pass_reset.php');
                exit();
            } else {
                $error['send'] = 'failed';
            }
        } else {
            $error['send'] = 'blank';
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
                        パスワード変更
                    </div>
                </div>
                <div class="form-group">
                <input type="hidden" name="id" value="<?php if (!empty($result['id'])) echo(htmlspecialchars($result['id'], ENT_QUOTES));?>">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">登録したメールアドレスを入力してください</label>
                    <input type="text" name="email" class="form-control" placeholder='example@gmail.com'>
                <?php if ($error['send'] === 'blank'): ?>
                    <small class="form-text text-muted"><p class="smail-error">＊メールアドレスを入力してください</p></small>
                <?php endif; ?>
                <?php if ($error['send'] === 'failed'): ?>
                    <small class="form-text text-muted"><p class="smail-error">＊登録済みメールアドレスを入力してください</p></small>
                <?php endif; ?>
                </div>
                <a href="pass_reset.php">
                    <button type="submit" class="btn login">変更画面へ</button>
                </a>
            </form>
        </div>
    </main>
</body>
</html>