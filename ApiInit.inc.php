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
        $result = array("success"=>0,"err"=>"Invalid Route");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function badMethod(){
        header("HTTP/1.0 405 Method Not Allowed");
        $result = array("success"=>0,"err"=>"Invalid Method");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function forbidden(){
        header("HTTP/1.0 403 Forbidden");
        $result = array("success"=>0,"err"=>"Not Allowed");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function selectAll(string $inputTable){
        if($inputTable == 'users'){
            $this->forbidden();
        } else {
            $sanizedInput = $this->real_escape_string($inputTable);
            $this->selectToJson("SELECT * FROM $sanizedInput");
        }
    }

    public function selectToJson(string $sql){
        $query = $this->query($sql);
        if ($query == null || $query->num_rows == 0){
            $output = array("success"=>0,"err"=>"Empty Set");
        } else {
            while($row = $query->fetch_assoc()){
                $res[] = $row;
            }
            $output = array("success"=>1,"res"=>$res);
        }
        echo json_encode($output, JSON_PRETTY_PRINT);
    }

    public function getSqlArray(string $sql): array{
        $query = $this->query($sql);
        while($row = $query->fetch_assoc()){
            $res[] = $row;
        }
        return $res;
    }

    public function arrayToJson(array $inputArr){
        $output = array("success"=>1,"res"=>$inputArr);
        echo json_encode($output, JSON_PRETTY_PRINT);
    }
}

$Api = new ApiInit($servername, $username, $password, $dbname);
if ($Api->connect_error) {
    die("Connection failed: " . $Api->connect_error);
}
?>
