<?php
    require_once 'session.php';
    unlogined_session();
    
    $username = 'root';
    $password = '';
    
    $database = new PDO('mysql:host=localhost;dbname=micro_posts;charset=UTF8;',$username,$password);


    $errorMessage = "";
    
    if($_POST['mail_address'] && $_POST['user_password'])
    {
        $sql = 'SELECT * FROM users WHERE mail_address = :mail_address';
        $statement = $database->prepare($sql);
        $statement->bindParam(':mail_address',$_POST['mail_address']);
        $statement->execute();
        
        $record = $statement->fetch();
        
        if($record == true)
        {
            if($record['password'] == $_POST['user_password'])
            {
                session_regenerate_id(true);
                $_SESSION['id'] = $record['id'];
                header( "Location: main-page.php?id=".$record['id']);
            }
            else 
            {
                $errorMessage = "パスワードが間違っています";
            }
        }
        else
        {
            $errorMessage = "登録されていないメールアドレスです";
        }
        
        
        $statement = null;
    }
    
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
    var_dump($errorMessage);
?>
        <a href="login.php"><h1>MicroPosts</h1></a>
        <h2>ログイン</h2>
        <form action="" method="POST">
            <input type="text" name="mail_address" placeholder="メールアドレスを入力" required>
            <br>
            <input type="text" name="user_password" placeholder="パスワードを入力" required>
            <input type="submit" name="user_login" value="ログイン">
        </form>
        <form action="sign-up.php">
        <h2>新規登録</h2>
        <input type="submit" value="新規登録">
        </form>
    </body>
</html>