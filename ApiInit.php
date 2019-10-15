<?php
declare(strict_types=1);
require_once('../../db.inc.php');

class ApiInit extends mysqli{
     public function getUri(){
        return explode("/",trim($_SERVER['REQUEST_URI']));
    }

    public function selectAll(string $inputTable){
        $sanizedInput = $this->real_escape_string($inputTable);
        $result = $this->query("SELECT * FROM $sanizedInput");
        while($row = $result->fetch_assoc()){
            $resultSet[] = $row;
        }
        echo json_encode($resultSet, JSON_PRETTY_PRINT);
    }
}

$Api = new ApiInit($servername, $username, $password, $dbname);
if ($Api->connect_error) {
    die("Connection failed: " . $Api->connect_error);
}
?>
