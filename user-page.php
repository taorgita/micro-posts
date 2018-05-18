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
        
    $sql = 'SELECT * FROM users WHERE id = :id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':id',$_GET['id']);
    $statement->execute();
    $page_master = $statement->fetch();

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
        $sql = 'DELETE FROM posts.post WHERE id = :delete_id';
        $statement = $database->prepare($sql);
        $statement->bindParam(':delete_id',$_POST['post_delete']);
        $statement->execute();
        $statement = null;
    }
    
    $sql = 'SELECT * FROM follows WHERE follow_user_id = :follow_user_id AND follower_user_id = :follower_user_id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':follow_user_id',$_POST['follow_user_id']);
    $statement->bindParam(':follower_user_id',$my_id);
    $statement->execute();
        
    $follewed = $statement->fetch();
    
    if($_POST['follow_user_id'])
    {
        if(!$follewed)
        {
            $sql = 'INSERT INTO follows (follow_user_id,follower_user_id) VALUES(:follow_user_id,:follower_user_id)';
            $statement = $database->prepare($sql);
            $statement->bindParam(':follow_user_id',$_POST['follow_user_id']);
            $statement->bindParam(':follower_user_id',$my_id);
            $statement->execute();
        }
        else
        {
            $sql = 'DELETE FROM follows WHERE follow_user_id = :follow_user_id AND follower_user_id = :follower_user_id';
            $statement = $database->prepare($sql);
            $statement->bindParam(':follow_user_id',$_POST['follow_user_id']);
            $statement->bindParam(':follower_user_id',$my_id);
            $statement->execute();
        }
    }
    
    $sql = 'SELECT *FROM users ORDER BY created_at DESC';
    $statement = $database->query($sql);
    $users_records = $statement->fetchAll();
    
    
    $sql = 'SELECT * FROM follows INNER JOIN users ON follows.follow_user_id = users.id AND follows.follower_user_id = :user_id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':user_id',$page_master['id']);
    $statement->execute();
    $follow_users_records = $statement->fetchAll();
    
    $sql = 'SELECT * FROM follows INNER JOIN users ON follows.follower_user_id = users.id AND follows.follow_user_id = :user_id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':user_id',$page_master['id']);
    $statement->execute();
    $follower_users_records = $statement->fetchAll();
    
    $sql = 'SELECT * FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.user_id = :user_id';
    $statement = $database->prepare($sql);
    $statement->bindParam(':user_id',$page_master['id']);
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
    </head>
    <body>
        <?php
            $userpage = "<a href = main-page.php><h1>MicroPosts</h1></a>";
            echo $userpage;
        ?>
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
        
        <form action="" method="POST">
        <button type="submit" name = "logout" value= "logout">ログアウト</button>
        </form>
        
           <?php
                if($page_master['id'] == $my_id) 
                {
                    ?>
                    <h2>自分のページです</h2>
                    <h2>投稿</h2>
                    <form action="" method="POST">
                    <input type="text" name="post" placeholder="つぶやき" required>
                    <input type="submit" value="投稿">
                    </form>
                    <?php
                }
                else
                {
                    ?>
                    <h2>
                        <?php 
                            print htmlspecialchars($page_master['username'], ENT_QUOTES, 'UTF-8')." のページです";
                        ?>
                    </h2>
                    <form action="" method="POST">
                    <?php 
                        $follow_text;
                        if($follewed) $follow_text = "フォローする";
                        else $follow_text = "フォロー解除";
                    ?>
                    <input type="hidden" name = "follow_user_id" value= <?php echo $page_master['id']; ?>>
                    <button type = "submit"><?php print $follow_text ?></button>
                    </form>
                    <?php
                }
            ?>        
            
            <h2>つぶやき一覧</h2>
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
        <h2>フォロー</h2>
<?php
            if($follow_users_records)
            {
                foreach($follow_users_records as $follow_user)
                {
                    $userpage = "<a href = user-page.php?id=".$follow_user['follow_user_id'].">";
    ?>
                    <li>
                    <?php echo $userpage;?>
                    <?php print htmlspecialchars($follow_user['username'], ENT_QUOTES, 'UTF-8');?>
                    </a>
                    </li>
<?php
                }
            }
?>
         <h2>フォロワー</h2>
<?php
            if($follower_users_records)
            {
                foreach($follower_users_records as $follower_user)
                {
                    $userpage = "<a href = user-page.php?id=".$follower_user['follower_user_id'].">";
    ?>
                    <li>
                    <?php echo $userpage;?>
                    <?php print htmlspecialchars($follower_user['username'], ENT_QUOTES, 'UTF-8');?>
                    </a>
                    </li>
<?php
                }
            }
?>
    </body>
</html>