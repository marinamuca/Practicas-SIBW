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

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $tag = $_POST['tag'];
    }

    if($tag != "")
        addTag($mysqli, $cod_prod, $tag);
    header("Location: producto.php?prod=" . $cod_prod);

?>