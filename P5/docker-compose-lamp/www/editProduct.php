<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    include("checkForms.php");
    session_start();


    $mysqli = dbConnect();

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);


    if(isset($_GET['prod'])){
        $codProd = $_GET['prod'];
    } else {
        $codProd = -1;
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $nombre = $_POST['nombre'];
        $img = $_FILES['img'];
        $caption = $_POST['caption'];
        $precio = $_POST['precio'];
        $tamano = $_POST['tamano'];
        $papel = $_POST['papel'];
        $descripcion = $_POST['descripcion'];
        $publicado = $_POST['publicado'];
        $error = [];

        $errormsgs = checkEditProduct($mysqli, $codProd, $nombre, $img, $precio, $tamano, $papel, $descripcion, $caption, $publicado);

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
                updateProducto($mysqli, 'nombre'  ,$nombre, "s", $codProd);
            }
            if(!empty($precio)){
                updateProducto($mysqli, 'precio', $precio, "i",$codProd);
            }
            if(!empty($tamano)){
                updateProducto($mysqli, 'tamano'  ,$tamano, "s",$codProd);
            }
            if(!empty($papel)){
                updateProducto($mysqli, 'tipo_papel', $papel, "s",$codProd);
            }
            if(!empty($descripcion)){
                updateProducto($mysqli, 'descripcion', $descripcion, "s",$codProd);
            }

            if($publicado != null)
                publicarProducto($mysqli, $codProd, 1);
            else
                publicarProducto($mysqli, $codProd, 0);

            if($img['name'] != null)
                insertarImagen($mysqli, $codProd, $img['name'], $caption);

            header("Location: producto.php?prod=$codProd");
        }
            
    }

   
    $producto = getProducto($mysqli, $codProd);
    $imagenes = getImagenesProducto($mysqli, $codProd);
    $user = getUser($mysqli, 'username', $_SESSION['username']);

    echo $twig->render('editProduct.html', ['errorMsgs' => $errormsgs, 'error' => $error, 'texto' => $comentario['texto'], 'id' => $codProd, 'producto' => $producto, 'imagenes' => $imagenes, 'user' => $user ])
?>