<?php
require_once('ApiInit.php');

// print_r($Api->getUri()[2]);

switch ($Api->getUri()[2]) {
    case "sprints":
        include_once 'inc/sprints.inc.php';
        break;
    default:
        include_once 'inc/404.inc.php';   
}


// $Api->selectAll("users");

$Api->close();

?>
