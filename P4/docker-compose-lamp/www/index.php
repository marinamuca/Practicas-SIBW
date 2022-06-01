<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $mysqli = dbConnect();
    $productos = getAllProducts($mysqli);

    if(isset($_SESSION['username']))
        $user = getUser($mysqli, 'username', $_SESSION['username']);

    echo $twig->render('portada.html', ['productos' => $productos, 'user' => $user])
?>