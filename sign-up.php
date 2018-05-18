<?php
    $username = 'root';
    $password = '';
    
    $database = new PDO('mysql:host=localhost;dbname=micro_posts;charset=UTF8;',$username,$password);


    $errorMessage = "";
    
    if($_POST['username'] && $_POST['mail_address'] && $_POST['user_password'])
    {
        $sql = 'SELECT mail_address FROM users WHERE mail_address = :mail_address';
        $statement = $database->prepare($sql);
        $statement->bindParam(':mail_address',$_POST['mail_address']);
        $statement->execute();
            
        $record = $statement->fetch();
           
        
        if($record == false)
        {
            
            $sql = 'INSERT INTO users (username, mail_address,password) VALUES(:username,:mail_address,:user_password)';
            $statement = $database->prepare($sql);
            $statement->bindParam(':username',$_POST['username']);
            $statement->bindParam(':mail_address',$_POST['mail_address']);
            $statement->bindParam(':user_password',$_POST['user_password']);
            $statement->execute();
            $statement = null;
         
            if($_POST['user_password'] == $_POST['re_password'])
            {
                header( "Location: main-page.php" ) ;
            }
            else
            {
                $errorMessage = "パスワードが間違っています";
            }
        }
        else
        {
            $errorMessage = "そのメールアドレスは既に使われています";
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
        <h2>新規登録</h2>
        <form action="" method="POST">
            <input type="text" name="username" placeholder="ユーザー名を入力" required>
            <input type="text" name="mail_address" placeholder="メールアドレスを入力" required>
            <br>
            <input type="text" name="user_password" placeholder="パスワードを入力" required>
            <input type="text" name="re_password" placeholder="パスワードを再入力" required>
            <input type="submit" value="新規登録">
        </form>
    </body>
</html>