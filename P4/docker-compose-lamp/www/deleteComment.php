<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();


    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    if(isset($_GET['comment'])){
        $cod_com = $_GET['comment'];
    } else {
        $cod_com = -1;
    }

    deleteComentario($mysqli, $cod_com);
    header("Location: allComments.php");

?>