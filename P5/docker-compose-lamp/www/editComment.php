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

    $comentario = getComentario($mysqli, $cod_com);

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $texto = $_POST['texto'];
        $error = [];

        $errormsgs['texto'] = "";
        if(empty($texto))
            $errormsgs['texto'] = "El texto no puede estar vacío";

        if($errormsgs['texto'] != ""){
            $error['texto'] = "error";
        }

        if($error == []){
            updateComentario($mysqli, $texto, $cod_com);
            header("Location: allComments.php");
        }
            
    }

   

    $user = getUser($mysqli, 'username', $_SESSION['username']);

    echo $twig->render('editComment.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'texto' => $comentario['texto'], 'id' => $cod_com, 'user' => $user ])
?>