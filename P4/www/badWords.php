<?php
 require_once "/usr/local/lib/php/vendor/autoload.php";
 include("bd.php");

 $msqli = dbConnect();
 header('Content-type: application/json; charset=utf-8');
 echo json_encode(getBadWords($msqli), JSON_FORCE_OBJECT);
    
?>