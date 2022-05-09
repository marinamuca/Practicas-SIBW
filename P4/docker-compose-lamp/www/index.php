<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    $msqli = dbConnect();
    $productos = getProductos($msqli);

    echo $twig->render('portada.html', ['productos' => $productos])
?>