<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("checkAuth.php");
    include("bd.php");
    session_start();

    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    $comments = getComments($mysqli);
    $user = getUser($mysqli, 'username', $_SESSION['username']);

    if($user['rol'] == "SU" || $user['rol'] == "MOD")
        echo $twig->render('users.html', ['user' => $user, 'comments' => $comments ])
?>