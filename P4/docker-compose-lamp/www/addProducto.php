<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    include("checkForm.php");
    session_start();


    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nombre = $_POST['nombre'];
        $img = $_FILES['img'];
        $caption = $_POST['caption'];
        $precio = $_POST['precio'];
        $tamano = $_POST['tamano'];
        $papel = $_POST['papel'];
        $descripcion = $_POST['descripcion'];
        $error = [];

        $errormsgs = checkAddProduct($mysqli, $nombre, $img, $precio, $tamano, $papel, $descripcion, $caption);

        if($errormsgs['nombre'] != ""){
            $error['nombre'] = "error";
        }
        if($errormsgs['img'] != ""){
            $error['img'] = "error";
        }
        if($errormsgs['caption'] != ""){
            $error['caption'] = "error";
        }
        if($errormsgs['precio'] != ""){
            $error['precio'] = "error";
        }
        if($errormsgs['tamano'] != ""){
            $error['tamano'] = "error";
        }
        if($errormsgs['papel'] != ""){
            $error['papel'] = "error";
        }
        if($errormsgs['descripcion'] != ""){
            $error['descripcion'] = "error";
        }

        if($error == []){
            $id = insertarProducto($mysqli,  $nombre, $precio, $tamano, $papel, $descripcion);
            if(!empty($img))
                insertarImagen($mysqli, $id, $img['name'], $caption);
            header("Location: index.php");
        }
            
    }

   

    $user = getUser($mysqli, 'username', $_SESSION['username']);

    echo $twig->render('addProducto.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'texto' => $comentario['texto'], 'id' => $cod_com, 'user' => $user ])
?>