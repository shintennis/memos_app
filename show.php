<?php
session_start();
require('db_connect.php');

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit();
}

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m,
posts p WHERE m.id=p.member_id AND p.id=? ');
$posts->execute(array($_REQUEST['id']));

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
    integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                <div class="card-header-wrapper">
                    <div class="card-header" style="font-size: 20px;">
                        掲示板
                        <a class="btn btn-outline-secondary view-btn" href="index.php" style="float: right;">一覧へ</a>
                    </div>
                </div>
                <?php foreach ($posts as $post): ?>
                    <div class="card-body">
                        <div class="view-wrapper">
                            <div class="view-header">
                            <?php if(!empty($post['picture'])): ?>
                                <img class="view-img" src="member_img/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>">
                            <?php else: ?>
                                <img class="view-img" src="userIcon/user-icon.png"  alt="<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>">
                            <?php endif; ?>
                                <span><?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?></span>
                            </div>
                            <hr>
                            <div class="view-body">
                                <p class="view-message"><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?></p>
                                <p class="view-created"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
        </div>
    </main>
</body>
</html>