<?php
declare(strict_types=1);
require_once('../../db.inc.php');

class ApiInit extends mysqli{
     public function getUri(){
        return explode("/",trim($_SERVER['REQUEST_URI']));
    }
}


// go global!!!

$ApiInit = new ApiInit($servername, $username, $password, $dbname);
if ($ApiInit->connect_error) {
    die("Connection failed: " . $ApiInit->connect_error);
}

function selectAll(string $table){
    global $ApiInit;
    $sanizedInput = $ApiInit->real_escape_string($table);
    $result = $ApiInit->query("SELECT * FROM $sanizedInput");
    return $result->fetch_assoc();
}






// print_r($ApiInit->getUri());

print_r(selectAll("comments"));


$ApiInit->close();


?>
