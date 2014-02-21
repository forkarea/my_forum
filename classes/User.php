<?php
include_once "./database_connection.php";
include_once "./common_functions.php";
if (PHP_VERSION_ID < 50500) {
        //fallback for functions unavailable in PHP <5.5
        include_once "./password_compat/lib/password.php";
}

        class User {
                public $user_id;
                public $login;
                public $password_hash;
                public $creation_date;

                //PDO::FETCH_CLASS mode suggests a non-parametric constructor
                //Therefore easy to use method for filling all fields requires a separate function
                public static function construct ($user_id, $login, $password_hash, $creation_date) {
                        $u = new User();
                        $u->user_id = $user_id;
                        $u->login = $login;
                        $u->password_hash = $password_hash;
                        $u->creation_date = $creation_date;
                        return $u;
                }

                public function create_login_cookie ($dbh) {
                        var_dump($this);
                        var_dump($_COOKIE);
                        try {
                                $login_token = openssl_random_pseudo_bytes(10);
                                $login_token = base64_encode($login_token);
                                
                                $stmt = $dbh->prepare("update users set login_token = :login_token where user_id = :id");
                                $stmt->bindParam(":login_token", $login_token);
                                $stmt->bindParam(":id", $this->user_id);
                                if (!$stmt->execute())
                                        return "Cannot execute statement";
                        } catch (PDOException $ex) {
                                return "Cannot connect to database";
                        }                    
                        $r1 = setcookie("login", $this->login, time() + 30*24*3600/*, 'localhost', '/', FALSE, TRUE*/);
                        if (!$r1) {
                                return "Cannot set login cookie.";
                        }
                        $r2 = setcookie("login_cookie", $login_token, time() + 30*24*3600/*, 'localhost', '/', FALSE, TRUE*/);
                        
                        if (!$r2) {
                                return "Cannot set login_cookie cookie.";
                        }
                        
                        //var_dump($this);

                        return $r1 && $r2; 
                }
        };
