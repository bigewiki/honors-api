<?php
declare(strict_types=1);
$hostname = $_SERVER['HTTP_HOST'];

if( $hostname == "localhost"){
    require_once('/var/www/db.inc.php');
} else {
    echo "error, update db.inc location";
}

class ApiInit extends mysqli{
    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(){
        $array = explode("/",trim($_SERVER['REQUEST_URI']));
        array_shift($array);
        return $array;
    }

    public function notFound(){
        header("HTTP/1.0 404 Not Found");
        $result = array("success"=>0,"error"=>"Invalid Route");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function badMethod(){
        header("HTTP/1.0 405 Method Not Allowed");
        $result = array("success"=>0,"error"=>"Invalid Method");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function selectAll(string $inputTable){
        $sanizedInput = $this->real_escape_string($inputTable);
        $result = $this->query("SELECT * FROM $sanizedInput");
        while($row = $result->fetch_assoc()){
            $resultSet[] = $row;
        }
        echo json_encode($resultSet, JSON_PRETTY_PRINT);
    }

    public function selectToJson(string $sql){
        $result = $this->query($sql);
        if ($result == null || $result->num_rows == 0){
            $resultSet = array("success"=>0,"error"=>"Empty Set");
        } else {
            while($row = $result->fetch_assoc()){
                $resultSet[] = $row;
            }
        }
        echo json_encode($resultSet, JSON_PRETTY_PRINT);
    }
}

$Api = new ApiInit($servername, $username, $password, $dbname);
if ($Api->connect_error) {
    die("Connection failed: " . $Api->connect_error);
}
?>
