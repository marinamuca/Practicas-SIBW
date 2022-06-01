<?php
    function queryDB($mysqli, $fields, $table, $id, $codigo, $order){
        // $codProd = $mysqli->real_escape_string($codProd); // Para evitar inyección de codigo

        $sentencia = $mysqli->prepare("SELECT $fields FROM $table WHERE $id = ? $order");

        if($table == "usuarios"){
            $sentencia->bind_param("s", $codigo );
        }
        else{
            $sentencia->bind_param("i", $codigo );
        }

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        // $query = "SELECT $fields FROM $table WHERE $id = '$codProd' $order";
        //  $result = $mysqli->query($query);
        $result = $sentencia->get_result();
        return $result;
    }

    function queryALL($mysqli, $fields, $table, $order = ""){
        $result = $mysqli->query("SELECT $fields FROM $table ORDER BY $order");
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
        $result = queryDB($mysqli, 'ruta, caption', 'imagen_producto','COD_PROD', $codProd, '');
        $infoImagen = array();
       
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);

            $images = array();
            $caption = array();
            foreach ($rows as $key => $row) {
                $infoImagen[$key] = ['imagen' => $row['ruta'], 'caption' => $row['caption']];
            }
            
        }
        return $infoImagen;     
    }

    function getAllProducts($mysqli){
        $result = queryDB($mysqli, 'COD_PROD, nombre', 'producto', 1, 1, 'ORDER BY Producto.COD_PROD ASC' );
       
        $rows = resultToArray($result);

        foreach ($rows as $key => $row) {
           $res_img = queryDB($mysqli, 'ruta', 'imagen_producto', 'COD_PROD', $row['COD_PROD'], '' );
           $rows_img = resultToArray($res_img);
           $productos[$key] = ['id' => $row['COD_PROD'], 'nombre' => $row['nombre'], 'imagen' => $rows_img[0]['ruta']];
        }

        return $productos;
    }

    function getComments($mysqli, $codProd){
        $result = queryDB($mysqli, 'cod_com, texto, username, fecha', 'Comentarios', 'COD_PROD', $codProd, 'ORDER BY FECHA DESC ');
       
        $comments = [];
        
        // echo "<script>console.log('Debug Objects: " . $result->num_rows . "' );</script>";
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);
            foreach ($rows as $key => $row) {
                $comments[$key] = ['id' => $row['cod_com'], 'texto' => $row['texto'], 'username' => $row['username'], 'fecha' => $row['fecha']];
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
    
    function getUsers($mysqli){
        $result = queryALL($mysqli, 'username, password, email, COD_ROL','usuarios', 'Usuarios.COD_ROL DESC');
        $rows = resultToArray($result);

        foreach ($rows as $key => $row) {
            $usuarios[$key] = ['username' => $row['username'], 'password' => $row['password'], 'email' => $row['email'], 'rol' => $row['COD_ROL']];
        }
        return $usuarios;
    }

    function getUser($mysqli, $field, $valor){
        $res = queryDB($mysqli, 'username, password, email, COD_ROL', 'usuarios', $field, $valor, '');

        if($res->num_rows > 0){
            $row = $res-> fetch_assoc();
            $user = array('username' => $row['username'], 'email' => $row['email'], 'password' => $row['password'], 'rol' => $row['COD_ROL']);
        }

        return $user;
    }

    function insertarUsuario($mysqli, $username, $email, $password){
        if(!$sentencia = $mysqli->prepare("INSERT INTO Usuarios(username, password, email) VALUES (?, ?, ?)")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        
        $sentencia->bind_param("sss", $username, $password, $email);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
    }

    function actualizarUsuario($mysqli, $columna, $otra_columna, $val_columna, $val_otra_columna){
        if(!$sentencia = $mysqli->prepare("UPDATE Usuarios set $columna = ? where $otra_columna = ?")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        
        $sentencia->bind_param("ss", $val_columna, $val_otra_columna);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
    }

    function insertarComentario($mysqli, $fecha, $texto, $username, $codProd){
        if(!$sentencia = $mysqli->prepare("INSERT INTO Comentarios(COD_COM, Fecha, texto, username, cod_prod) VALUES (NULL, ?, ?, ?, ?)")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("sssi", $fecha, $texto, $username, $codProd);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
    }
?>