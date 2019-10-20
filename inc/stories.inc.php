<?php

class Stories{
    public function getRequest(){
        global $Api;
        switch($Api->getUri()[3]){
            case null:
                $Api->selectAll('stories');
                break;
            default:
                $Api->notFound();
        }
    }
}
$Stories = new Stories();

switch($Api->getMethod()){
    case "GET":
        $Stories->getRequest();
        break;
    default:
        $Api->badMethod();
}

?>