<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("checkForms.php");
    include("bd.php");

    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = $_POST['username'];
        $password = $_POST['password'];
        $error = [];

        $errormsgs = checkLogin($mysqli, $username, $password);
        
        if($errormsgs['username'] != ""){
            $error['username'] = "error";
        }

        if($errormsgs['password'] != ""){
            $error['password'] = "error";
        }

        if($error == []){
            session_start();
            $_SESSION['username'] = $username;
            header("Location: index.php");
        }
            
    }

    echo $twig->render('login.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'username' => $username])
?>