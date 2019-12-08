<?php
$hostname = $_SERVER['HTTP_HOST'];

if( $hostname == 'localhost'){
    require_once('/var/www/db.inc.php');
    $docRoot = '/edward/honors-api/';
} else if($hostname == 'nbtl.mesacc.edu') {
    require_once('/home/stu/superuser/db.inc.php');
    $docRoot = '/superuser/honors-api/';
} else if($hostname == 'muniz.dev'){
    require_once('/home/muniz/db.inc.php');
    $docRoot = '/honors-api/v1/';
}

?>