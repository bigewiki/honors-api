<?php


switch($Api->getMethod()){
    case "GET":
        echo "hello GET";
        break;
    default:
        $Api->badMethod();
}