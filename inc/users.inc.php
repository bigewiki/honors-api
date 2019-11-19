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
                    $query->next_result();
                    $query->fetch();
                    $query->free_result();
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
                        //add the API key
                        $query = $Api->query("CALL createKey($userId,'$newKey')");
                        // echo $creation;
                        // echo "----";
                        // echo $expiration;
                        //return the API key to the consumer
                        $Api->arrayToJson(array('key'=>$newKey));
                    } else {
                        $Api->badRequest('Incorrect username or password');
                    }
                } else {
                    $Api->badRequest('Incorrect username or password');
                }

            } else {
                $Api->badRequest('Missing params');
            }


            //get the user's id
            //validate the password
            //look for existing token and delete it
            //figure out the current server time, format in mysql datetime
            //figure out an expiration time for the token
            //generate a random alhpanumberic string to act as the token
            //create a hash for that token
            //insert the record for the token including the creation time, expiration time, a foreign key user id, and the token hash
            //return json, including the token
        }

        public function postRequest(){
            global $Api;
            switch($Api->getUri()[1]){
                case 'login':
                    $this->getToken();
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