<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    if(isset($_GET['username'])){
        $userEdit = $_GET['username'];
    } else {
        $userEdit = -1;
    }
    if(isset($_GET['nuevoRol'])){
        $nuevoRol = $_GET['nuevoRol'];
    } else {
        $nuevoRol = -1;
    }

    $mysqli = dbConnect();
    $infoUser = getUser($mysqli, 'username', $userEdit);
    $superUsers = numSuperUsers($mysqli);

    if($infoUser['rol'] != 'SU' || ($infoUser['rol'] == 'SU' && $superUsers>1))
        actualizarUsuario($mysqli, 'COD_ROL', 'username', $nuevoRol, $userEdit);

    header("Location: allUsers.php");

?>