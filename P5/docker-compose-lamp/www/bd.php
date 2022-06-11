<?php
    function queryDB($mysqli, $fields, $table, $id, $codigo, $order){
        // $codProd = $mysqli->real_escape_string($codProd); // Para evitar inyección de codigo

        $sentencia = $mysqli->prepare("SELECT $fields FROM $table WHERE $id = ? $order");

        if($table == "usuarios" || $id == "nombre"){
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
        
        $result = $mysqli->query("SELECT $fields FROM $table $order");
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

    function getCommentsFromProduct($mysqli, $codProd){
        $result = queryDB($mysqli, 'cod_com, texto, username, fecha, editado', 'Comentarios', 'COD_PROD', $codProd, 'ORDER BY FECHA DESC ');
       
        $comments = [];
        
        // echo "<script>console.log('Debug Objects: " . $result->num_rows . "' );</script>";
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);
            foreach ($rows as $key => $row) {
                $comments[$key] = ['id' => $row['cod_com'], 'texto' => $row['texto'], 'username' => $row['username'], 'editado' => $row['editado'], 'fecha' => $row['fecha']];
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
    
    function getRoles($mysqli){
        $result = queryALL($mysqli, 'COD_ROL, nombre','Roles');
        $rows = resultToArray($result);

        foreach ($rows as $key => $row) {
            $roles[$key] = ['codigo' => $row['COD_ROL'], 'nombre' => $row['nombre']];
        }
        return $roles;
    }

    function getUsers($mysqli){
        $result = queryALL($mysqli, 'username, password, email, COD_ROL','usuarios', ' ORDER BY Usuarios.COD_ROL DESC');
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

    function updateComentario($mysqli, $texto, $cod_comm){
        if(!$sentencia = $mysqli->prepare("UPDATE Comentarios set texto = ?, editado = 1 where cod_com = ?")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("si", $texto, $cod_comm);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        } 

        $sentencia->close();
    }

    function deleteComentario($mysqli, $cod_comm){
        if(!$sentencia = $mysqli->prepare("DELETE FROM `Comentarios` WHERE `Comentarios`.`COD_COM` = ?")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("i", $cod_comm);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
    }


    function deleteProducto($mysqli, $codProd){
        if(!$sentencia = $mysqli->prepare("DELETE FROM `Producto` WHERE `Producto`.`COD_PROD` = ?")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("i", $codProd);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
    }

    function numSuperUsers($mysqli){
        $res = queryDB($mysqli, 'username, password, email, COD_ROL', 'usuarios', 'COD_ROL', 'SU', '');
        return $res->num_rows;
    }

    function getAllComments($mysqli){
        $result = queryALL($mysqli, 'COD_COM, fecha, texto, username, cod_prod, editado','Comentarios');
        $rows = resultToArray($result);

        foreach ($rows as $key => $row) {
            $usuarios[$key] = ['username' => $row['username'], 'id' => $row['COD_COM'], 'fecha' => $row['fecha'], 'texto' => $row['texto'], 'editado' => $row['editado'], 'producto' => $row['cod_prod']];
        }
        return $usuarios;
    }

    function getComentario($mysqli, $cod_comm){
        $res = queryDB($mysqli, 'texto', 'Comentarios', 'cod_com', $cod_comm, '');

        if($res->num_rows > 0){
            $row = $res-> fetch_assoc();
            $comentario = array('texto' => $row['texto']);
        }

        return $comentario;
    }

    function getIDProductByName($mysqli, $name){
        $res = queryDB($mysqli, 'COD_PROD', 'Producto', 'nombre', $name, '');

        $id = -1;
        if($res->num_rows > 0){
            $row = $res-> fetch_assoc();
            $id = $row['COD_PROD'];
        } 

        return $id;
    }

    function insertarProducto($mysqli, $nombre, $precio, $tamano, $papel, $descripcion){
        if(!$sentencia = $mysqli->prepare("INSERT INTO Producto(COD_PROD, nombre, precio, tamano, tipo_papel, descripcion) VALUES (NULL, ?, ?, ?, ?, ?)")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("sisss", $nombre, $precio, $tamano, $papel, $descripcion);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $id= getIDProductByName($mysqli, $nombre);
        
        $sentencia->close();


        return $id;
    }

    function insertarImagen($mysqli, $codProd, $nombre, $caption){
        if(!$sentencia = $mysqli->prepare("INSERT INTO Imagen_Producto(COD_PROD, COD_IMG, ruta, caption) VALUES (?, NULL, ?, ?)")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("iss", $codProd, $nombre, $caption);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }

        $sentencia->close();
  
    }

    function updateProducto($mysqli, $field, $value, $type, $codProd){
        if(!$sentencia = $mysqli->prepare("UPDATE Producto set $field = ? where cod_prod = ?")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param($type."i", $value, $codProd);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        } 

        $sentencia->close();
    }

    function addTag($mysqli, $codProd, $texto){
        if(!$sentencia = $mysqli->prepare("INSERT INTO Etiquetas(COD_PROD, etiqueta, COD_TAG) VALUES (?, ?, NULL)")){
            echo "Falló la preparación. (" . $mysqli->errno . ") " . $mysqli->error;
        }
        $sentencia->bind_param("is", $codProd, $texto);

        if (!$sentencia->execute()) {
            echo "Falló la ejecución: (" . $sentencia->errno . ") " . $sentencia->error;
        }
        
        $sentencia->close();


    }

    function getTagsFromProduct($mysqli, $codProd){
        $result = queryDB($mysqli, 'COD_TAG, etiqueta', 'Etiquetas', 'COD_PROD', $codProd, ' ');
       
        $tags = [];
        
        // echo "<script>console.log('Debug Objects: " . $result->num_rows . "' );</script>";
        if($result->num_rows > 0){
            
            $rows = resultToArray($result);
            foreach ($rows as $key => $row) {
                $tags[$key] = ['id' => $row['COD_TAG'], 'tag' => $row['etiqueta']];
            }
        } 

        return $tags;
    }
?>