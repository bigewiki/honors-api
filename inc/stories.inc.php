<?php
declare(strict_types=1);

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
                    $this->getFullStory(intval($route));
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

    public function deleteRequest(){
        global $Api;
        $route = $Api->getUri()[1];
        switch($route){
            case null:
                $Api->forbidden();
                break;
            case (is_numeric($route)):
                $this->deleteStory(intval($route));
                break;
            default:
                $Api->forbidden();
        }
    }

    private function getFullStory(int $route) {
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
        //check token
        if($Api->checkToken()['valid']){
            //err if missing name param
            if(!$_POST['name']){
                $Api->badRequest('Story name parameter missing');
            } else {
                $query = $Api->prepare("CALL createStory(?,?,?,?,?,?)");

                $sanitizedAssoc = $Api->sanitizeAssoc($_POST);

                $query->bind_param(
                    'sssiii',
                    $sanitizedAssoc['name'],
                    $sanitizedAssoc['description'],
                    $sanitizedAssoc['priority'],
                    $sanitizedAssoc['dependency'],
                    $sanitizedAssoc['time-size'],
                    $sanitizedAssoc['epic-id']
                );
                $Api->insertRecord($query);
            }
        } else {
            $Api->badRequest($Api->checkToken()['message']);
        }

    }

    private function deleteStory(int $route) {
        global $Api;
        if($Api->checkToken()['valid']){
            global $Api;
            $query = $Api->prepare("CALL deleteStory(?)");
            $query->bind_param('i',$route);
            $Api->deleteRecord($query,$route,'stories');
        } else {
            $Api->badRequest($Api->checkToken()['message']);
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
    case "DELETE":
        $Stories->deleteRequest();
        break;
    default:
        $Api->badMethod();
}

?>