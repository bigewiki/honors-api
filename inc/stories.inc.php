<?php
//declare strict types


class Stories{
    private function getFullStory($route) {
        global $Api;
        $output = array('tasks'=>null, 'comments'=>null);
    
        $multiQuery = implode(";",array(
            "CALL displayStory($route)",
            "CALL displayStoryTasks($route)",
            "CALL displayStoryComments($route)"
        ));

        if ($Api->multi_query($multiQuery)) {
            $i = 1;
            do {
                if ($result = $Api->store_result()) {
                    if ($i == 1) {
                        while ($row = $result->fetch_assoc()) {
                            $output[] = $row;
                        }
                        $output = $output[0];
                    } else if ($i == 2) {
                        while ($row = $result->fetch_assoc()) {
                            $output['tasks'][] = $row;
                        }
                    } else if ($i == 3) {
                        while ($row = $result->fetch_assoc()) {
                            $output['comments'][] = $row;
                        }
                    }
                    $i++;
                    $result->free();
                }
            } while ($Api->next_result());
        }

        $Api->arrayToJson($output);
    }

    public function getRequest(){
        global $Api;
        $route = $Api->getUri()[1];
        switch($route){
            case null:
                $Api->selectAll('stories');
                break;
            default:
                if(is_numeric($route)){
                    $this->getFullStory($route);
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