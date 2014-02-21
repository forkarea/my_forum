<?php
        
include_once "database_connection.php";
include_once "common_functions.php";
if (PHP_VERSION_ID < 50500) {
        //fallback for functions unavailable in PHP <5.5
        include_once "password_compat/lib/password.php";
}

        class UserManager {
                private $error = NULL;

                public static function get_empty_error_state() {
                        return array('login_error' => NULL, 
                                         'password_error' => NULL,
                                         'password_repeat_error' =>NULL,
                                         'error' => NULL);
                }

                private function check_new_user_parameters($dbh, $login, $password, $password_repeat) {
                        $ret = $this->get_empty_error_state();
                        $ok = true;

                        $login_length = mb_strlen($login, "UTF-8");
                        if ($login_length < 4) {
                               $ret['login_error'] = "The user login is too short (min. 4 chars)"; 
                               $ok = false;
                        } else if ($login_length >= 20) {
                               $ret['login_error'] = "The user login is too long (max. 20 chars)"; 
                               $ok = false;
                        } else if (!is_valid_utf8($login)) {
                               $ret['login_error'] = "The user login is not a valid UTF-8 string"; 
                               $ok = false;
                       //check for allowed characters in the user login
                       //allowed are: Unicode letters, digits and
                        } else if (preg_match('/^(\p{L}|[-0-9_])*$/u', $login) !== 1) {
                                $ret['login_error'] = "The user login contains invalid characters (only letters (including multilingual), digits, hyphen and underscore allowed).";
                                $ok = false;
                        } else {
                                try {
                                        $stmt = $dbh->prepare("SELECT user_id FROM users WHERE login=:login");
                                        $stmt->bindParam(":login", $login);
                                        $stmt->execute();

                                        $row = $stmt->fetch();
                                        if ($row != NULL) {
                                                $ret['login_error'] = "The user login already exists";
                                                $ok = false;
                                        }
                                } catch (PDOException $ex) {
                                        $ret['error'] = "Cannot connect to the database.";
                                        $ok = false;
                                }
                        }

                        if ($password !== $password_repeat) {
                               $ret['password_repeat_error'] = "The passwords don't match"; 
                               $ok = false;
                        }

                        if (strlen($password) < 10) {
                                $ret['password_error'] = "The password is too short (minimum 10 characters)";
                               $ok = false;
                        } 

                        if ($ok !== true) {
                                return $ret;
                        }

                        return NULL;
                }

                public function create_user($dbh, $login, $password, $password_repeat) {
                        $ret = $this->check_new_user_parameters($dbh, $login, $password, $password_repeat);
                        if (!is_null($ret)) {
                                $this->error = $ret;
                                return NULL;
                        }

                        $ret = $this->get_empty_error_state();

                        //PHP manual suggests to store hashes in a column 255 chars wide 
                        $options = array( 'cost' => 11 );
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT, $options); 
                        if ($hashed_password === FALSE) {
                                $ret['error'] = "Cannot create password hash!";
                                $this->error =  $ret;
                                return NULL;
                        } 

                        try {
                                $stmt = $dbh->prepare("insert into users (login, password_hash, time) values (:login, :password_hash, :time)");
                                $stmt->bindParam(":login", $login);
                                $stmt->bindParam(":password_hash", $hashed_password);
                                $time = current_date_for_db();
                                $stmt->bindParam(":time", $time);
                                $stmt->execute();

                                $new_user_id = $dbh->lastInsertId();
                        } catch (PDOException $ex) {
                                $ret['login_error'] = "Cannot create user in the database.";
                                $this->error = $ret;
                                return NULL;
                        }

                        return new User($new_user_id, $login, $hashed_password, $time);
                }

                public function get_last_error() {
                        return $this->error;
                }
        };
