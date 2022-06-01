<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    session_start();

    $mysqli = dbConnect();

    if (isset($_SESSION['username'])) {
        $user = getUser($mysqli, 'username', $_SESSION['username']);
    }

    
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $user){
        if(isset($_GET['prod'])){
            $codProd = $_GET['prod'];
        } else {
            $codProd = -1;
        }
        $comentario = json_decode(file_get_contents('php://input'));

        // insertarComentario($mysqli, "FECHA", $comentario[1], "marinamuca", "1");
        insertarComentario($mysqli, $comentario[1], $comentario[0], $_SESSION['username'], $codProd);
    }
?>