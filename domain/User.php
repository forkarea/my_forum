<?php
        namespace domain;

        use PDO;

        class User
        {
                public $user_id;
                public $login;
                public $password_hash;
                public $signup_time;
                public $dbh;

                const USERNAME_COOKIE_NAME = "login_username";
                const LOGIN_SECRET_COOKIE_NAME = "login_secret";

                public function __construct($dbh)
                {
                        $this->dbh = $dbh;
                }


                public static function construct($dbh, $user_id, $login, $password_hash, $signup_time)
                {
                        $u = new User($dbh);
                        $u->user_id = $user_id;
                        $u->login = $login;
                        $u->password_hash = $password_hash;
                        $u->signup_time = $signup_time;

                        return $u;
                }

                public function create_login_cookie()
                {
                        $_SESSION['user_id'] = $this->user_id;
                }

                public function verify_password($password)
                {
                        if (!is_string($this->password_hash))
                                return false;
                        if (strlen($this->password_hash) === 0)
                                return false;
                        return password_verify($password, $this->password_hash);
                }
        };
