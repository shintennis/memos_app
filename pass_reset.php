<?php
session_start();
require('db_connect.php');
var_dump($_SESSION['pass_reset']);
if (!empty($_POST)) {

    $pass = $_POST['password'];
    $pass_re = $_POST['password_re'];

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


    //チェック
    if(empty($error)) {
        try {
            //準備
            $pass_set = $db->prepare('SELECT * FROM members WHERE id= :id');
            //実行
            $pass_set->execute(array(':id' => $_GET['id']));
            $result = $pass_set->fetch(PDO::FETCH_ASSOC);
            $_SESSION['pass_reset']['password'] = $_POST['password'];
            $_SESSION['pass_reset']['password_re'] = $_POST['password_re'];
            header('Location: reset_check.php');
            exit();
        } catch(Exception $e) {
            echo "失敗:" . $getmessage();
        }
    }

}



?>
<?php include('head.php'); ?>
<?php include('header.php'); ?>
<main>
        <div class="card">
            <form class="card-body" action="" method="POST">
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        パスワード変更
                    </div>
                </div>

                
                <div class="form-group">
                <input type="hidden" name="id" value="<?php if (!empty($result['id'])) echo(htmlspecialchars($result['id'], ENT_QUOTES));?>">
                </div>
                <div class="form-group">
                    <label>・パスワード(4文字以上)<span class="required">必須</span></label>
                    <input type="password" name="password" class="form-control">
                    <?php if ($error['password'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワードを入力してください</p></small>
                    <?php elseif ($error['password'] === 'no_password'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード4文字以上で入力してください</p></small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>・パスワード確認用(4文字以上)<span class="required">必須</span></label>
                    <input type="password" name="password_re" class="form-control">
                    <?php if ($error['password_re'] === 'blank'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード確認用を入力してください</p></small>
                    <?php elseif ($error['password_re'] === 'nomatch'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワードが一致していません。</p></small>
                    <?php elseif ($error['password_re'] === 'no_password'): ?>
                        <small class="form-text text-muted"><p class="smail-error">＊パスワード4文字以上で入力してください</p></small>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">入力内容を確認する</button>
            </form>
        </div>
    </main>
</body>
</html>