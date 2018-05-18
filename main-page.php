<?php

    require_once 'session.php';
    logined_session();
    
    $username = 'root';
    $password = '';
    
    $database = new PDO('mysql:host=localhost;dbname=micro_posts;charset=UTF8;',$username,$password);

    $errorMessage = "";
    
    $my_id = $_SESSION['id'];
    
    if($_POST['logout'])
    {
        logout_session();
        exit;
    }
    
    if($_POST['post'])
    {
        $sql = 'INSERT INTO posts (post,user_id,rt_id) VALUES(:post,:user_id,:rt_id)';
        $statement = $database->prepare($sql);
        $statement->bindParam(':post',$_POST['post']);
        $statement->bindParam(':user_id',$my_id);
        $statement->bindParam(':rt_id',$my_id);
        $statement->execute();
        $statement = null;
    }

    if($_POST['post_delete'])
    {
        $sql = 'DELETE FROM posts WHERE id = :delete_id';
        $statement = $database->prepare($sql);
        $statement->bindParam(':delete_id',$_POST['post_delete']);
        $statement->execute();
        $statement = null;
    }
    
    $sql = 'SELECT *FROM users ORDER BY created_at DESC';
    $statement = $database->query($sql);
    $users_records = $statement->fetchAll();

    $sql = 'SELECT * FROM posts INNER JOIN users ON posts.user_id = users.id LEFT JOIN follows ON users.id = follows.follow_user_id WHERE follows.follower_user_id = :my_follow_id OR posts.user_id = :my_id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':my_follow_id',$my_id);
    $statement->bindParam(':my_id',$my_id);
    $statement->execute();
    $posts_records = $statement->fetchAll();
   
    
    $statement = null;
    
    $database = null;
?>


<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>MicroPosts</title>
        <style>
            .deletebutton
            {
               width:40px;
               height:15px;
               font-size:8px;
            }
            .inlineform
            {
                 display:inline;
            }
        </style>
    </head>
    <body>
        <?php
            $userpage = "<a href = main-page.php><h1>MicroPosts</h1></a>";
            echo $userpage;
        ?>
        
        <form action="" method="POST">
        <button type="submit" name = "logout" value= "logout">ログアウト</button>
        </form>
        
                    <h2>メインページです</h2>
                    <h2>投稿</h2>
                    <form action="" method="POST">
                    <input type="text" name="post" placeholder="つぶやき" required>
                    <input type="submit" value="投稿">
                    </form>
            
            <h2>タイムライン</h2>
<?php
            if($posts_records)
            {
                foreach($posts_records as $post_record)
                {
                    if($post_record['user_id'] == $my_id)
                    {
?>
                        <li>
                            <?php print "自分 のつぶやき :".htmlspecialchars($post_record['post'], ENT_QUOTES, 'UTF-8'); ?>
                            <form class = "inlineform" action="" method="POST">
                            <input type = "hidden" name = "post_delete" value= <?php echo $post_record[0];?>>
                            <button class = "deletebutton" type = "submit">削除</button>
                            </form>
                        </li>
<?php
                    }
                    else
                    {
?>
                        <li><?php print htmlspecialchars($post_record['username'], ENT_QUOTES, 'UTF-8')." のつぶやき :".htmlspecialchars($post_record['post'], ENT_QUOTES, 'UTF-8'); ?></li>
<?php
                    }
                }
            }
?>

        <h2>ユーザー 一覧</h2>
<?php
            if($users_records)
            {
                foreach($users_records as $user_record)
                {
                    $userpage = "<a href = user-page.php?id=".$user_record['id'].">";
?>
                    <li>
                        <?php echo $userpage ?>
                        <?php print htmlspecialchars($user_record['username'], ENT_QUOTES, 'UTF-8');?>
                        </a>
                    </li>
<?php
                }
            }
?>
    </body>
</html>