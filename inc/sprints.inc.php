<?php

class Sprints{
    public function getRequest(){
        global $Api;
        switch($Api->getUri()[1]){
            case null:
                $Api->selectAll('sprints');
                break;
            case "current":
                $Api->selectToJson('CALL displaySprint(currentSprint())');
                break;
            case "last":
                $Api->selectToJson('CALL displaySprint(lastSprint())');
                break;
            case "next":
                $Api->selectToJson('CALL displaySprint(nextSprint())');
                break;
            case "future":
                $Api->selectToJson('CALL displaySprint(futureSprint())');
                break;
            default:
                $Api->notFound();
        }
    }
}
$Sprints = new Sprints();

switch($Api->getMethod()){
    case "GET":
        $Sprints->getRequest();
        break;
    default:
        $Api->badMethod();
}

?>