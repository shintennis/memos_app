<?php

//セッションスタート
session_start();
//DB接続
require('db_connect.php');

//ろぐいん時間の確認
if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    //DBからユーザーIDの取得
    $members = $db->prepare('SELECT * FROM members WHERE id=? ');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

//返信機能
if (!empty($_POST)) {
    if ($_POST['message'] !== '') {
        if (!isset($_REQUEST['res'])) {
            $_POST['reply_post_id'] = 0;
        }
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW() ');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));
    
        header('Location: index.php');
        exit();
    }
}

//---------------------------いいね---------------------------//


    //ユーザーIDと投稿IDを元にいいね値の重複チェック
    function dbConnect(){
        // DBへの接続準備
        $dsn = 'mysql:dbname=memos_db;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';
        $options = array(
            // SQL実行失敗時にはエラーコードのみ設定
            PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
            // デフォルトフェッチモードを連想配列形式に設定
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
            // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        );
        // PDOオブジェクト生成（DBへ接続）
        $dbh = new PDO($dsn, $user, $password, $options);
        return $dbh;
    }

    //クエリの実行
    function queryPost($dbh, $sql, $data){
        // クエリ作成
        $stmt = $dbh->prepare($sql);
        // SQL文を実行
        if(!$stmt->execute($data)){
            print_r("クエリ失敗しました");
            return 0;
        }
        print_r('クエリ成功');
        return $stmt;
    }
    
        //DBに登録されているか確認
    function isGood($user_id, $post_id){
        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM good WHERE post_id = :p_id AND user_id = :u_id';
            $data = array(':u_id' => $user_id, ':p_id' => $post_id);
            // クエリ実行
            $stmt = queryPost($dbh, $sql, $data);
    
            if($stmt->rowCount()){
                print_r('お気に入りです');
                return true;
            }else{
                print_r('特に気に入ってません');
                return false;
            }
    
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
        }
    }
//------------------------------------------------------//


//ページ移動
$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

// $dbh = dbConnect();
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts ');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);
$start = ($page - 1) * 5;

//ページに表示する投稿数制限
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p 
WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');

$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();


//返信の処理
if (isset($_REQUEST['res'])) {
    // $dbh = dbConnect();
    $resuponse = $db->prepare('SELECT m.name, m.picture, p.*
    FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ');
    $resuponse->execute(array($_REQUEST['res']));
    
    $table = $resuponse->fetch();
    $message = '@' . $table['name'] . $table['message'] . " > ";
}


?>

<?php include('head.php'); ?>
<?php include('header.php'); ?>
    <main>
        <div class="card">
            <form class="card-body" action="" method="POST" enctype="multipart/form-data">
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        メッセージ
                            <a href="logout.php" class="btn btn-outline-danger" style="float: right;">ログアウト</a>
                    </div>
                </div>
                <div class="form-group">
                    <h4><?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>さん</h4>
                    <textarea id="textarea" name="message"><?php print(htmlspecialchars($message, ENT_QUOTES)); ?></textarea>
                    <input type="hidden" name="reply_post_id" value="<?php print(htmlspecialchars($_REQUEST['res'], ENT_QUOTES)); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary comment">投稿</button>
                
            </form>

        <?php foreach ($posts as $post): ?>
            <div class="msg">
                <div class="msg-body">
                <?php if($post['picture'] !== 'user-icon.png'): ?>
                    <img src="member_img/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" style="float: left" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>">
                <?php else: ?>
                    <img src="userIcon/user-icon.png" style="float: left" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>">
                <?php endif; ?>
                    <p class="user_name"><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?> 
                        <span>
                            <small class="text-muted">
                                <a href="show.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="text-decoration: none;"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>
                                <a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="color: gray;">
                                <i class="far fa-comment fa-lg px-16"></i></a>
                            </small>
                            <?php if ($_SESSION['id'] == $post['member_id']): ?>
                                <a href="delete.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="color: #F33;">
                                <i class="fas fa-trash fa-md px-16" style="float: right;"></i></a>
                            <?else: ?>
                            <?php endif; ?>
                        </span>
                    </p>
                    <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
                </div>
                <!-- いいね機能 -->
                <form class="favorite_count" action="#" method="post" style="float: right;">
                    <input type="hidden" name="post_id">
                    <div class="btn-good" data-user_id="<?php echo $_SESSION['id'] ?>" data-post_id="<?php echo $post['id'] ?>">
                    <i class="fa-heart fa-lg px-16 iro
                        <?php
                        if(!isGood($_SESSION['id'],$post['id'])){ 
                            //いいね押したらハートが塗りつぶされる
                            echo ' far';
                        }else{ 
                            //いいねを取り消したらハートのスタイルが取り消される
                            echo ' fas';
                        }; ?>"></i>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>

            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item"><a class="page-link" href="index.php?page=<?php print($page-1); ?>">前のページ</a></li>
                <?php endif; ?>
                <?php if($page < $maxPage): ?>
                <li class="page-item"><a class="page-link" href="index.php?page=<?php print($page+1); ?>">次のページ</a></li>
                <?php endif; ?>
            </ul>
            </div>
        </main>
        <script type="text/javascript" src="js/action.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/cesiumjs/1.78/Build/Cesium/Cesium.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</body>
</html>
