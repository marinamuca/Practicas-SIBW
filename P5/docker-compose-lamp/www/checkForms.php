<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";

    session_start();

    // AUTENTICACION
    function checkLogin($mysqli, $username, $password){
        $users = getUsers($mysqli);
        $errormsgs = ['username' => "", 'password' => ""];

        if(empty($username)){
            $errormsgs['username'] = "El nombre de usuario no puede estar vacio";
        }
        if(empty($password)){
            $errormsgs['password'] = "La contraseña no puede estar vacía";
        }

        if($errormsgs['username'] == "" && $errormsgs['password'] == ""){
            $existeUser = false;
            $contraseñaCorrecta = false;
            for ($i=0; $i < sizeof($users); $i++) { 
                if($users[$i]['username'] === $username){
                    $existeUser = true;
                    if(password_verify($password, $users[$i]['password'])){
                        $contraseñaCorrecta = true;
                    }
                } 
            }

            if(!$existeUser){
                $errormsgs['username'] = "El nombre de usuario introducido no es válido.";
            } 

            if(!$contraseñaCorrecta){
                $errormsgs['password'] = "La contraseña no es correcta.";
            }
        }

        return $errormsgs;
    }

    function checkRegister($mysqli, $username, $email, $password, $password_conf){
        $errormsgs = ['username' => "", 'email' => "", 'password' => "", 'password_conf' => ""];
        
        if(empty($username)){
            $errormsgs['username'] = "El nombre de usuario no puede estar vacio";
        }
        if(empty($email)){
            $errormsgs['email'] = "El correo electrónico no puede estar vacio";
        }
        if(empty($password)){
            $errormsgs['password'] = "La contraseña no puede estar vacía";
        }
        if(empty($password_conf)){
            $errormsgs['password_conf'] = "Debe confirmar su contraseña";
        }

        $user = getUser($mysqli, 'username', $username);
        if( $user != null )
            $errormsgs['username'] = "El nombre de usuario ya existe";
              
        if($password != $password_conf && $errormsgs['password']=="" && $errormsgs['password_conf']=="")
            $errormsgs['password'] = "Las contraseñas no coinciden";

        // $user = null;
        $user = getUser($mysqli, 'email', $email);
        if( $user != null )
            $errormsgs['email'] = "El email ya existe";
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            $errormsgs['email'] = "Formato de email invalido";
        
        return $errormsgs; 
    }

    function checkEditProfile($mysqli, $username, $email, $password_actual, $password_nueva, $password_conf, $user_Actual){
        $errormsgs = ['username' => "", 'email' => "", 'password' => "", 'password_conf' => ""];
        
        if(!empty($username)){
            $user = getUser($mysqli, 'username', $username);
            if( $user != null )
                $errormsgs['username'] = "El nombre de usuario ya existe";
        }

        if(!empty($email)){
            $user = getUser($mysqli, 'email', $email);
            if( $user != null )
                $errormsgs['email'] = "El email ya existe";
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                $errormsgs['email'] = "Formato de email invalido";
        }

        if(!empty($password_actual) || !empty($password_nueva) || !empty($password_conf)){
            $algunoVacio = false;
            if(empty($password_actual)){
                $algunoVacio = true;
                $errormsgs['password'] = "La contraseña no puede estar vacía";
            }
            if(empty($password_conf)){
                $algunoVacio = true;
                $errormsgs['password_conf'] = "Debe confirmar su contraseña";
            }
            if(empty($password_nueva)){
                $algunoVacio = true;
                $errormsgs['password_nueva'] = "La nueva contraseña no puede estar vacía";
            }

            if(!password_verify($password_actual, $user_Actual['password']) && !$algunoVacio){
                $errormsgs['password_actual'] = "La contraseña no es correcta";
            }

            if($password_nueva != $password_conf && !$algunoVacio)
                $errormsgs['password'] = "Las contraseñas no coinciden";
        }
       
        
        return $errormsgs; 
    }

    function checkImagen($img){
        $size_img = $img['size'];
        $file_type = $img['type'];
        $file_ext = strtolower(end(explode('.',$nombre_img)));
        $error = "";
        $extensions = array("jpeg", "jpg", "png");

        echo($file_ext);

        if($size_img > 2097152)
            $error = "Tamaño de la imagen demasiado grande";

        if(in_array($file_ext, $extensions) === false)
            $error = $file_ext;
        

        return $error;
    }

    function checkAddProduct($mysqli, $nombre, $img, $precio, $tamano, $papel, $descripcion, $caption){
        $errormsgs = ['nombre' => "", 'img' => "", 'caption' => "",'precio' => "", 'tamano' => "", 'papel' => "", 'descripción' => ""];

        if(empty($nombre)){
            $errormsgs['nombre'] = "El nombre no puede estar vacío";
        }
        if(empty($precio)){
            $errormsgs['precio'] = "Debe introducir un precio";
        }
    
        if($img['size'] > 0){
            $errormsgs['img'] = checkImagen($img);
            if($errormsgs['img'] == ""){
                if(empty($caption))
                    $errormsgs['caption'] = "Debe añadir un pie de foto.";
                else {
                    $nombre_img = $img['name'];
                    move_uploaded_file( $tmp_img = $img['tmp_name'], "static/image/" . $nombre_img );
                }
            }
        }
        
        if(!empty($nombre)){
            $id = getIDProductByName($mysqli, $nombre);
            if( $id != -1 )
                $errormsgs['nombre'] = "El nombre de producto ya existe";
        }
        
        return $errormsgs; 
    }

    function checkEditProduct($mysqli, $nombre, $img, $precio, $tamano, $papel, $descripcion, $caption){
        $errormsgs = ['nombre' => "", 'img' => "", 'caption' => "",'precio' => "", 'tamano' => "", 'papel' => "", 'descripción' => ""];
    
        if($img['size'] > 0){
            $errormsgs['img'] = checkImagen($img);
            if($errormsgs['img'] == ""){
                if(empty($caption))
                    $errormsgs['caption'] = "Debe añadir un pie de foto.";
                else {
                    $nombre_img = $img['name'];
                    move_uploaded_file( $tmp_img = $img['tmp_name'], "static/image/" . $nombre_img );
                }
            }
        }
        
        if(!empty($nombre)){
            $id = getIDProductByName($mysqli, $nombre);
            if( $id != -1 )
                $errormsgs['nombre'] = "El nombre de producto ya existe";
        }
        
        return $errormsgs; 
    }

    
?>