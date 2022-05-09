<?php
    function queryDB($mysqli, $fields, $table, $id, $codProd, $order){
        $codProd = $mysqli->real_escape_string($codProd); // Para evitar inyección de codigo

        $query = "SELECT $fields FROM $table WHERE $id = '$codProd' $order";
        $result = $mysqli->query($query);
        return $result;
    }

    function resultToArray($result){
        
        $rows = array();
        while($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    function dbConnect(){
        $mysqli = new mysqli("mysql", "admin", "SIBW", "SIBW"); // contenedor, user, passwd, DB
        if($mysqli->connect_errno){
            echo("Fallo al conectar: " . $mysqli->connect_errno);
        }
        return $mysqli;
    }

    function getProducto($mysqli, $codProd){
        
        // Consulta a la tabla producto
        $res = queryDB( $mysqli, 'COD_PROD, nombre, precio, descripcion, tamano, tipo_papel', 'producto','COD_PROD', $codProd, '' );
     
        $infoProduct = array('nombre' => 'Default name', 'precio' => '0', 'descripcion' => '', 'tamano' => '0x0 cm', 'papel' => '');
        // Si la consulta devuelve algo:
        if($res->num_rows > 0){
            $row = $res-> fetch_assoc();
            $infoProduct = array('id' => $row['COD_PROD'], 'nombre' => $row['nombre'], 'precio' => $row['precio'], 'descripcion' => $row['descripcion'], 'tamano' => $row['tamano'], 'papel' => $row['tipo_papel']);
        }
        return $infoProduct;
    }

    function getImagenesProducto($mysqli, $codProd){
        $result = queryDB($mysqli, 'imagen, caption', 'imagen_producto','COD_PROD', $codProd, '');
        $infoImagen = array();
       
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);

            $images = array();
            $caption = array();
            foreach ($rows as $key => $row) {
                $infoImagen[$key] = ['imagen' => base64_encode($row['imagen']), 'caption' => $row['caption']];
            }
            
        }
        return $infoImagen;     
    }

    function getProductos($mysqli){
        $result = queryDB($mysqli, 'COD_PROD, nombre', 'producto', 1, 1, 'ORDER BY Producto.COD_PROD ASC' );
       
        $rows = resultToArray($result);

        foreach ($rows as $key => $row) {
           $res_img = queryDB($mysqli, 'imagen', 'imagen_producto', 'COD_PROD', $row['COD_PROD'], '' );
           $rows_img = resultToArray($res_img);
           $productos[$key] = ['id' => $row['COD_PROD'], 'nombre' => $row['nombre'], 'imagen' => base64_encode($rows_img[0]['imagen'])];
        }

        return $productos;
    }

    function getComments($mysqli, $codProd){
        $result = queryDB($mysqli, 'cod_comment, texto, autor, fecha', 'Comentarios_producto', 'COD_PROD', $codProd, 'ORDER BY FECHA DESC ');

       
        $comments = [];
        
        // echo "<script>console.log('Debug Objects: " . $result->num_rows . "' );</script>";
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);
            foreach ($rows as $key => $row) {
                $comments[$key] = ['id' => $row['cod_comment'], 'texto' => $row['texto'], 'autor' => $row['autor'], 'fecha' => $row['fecha']];
                
            }
        } 

        return $comments;
    }

    function getBadWords($mysqli){
        $result = queryDB($mysqli, 'palabra', 'badWords', 1, 1, '' );
        $badWordsJSON = array();
        if($result->num_rows > 0){
            $rows = resultToArray($result);
            foreach ($rows as $key => $row) {
                $badWordsJSON[] = $row['palabra'];
            }
        }
        return $badWordsJSON;
    }

?>