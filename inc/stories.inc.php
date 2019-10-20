<?php

class Stories{
    public function getRequest(){
        global $Api;
        $route = $Api->getUri()[3];
        switch($route){
            case null:
                $Api->selectAll('stories');
                break;
            default:
                if(is_numeric($route)){
                    $tasks = $Api->getSqlArray("call displayStoryTasks($route)");
                    $output = array("tasks"=>$tasks);
                    $Api->arrayToJson($output);
                } else {
                    $Api->notFound();
                }
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