<?php
require_once('ApiInit.inc.php');

switch ($Api->getUri()[0]) {
    case "sprints":
        include_once 'inc/sprints.inc.php';
        break;
    case "stories":
        include_once 'inc/stories.inc.php';
        break;
    case "users":
        include_once 'inc/users.inc.php';
        break;
    default:
        $Api->notFound();
}

$Api->close();

?>
