<?php
require_once('../ApiInit.php');

if($_SERVER['REQUEST_METHOD'] == "GET"){
    $result = $Api->query("SELECT fname, lname, getRole(role_id), email FROM users");
    while($row = $result->fetch_assoc()){
        $resultSet[] = $row;
    }
    echo json_encode($resultSet, JSON_PRETTY_PRINT);
}

$Api->close();

?>
