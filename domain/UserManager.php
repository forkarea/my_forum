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
                        } catch (\PDOException $ex) {
                                return NULL;
                        }

                        $this->all_received_users[$user_id] = $user;
                        return $user;
                }

                public function get_logged_in_user()
                {
                        //http://stackoverflow.com/questions/132194/php-storing-objects-inside-the-session
                        //suggests it is better to get objects from the DB each time
                        //then serialize/deserialize them to/from the session
                        if (!isset($_SESSION['user_id']))
                                return NULL;

                        $user_id = $_SESSION['user_id'];
                        if ($user_id === NULL)
                                return NULL;
                        return $this->get_user_by_id($user_id);
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

                        } catch (\PDOException $ex) {
                                return NULL;
                        }

                        return $user;
                }

                public function clear_login_cookies()
                {
                        unset($_SESSION['user_id']);
                }
        };
