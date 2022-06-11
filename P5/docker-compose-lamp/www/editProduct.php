<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    include("checkForms.php");
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
        $nombre = $_POST['nombre'];
        $img = $_FILES['img'];
        $caption = $_POST['caption'];
        $precio = $_POST['precio'];
        $tamano = $_POST['tamano'];
        $papel = $_POST['papel'];
        $descripcion = $_POST['descripcion'];
        $error = [];

        $errormsgs = checkEditProduct($mysqli, $nombre, $img, $precio, $tamano, $papel, $descripcion, $caption);

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
            if(!empty($nombre)){
                updateProducto($mysqli, 'nombre'  ,$nombre, "s",$cod_prod);
            }
            if(!empty($precio)){
                updateProducto($mysqli, 'precio', $precio, "i",$cod_prod);
            }
            if(!empty($tamano)){
                updateProducto($mysqli, 'tamano'  ,$tamano, "s",$cod_prod);
            }
            if(!empty($papel)){
                updateProducto($mysqli, 'tipo_papel', $papel, "s",$cod_prod);
            }
            if(!empty($descripcion)){
                updateProducto($mysqli, 'descripcion', $descripcion, "s",$cod_prod);
            }

            if(!empty($img))
                insertarImagen($mysqli, $cod_prod, $img['name'], $caption);
            // header("Location: index.php");
        }
            
    }

   

    $user = getUser($mysqli, 'username', $_SESSION['username']);

    echo $twig->render('editProduct.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'texto' => $comentario['texto'], 'id' => $cod_prod, 'user' => $user ])
?>