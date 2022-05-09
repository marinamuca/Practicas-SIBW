<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");

    $loader = new \Twig\Loader\FilesystemLoader('templates');
    $twig = new \Twig\Environment($loader);

    if(isset($_GET['prod'])){
        $codProd = $_GET['prod'];
    } else {
        $codProd = -1;
    }

    $msqli = dbConnect();
    $infoProduct = getProducto($msqli, $codProd);
    $imagenes = getImagenesProducto($msqli, $codProd);
    $comments = getComments($msqli, $codProd);


    if(isset($_GET['print'])){
        echo $twig->render('producto_imprimir.html', ['producto' => $infoProduct, 'imagenes' => $imagenes]);
    }
    else{
        echo $twig->render('producto.html', ['producto' => $infoProduct, 'imagenes' => $imagenes, 'comments' =>  $comments]);
    }

?>