<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("checkForms.php");
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $mysqli = dbConnect();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password_conf = $_POST['password-confirm'];

        $error = [];

        $errormsgs = checkRegister($mysqli, $username, $email, $password, $password_conf);
        
        if($errormsgs['username'] != ""){
            $error['username'] = "error";
        }

        if($errormsgs['email'] != ""){
            $error['email'] = "error";
        }

        if($errormsgs['password'] != ""){
            $error['password'] = "error";
        }
        

        if($errormsgs['password_conf'] != ""){
            $error['password_conf'] = "error";
        }

        if($error == []){
            session_start();
            $password = password_hash($password, PASSWORD_DEFAULT);
            insertarUsuario($mysqli, $username, $email, $password);
            $_SESSION['username'] = $username;
            header("Location: index.php");
        }
          
    }

    echo $twig->render('register.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'username' => $username, 'email' => $email])
?>