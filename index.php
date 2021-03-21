<?php
session_start();
require('db_connect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=? ');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

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

$page = $_REQUEST['page'];
if ($page == '') {
    $page = 1;
}
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts ');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);
$start = ($page - 1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p 
WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');

$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

if (isset($_REQUEST['res'])) {
    //返信の処理
    $resuponse = $db->prepare('SELECT m.name, m.picture, p.*
    FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ');
    $resuponse->execute(array($_REQUEST['res']));
    
    $table = $resuponse->fetch();
    $message = '@' . $table['name'] . $table['message'] . " > ";
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
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">


    <title>掲示板</title>
</head>
<body>
    <nav class="navbar navbar-dark">
        <a class="navbar-brand" href="#">
            掲示板
        </a>
    </nav>
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
                    <img src="member_img/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" style="float: left" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>">
                    <p class="user_name" style="margin: 0 auto;"><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?> 
                        <span>
                            <small class="text-muted" style="font-size: 12px;">
                                <a href="show.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="text-decoration: none;"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></a>
                                <a href="index.php?res=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="color: gray;">
                                <i class='far fa-comment-alt w3-large' style="margin-left: 10px;"></i></a>
                            </small>
                            <?php if ($_SESSION['id'] == $post['member_id']): ?>
                                <a href="delete.php?id=<?php print(htmlspecialchars($post['id'], ENT_QUOTES)); ?>" style="color: #F33;">
                                <i class="fa fa-trash w3-large" style="float: right;"></i></a>
                            <?php endif; ?>
                        </span>
                    </p>
                    <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
                </div>
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
</body>
</html>
