<?php
//declare strict types


class Stories{
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

    public function postRequest(){
        global $Api;
        $route = $Api->getUri()[1];
        switch($route){
            case null:
                $this->createStory();
                break;
            default:
                $Api->forbidden();
        }
    }

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

        if(isset($output)){
            $Api->arrayToJson($output);
        } else {
            $Api->notFoundMsg('No record found');
        }
    }

    private function createStory() {
        global $Api;
        //err if missing name param
        if(!$_POST['name']){
            $Api->badRequest('Story name parameter missing');
        } else {
            //success but check for other params
            $name = "'".$_POST['name']."'";
            //check for missing description
            if(!$_POST['description']){
                $description = 'null';
            } else {
                $description = "'".$_POST['description']."'";
            }
            //check for missing priority
            if(!$_POST['priority']){
                $priority = 'null';
            } else {
                $priority = "'".$_POST['priority']."'";
            }
            //check for missing dependency
            if(!$_POST['dependency']){
                $dependency = 'null';
            } else {
                $dependency = $_POST['dependency'];
            }
            //check for missing time-size
            if(!$_POST['time-size']){
                $timeSize = 'null';
            } else {
                $timeSize = $_POST['time-size'];
            }
            //check for missing epic-id
            if(!$_POST['epic-id']){
                $epicId = 'null';
            } else {
                $epicId = $_POST['epic-id'];
            }
            $Api->insertRecord("CALL createStory($name,$description,$priority,$dependency,$timeSize,$epicId)");
        }
    }


}
$Stories = new Stories();

switch($Api->getMethod()){
    case "GET":
        $Stories->getRequest();
        break;
    case "POST":
        $Stories->postRequest();
        break;
    default:
        $Api->badMethod();
}

?>