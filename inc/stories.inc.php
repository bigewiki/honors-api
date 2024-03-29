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

    public function patchRequest(){
        global $Api;
        $route = $Api->getUri()[1];
        switch($route){
            case null:
                $Api->forbidden();
                break;
            case (is_numeric($route)):
                $this->patchStory(intval($route));
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
            $requestBody = json_decode(file_get_contents('php://input'));
            //err if missing name param
            if(!$requestBody->name){
                $Api->badRequest('Story name parameter missing');
            } else {
                $query = $Api->prepare("CALL createStory(?,?,?,?,?,?)");
                $cleanRequest = $Api->sanitizeAssoc((array)$requestBody);

                $query->bind_param(
                    'sssiii',
                    $cleanRequest['name'],
                    $cleanRequest['description'],
                    $cleanRequest['priority'],
                    $cleanRequest['dependency'],
                    $cleanRequest['time-size'],
                    $cleanRequest['epic-id']
                );
                $query->execute();

                $query->bind_result($id,$name,$description,$owner,$sprint_id,$priority,$dependency,$time_size,$epic_id,$status);
        
                while($query->fetch()){
                    $newRecord['id'] = $id;
                    $newRecord['name'] = $name;
                    $newRecord['description'] = $description;
                    $newRecord['owner'] = $owner;
                    $newRecord['sprint_id'] = $sprint_id;
                    $newRecord['priority'] = $priority;
                    $newRecord['dependency'] = $dependency;
                    $newRecord['time_size'] = $time_size;
                    $newRecord['epic_id'] = $epic_id;
                    $newRecord['status'] = $status;
                }
                $query->free_result();
        
                if($newRecord['id']){
                    header("HTTP/1.0 201 Created");
                    $result = array("success"=>1,"notice"=>"Record created","res"=>$newRecord);
                    echo json_encode($result, JSON_PRETTY_PRINT);
                } else {
                    $Api->badRequest('No record created, please check your parameters');
                }
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

    private function patchStory(int $route) {
        global $Api;
        if($Api->checkToken()['valid']){
            $requestBody = json_decode(file_get_contents('php://input'));
            $cleanRequest = $Api->sanitizeAssoc((array)$requestBody);
            $query = $Api->prepare("CALL patchStory(?,?,?,?,?,?,?,?,?,?)");
            $query->bind_param(
                'issiisiiis',
                $route,
                $cleanRequest['name'],
                $cleanRequest['description'],
                $cleanRequest['owner'],
                $cleanRequest['sprint'],
                $cleanRequest['priority'],
                $cleanRequest['dependency'],
                $cleanRequest['size'],
                $cleanRequest['epic'],
                $cleanRequest['status']
            );
            $query->execute();
            if($query->error == "Story not found"){
                $Api->notFoundMsg('Story not found');
            } else {
                $this->getFullStory($route);
            }
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
    case "PATCH":
        $Stories->patchRequest();
        break;
    default:
        $Api->badMethod();
}

?>