<?php
require_once('ApiInit.php');

// print_r($Api->getUri()[2]);

switch ($Api->getUri()[2]) {
    case "sprints":
        include_once 'inc/sprints.inc.php';
        break;
    default:
        $Api->notFound();   
}


// $Api->selectAll("users");

$Api->close();

?>
