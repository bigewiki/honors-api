<?php
    declare(strict_types=1);
    //no time yet to create new user route
    //here a cheap way to set grey worm's pw to 'mypassword'
    //update users set password_hash = '$2y$12$FevWjgpAQILYB5FzdX4fDu/1.B93jLIZ/h9x1Cg3Q4FRGwv19pdxK' WHERE email = 'grey.worm@targaryen.net';

    class Users {
        private function getToken(){
            global $Api;
            //get POST params for username and password and sanitize
            if($_POST['username'] && $_POST['password']){
                $cleanUser = $Api->real_escape_string(trim($_POST['username']));
                $cleanPass = $Api->real_escape_string(trim($_POST['password']));
                //confirm that user exists
                $query = $Api->prepare("CALL getPasswordHash(?)");
                $query->bind_param('s',$cleanUser);
                $query->execute();
                $query->bind_result($storedHash);
                if($query->fetch()){
                    $query->free_result();
                    // $options = [ 'cost' => 16 ];
                    // $query2 = $Api->prepare("CALL getPasswordHash(?)");
                    // $query2->bind_param('i',$id);
                    // $query2->execute();
                    // $query2->bind_result($storedHash);
                    if(password_verify($_POST['password'], $storedHash)){
                        echo "verified";
                    } else {
                        echo "not verified";
                    }
    
                    // $hashpw = password_hash($cleanPass, PASSWORD_DEFAULT, $options);
                    //just in case we need to get the hash out for demo reasons
                    //echo "<h2>Hash of password: <br /> $hashpw</h2>";
                    
                    // password_verify($_POST['password'], $hashpw)
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