<?php
declare(strict_types=1);
require('../../db.inc.php');

class apiInit extends mysqli{
    function getUri(){
        return explode("/",trim($_SERVER['REQUEST_URI']));
    }
}

$apiInit = new apiInit($servername, $username, $password, $dbname);
if ($apiInit->connect_error) {
    die("Connection failed: " . $apiInit->connect_error);
}

$sql = "select * from users";
$result = mysqli_query($apiInit, $sql);
print_r(mysqli_fetch_assoc($result));
$apiInit->close();


?>
