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

    function checkEdit($mysqli, $username, $email, $password_actual, $password_nueva, $password_conf, $user_Actual){
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
?>