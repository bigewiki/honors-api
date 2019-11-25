<?php
declare(strict_types=1);

require_once('config.inc.php');

class ApiInit extends mysqli{
    public function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getUri(){
        global $docRoot;
        $uri = $_SERVER['REQUEST_URI'];
        $pattern = '/' . preg_replace('|/|','\/',$docRoot) . '/';
        $array = explode("/",preg_replace($pattern,'',$uri));
        return $array;
    }

    public function badRequest(string $msg){
        header("HTTP/1.0 400 BAD REQUEST");
        $result = array("success"=>0,"err"=>$msg);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function notFound(){
        header("HTTP/1.0 404 Not Found");
        $result = array("success"=>0,"err"=>"Invalid Route");
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function notFoundMsg(string $msg){
        header("HTTP/1.0 404 Not Found");
        $result = array("success"=>0,"err"=>$msg);
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

    public function fiveHundred(string $msg){
        header("HTTP/1.0 500 Internal Server Error");
        $result = array("success"=>0,"err"=>$msg);
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function sanitizeAssoc(array $inputArr){
        foreach($inputArr as $index => $value){
            $inputArr[$index] = $this->real_escape_string(htmlentities(trim($value)));
        }
        return $inputArr;
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

    public function insertRecord(object $query){
        $query->execute();

        $query->bind_result($id,$name,$description,$owner,$sprint_id,$priority,$dependency,$time_size,$epic_id,$status);

        while($query->fetch()){
            $newRecord['id'] = $id;
            $newRecord['name'] = $name;
            $newRecord['description'] = $description;
            $newRecord['owner'] = $owner;
            $newRecord['sprint_id'] = $sprint_id;
            $newRecord['priority'] = $priority;
            $newRecord['dependency'] = $dependency;
            $newRecord['time_size'] = $time_size;
            $newRecord['epic_id'] = $epic_id;
            $newRecord['status'] = $status;
        }
        $query->free_result();

        if($newRecord['id']){
            header("HTTP/1.0 201 Created");
            $result = array("success"=>1,"notice"=>"Record created","res"=>$newRecord);
            echo json_encode($result, JSON_PRETTY_PRINT);
        } else {
            $this->badRequest('No record created, please check your parameters');
        }
    }

    public function deleteRecord(object $query, int $targetId, string $tableName){
        $query->execute();
        if($query->affected_rows == 1){
            $result = array("success"=>1,"notice"=>"Record $targetId deleted from $tableName");
            echo json_encode($result, JSON_PRETTY_PRINT);
        } else if($query->affected_rows == 0){
            $this->notFoundMsg("Record $targetId could not be deleted, not found in $tableName");
        }
    }

    public function checkToken(){
        $fullToken = $_POST['token'];
        if($fullToken){
            $prefix = explode(".",$fullToken)[0];
            $prefix = $this->real_escape_string(trim($prefix));
            $query = $this->prepare("CALL checkKey(?)");
            $query->bind_param('s',$prefix);
            $query->execute();
            $query->bind_result($storedValue);
            if($query->fetch()){
                $secret = explode($prefix.".",$fullToken)[1];
                $storedHash = explode($prefix.".",$storedValue)[1];
                if(password_verify($secret, $storedHash)){
                    $this->arrayToJson(array("message"=>"Your token is valid"));
                } else {
                    $this->badRequest('Sorry, token is invalid or expired');
                }
            } else {
                $this->badRequest('Sorry, token is invalid or expired');
            }
        } else {
            $this->badRequest('Missing token');
        }
    }
}

$Api = new ApiInit($servername, $username, $password, $dbname);
if ($Api->connect_error) {
    die("Connection failed: " . $Api->connect_error);
}

?>


