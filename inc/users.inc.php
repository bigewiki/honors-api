<?php
    declare(strict_types=1);
    //no time yet to create new user route

    //get a password hash
    // $options = [ 'cost' => 16 ];
    // $hashpw = password_hash($cleanPass, PASSWORD_DEFAULT, $options);
    //echo "<h2>Hash of password: <br /> $hashpw</h2>";
    
    //here a cheap way to set a password in the db, 
    //update users set password_hash = 'PASSWORDHASH' WHERE email = 'grey.worm@targaryen.net';

    class Users {
        private function getToken(){
            global $Api;
            //get POST params for username and password and sanitize
            if($_POST['username'] && $_POST['password']){
                $cleanUser = $Api->real_escape_string(trim($_POST['username']));
                $cleanPass = $Api->real_escape_string(trim($_POST['password']));
                //confirm that user exists
                $query = $Api->prepare("CALL getPasswordHash(?);");
                $query->bind_param('s',$cleanUser);
                $query->execute();
                $query->bind_result($userId,$storedHash);
                if($query->fetch()){
                    $query->close();
                    //if password validates
                    if(password_verify($_POST['password'], $storedHash)){
                        //generate the api key
                        $permitted_chars = 'abcdefghijklmnopqrstuvwxyz';
                        $newKey = '';
                        for ( $i = 1; $i <= 40; $i++){
                            $randIndex = random_int(0,25);
                            $newKey.=substr($permitted_chars,$randIndex,1);
                            $newKey.=random_int(0,9);
                        }
                        $newKey = substr(str_shuffle($newKey), 0, 40);
                        //get a randomized prefix (sort of a signature to help grab it from the db)
                        $prefix = '';
                        for ( $i = 1; $i <= 20; $i++){
                            $randIndex = random_int(0,25);
                            $prefix.=substr($permitted_chars,$randIndex,1);
                            $prefix.=random_int(0,9);
                        }
                        $prefix = substr(str_shuffle($prefix), 0, 10);
                        //add the API key and get the creation/expiration
                        $keyHash = password_hash($newKey,PASSWORD_DEFAULT,[ 'cost' => 16]);
                        //combine the prefix and the hash to be sent to the DB
                        $keyHash = $prefix.".".$keyHash;
                        //send the combined prefix + hash
                        $query = $Api->prepare("CALL createKey(?,?)");
                        $query->bind_param('is',$userId,$keyHash);
                        $query->execute();
                        $query->bind_result($creation,$expiration);
                        //return the API key to the consumer
                        if($query->fetch()){
                            $Api->arrayToJson(array('creation'=>$creation,'expiration'=>$expiration,'token'=>$prefix.".".$newKey));
                        }
                    } else {
                        $Api->badRequest('Incorrect username or password');
                    }
                } else {
                    $Api->badRequest('Incorrect username or password');
                }

            } else {
                $Api->badRequest('Missing params');
            }
        }

        private function checkToken(){
            global $Api;
            $Api->checkToken();
        }

        public function postRequest(){
            global $Api;
            switch($Api->getUri()[1]){
                case 'login':
                    $this->getToken();
                    break;
                case 'check-token':
                    $this->checkToken();
                    break;
                default:
                    $Api->forbidden();
            }
        }
    }

    $Users = new Users();

    switch($Api->getMethod()){
        case "POST":
            $Users->postRequest();
            break;
        default:
            $Api->badMethod();
    }



?>