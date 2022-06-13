<?php
    require_once "/usr/local/lib/php/vendor/autoload.php";
    include("bd.php");
    session_start();

    $mysqli = dbConnect();

    $user = getUser($mysqli, 'username', $_SESSION['username']);
    header('Content-type: application/json; charset=utf-8');
    if(isset($_GET['input'])){
        $input = $_GET['input'];
        if($user['rol'] != 'SU' || $user['rol'] == 'GEST')
            echo json_encode(searchProduct($mysqli, $input, true), JSON_FORCE_OBJECT);
        else {
            echo json_encode(searchProduct($mysqli, $input, false), JSON_FORCE_OBJECT);
        }
    }
?>