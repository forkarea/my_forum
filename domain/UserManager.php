<?php
        namespace domain;
        use PDO;

        class UserManager
        {
                private $error = NULL;
                private $dbh = NULL;
                private $all_received_users = array();

                public function __construct($dbh)
                {
                        $this->dbh = $dbh;
                }

                public static function get_empty_error_state()
                {
                        return array('login_error' => NULL,
                                         'password_error' => NULL,
                                         'password_repeat_error' =>NULL,
                                         'error' => NULL);
                }

                private function check_new_user_parameters($login, $password, $password_repeat)
                {
                        $ret = $this->get_empty_error_state();
                        $ok = true;

                        $login_length = mb_strlen($login, "UTF-8");
                        if ($login_length < 4) {
                               $ret['login_error'] = "The user login is too short (min. 4 chars)";
                               $ok = false;
                        } elseif ($login_length >= 20) {
                               $ret['login_error'] = "The user login is too long (max. 20 chars)";
                               $ok = false;
                        } elseif (!\utility\SecFun::is_valid_utf8($login)) {
                               $ret['login_error'] = "The user login is not a valid UTF-8 string";
                               $ok = false;
                        //check for allowed characters in the user login
                        } elseif (preg_match('/^(\p{L}|[-0-9._])*$/u', $login) !== 1) {
                                $ret['login_error'] = "The user login contains invalid characters (only letters (including multilingual), digits, hyphen, underscore and dot allowed).";
                                $ok = false;
                        } else {
                                try {
                                        $stmt = $this->dbh->prepare("SELECT user_id FROM users WHERE login=:login");
                                        $stmt->bindParam(":login", $login);
                                        $r = $stmt->execute();

                                        $row = $stmt->fetch();
                                        if ($row !== false) {
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

                public function create_user($login, $password, $password_repeat)
                {
                        $ret = $this->check_new_user_parameters($login, $password, $password_repeat);
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
                                $stmt = $this->dbh->prepare("insert into users (login, password_hash, signup_time) values (:login, :password_hash, :time)");
                                $stmt->bindParam(":login", $login);
                                $stmt->bindParam(":password_hash", $hashed_password);
                                $time = \utility\DatabaseConnection::getCurrentDateForDb();
                                $stmt->bindParam(":time", $time);
                                $stmt->execute();

                                $new_user_id = $this->dbh->lastInsertId();
                        } catch (PDOException $ex) {
                                $ret['login_error'] = "Cannot create user in the database.";
                                $this->error = $ret;

                                return NULL;
                        }

                        return User::construct($this->dbh, $new_user_id, $login, $hashed_password, $time);
                }

                public function get_last_error()
                {
                        return $this->error;
                }

                public function get_user_by_id($user_id)
                {
                        if (is_null($user_id))
                                return NULL;

                        if (isset($this->all_received_users[$user_id])) {
                                return $this->all_received_users[$user_id];
                        }

                        try {
                                $stmt = $this->dbh->prepare("select * from users where user_id = :user_id");
                                $stmt->bindParam(":user_id", $user_id);
                                $stmt->execute();
                                $user = $stmt->fetchObject("domain\User", array($this->dbh));
                                if ($user === false) {
                                        return NULL;
                                }
                        } catch (PDOException $ex) {
                                return NULL;
                        }

                        $this->all_received_users[$user_id] = $user;
                        return $user;
                }
                public function get_logged_in_user()
                {
                        if (!isset($_COOKIE[User::LOGIN_SECRET_COOKIE_NAME]) ||
                                        !isset($_COOKIE[User::USERNAME_COOKIE_NAME])) {
                                return NULL;
                        }

                        try {
                                $stmt = $this->dbh->prepare("select * from users where login = :login AND login_token = :login_token");
                                $stmt->bindParam(":login", $_COOKIE[User::USERNAME_COOKIE_NAME]);
                                $stmt->bindParam(":login_token",
                                        $_COOKIE[User::LOGIN_SECRET_COOKIE_NAME]);
                                $stmt->execute();
                                $stmt->setFetchMode(PDO::FETCH_CLASS, 'domain\User', array($this->dbh));
                                $user = $stmt->fetch();
                                if ($user === false) {
                                        return NULL;
                                }

                        } catch (PDOException $ex) {
                                return NULL;
                        }

                        $this->all_received_users[$user->user_id] = $user;
                        return $user;
                }

                public function log_in_user($login, $password)
                {
                        try {
                                $stmt = $this->dbh->prepare("select * from users where login = :login");
                                $stmt->bindParam(":login", $login);
                                $stmt->execute();
                                $stmt->setFetchMode(PDO::FETCH_CLASS, 'domain\User', array($this->dbh));
                                $user = $stmt->fetch();
                                if ($user === false || $user === NULL) {
                                        return NULL;
                                }
                                if ($user->verify_password($password) !== true) {
                                        $user = NULL;
                                }

                        } catch (PDOException $ex) {
                                return NULL;
                        }

                        return $user;
                }


                public function clear_login_cookies()
                {
                        setcookie(User::USERNAME_COOKIE_NAME, "", 0);
                        setcookie(User::LOGIN_SECRET_COOKIE_NAME, "", 0);
                }
        };
