<?php

function unlogined_session () 
{
    @session_start();
    
    if (isset($_SESSION["id"])) 
    {
        header('Location: ./main-page.php?id='.$_SESSION["id"]);
        exit;
    }
}

function logined_session() 
{
    @session_start();
    
    if (!isset($_SESSION["id"])) 
    {
        header('Location: ./login.php');
        exit;
    }
}

function logout_session()
{
    setcookie(session_name(), '', 1);
    session_destroy();
    header ('Location: ./login.php');
}

?>