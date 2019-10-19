<?php
    header("HTTP/1.0 404 Not Found");
    $result = array("success"=>0,"error"=>"Invalid Route");
    echo json_encode($result, JSON_PRETTY_PRINT);