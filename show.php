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

<?php include('head.php'); ?>
<?php include('header.php'); ?>
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
                            <?php if($_SESSION['join']['image'] !== 'user-icon.png'): ?>
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