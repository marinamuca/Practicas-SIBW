<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("checkAuth.php");
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    session_start();
    $mysqli = dbConnect();

    $user = getUser($mysqli, 'username', $_SESSION['username']);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password_actual = $_POST['password-actual'];
        $password_nueva = $_POST['password-nueva'];
        $password_conf = $_POST['password-confirm'];

        $error = [];

        $errormsgs = checkEdit($mysqli, $username, $email, $password_actual, $password_nueva, $password_conf, $user);
        
        if($errormsgs['username'] != ""){
            $error['username'] = "error";
        }

        if($errormsgs['email'] != ""){
            $error['email'] = "error";
        }

        if($errormsgs['password_actual'] != ""){
            $error['password_actual'] = "error";
        }

        if($errormsgs['password_nueva'] != ""){
            $error['password_nueva'] = "error";
        }

        if($errormsgs['password_conf'] != ""){
            $error['password_conf'] = "error";
        }

        if($error == []){   
            if(!empty($username)){
                actualizarUsuario($mysqli, 'username', 'email', $username, $user['email']);
                $_SESSION['username'] = $username;
            }
            if(!empty($email)){
                actualizarUsuario($mysqli, 'email', 'username', $email, $user['username']);
            }
            if(!empty($password_actual)){
                $password = password_hash($password_nueva, PASSWORD_DEFAULT);
                actualizarUsuario($mysqli, 'password', 'username', $password, $user['username']);
            }
        }
          
        $user = getUser($mysqli, 'username', $_SESSION['username']);

    }

   
    echo $twig->render('perfil.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'user' => $user])
?>