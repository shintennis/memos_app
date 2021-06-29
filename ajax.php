
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="js/action.js"></script>

<?php
session_start();
require('db_connect.php');

//DBに登録されているかチェック
function check_favolite_duplicate($user_id,$post_id){
    // $dsn='mysql:dbname=memos_db;host=memos-web.chte87d6gkzh.ap-northeast-1.rds.amazonaws.com;charset=utf8;';
    // $user='memos_db';
    // $password='Sinteni1126';
    // $dbh=new PDO($dsn,$user,$password);
    // $sql = "SELECT *
    //         FROM good
    //         WHERE :user_id = user_id AND :post_id = post_id";
    $dsn = $db->preare('SELECT * FROM good WHERE :user_id = user_id AND :post_id = post_id');
    $dsn->execute(array(':user_id' => $user_id ,
                         ':post_id' => $post_id));
    $favorite = $dsn->fetch();
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute(array(':user_id' => $user_id ,
    //                      ':post_id' => $post_id));
    // $favorite = $stmt->fetch();
    return $favorite;
}






if (isset($_POST)) {

    $post_u = $_POST['c_id'];
    $post_p = $_POST['p_id'];
    var_dump($post_u);
    var_dump($post_p);
    
    //既に登録されているか確認
    if(check_favolite_duplicate($post_u,$post_p)){
        $sql = 'DELETE FROM good WHERE user_id =? AND post_id =?';
    }else{
        $sql = 'INSERT INTO good SET user_id=?, post_id=?, created=NOW()';
    }
    
    try{
        $dsn = $db->prepare($sql);
        $dsn->execute(array($post_u,
                            $post_p,
                            ));
        $dsn->fetch();
    

} catch (\Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
    echo json_encode("error");
  }
} else {
    echo "POST_error";
}

