<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();


    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    if(isset($_GET['prod'])){
        $cod_prod = $_GET['prod'];
    } else {
        $cod_prod = -1;
    }

    deleteProducto($mysqli, $cod_prod);
    header("Location: index.php");

?>