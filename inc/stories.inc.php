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
                    $query  = "CALL displayStory($route);";
                    $query .= "CALL displayStoryTasks($route)";
                    //get the query result in an array
                    if ($Api->multi_query($query)) {
                        do {
                            if ($result = $Api->store_result()) {
                                while ($row = $result->fetch_assoc()) {
                                    $results[] = $row;
                                }
                                $result->free();
                            }
                        } while ($Api->next_result());
                    }
                    $output = $results[0];
                    array_shift($results);
                    $output['tasks']=$results;
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