<?php

class Sprints{
    public function getRequest(){
        global $Api;
        switch($Api->getUri()[3]){
            case null:
                $Api->selectAll('sprints');
                break;
            case "current":
                $Api->selectToJson('
                    SELECT *
                    FROM sprints sp JOIN stories st ON (st.sprint_id=sp.id)
                    JOIN users u ON (st.owner=u.id)
                    WHERE sp.id = currentSprint()
                ');
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