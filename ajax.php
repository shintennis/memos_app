
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="js/action.js"></script>

<?php
ini_set('display_errors', 1);

session_start();
require('db_connect.php');

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




    
    if (isset($_POST)) {

        $u_id = $_POST['c_id'];
        $p_id = $_POST['p_id'];
        var_dump($u_id);
        var_dump($p_id);
        
        //既に登録されているか確認
        if(isGood($u_id,$p_id)){
            $sql = 'DELETE FROM good WHERE user_id =? AND post_id =?';
        }else{
            $sql = 'INSERT INTO good SET user_id=?, post_id=?, created_time=NOW()';
        }
        
    try{
        $dsn = $db->prepare($sql);
        $dsn->execute(array($u_id,
                            $p_id,
                            ));
        print_r($dsn->errorinfo());
        $dsn->fetch();
    
    } catch (\Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        echo json_encode("error");
      }
    } else {
        echo "POST_error";
    }

