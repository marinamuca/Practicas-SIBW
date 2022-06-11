<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();


    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    if(isset($_GET['img']) && isset($_GET['prod'])){
        $img = $_GET['img'];
        $prod = $_GET['prod'];
        if(isset($_SESSION['username']))
            $user = getUser($mysqli, 'username', $_SESSION['username']); 
    } else {
        header("Location: index.php");
    } 
    
    if($user['rol'] == "GEST" || $user['rol'] == "SU"){
        deleteImagen($mysqli, $img);
        header("Location: editProduct.php?prod=" . $prod );
    } else
        header("Location: index.php");

?>