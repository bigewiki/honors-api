<?php
$hostname = $_SERVER['HTTP_HOST'];

if( $hostname == "localhost"){
    require_once('/var/www/db.inc.php');
    $docRoot = "/edward/honors-api/";
} else {
    require_once('/home/muniz/db.inc.php');
}

?>