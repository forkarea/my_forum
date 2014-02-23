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
                //PDO::FETCH_CLASS mode suggests a non-parametric constructor
                //Therefore easy to use method for filling all fields requires a separate function
                public static function construct ($dbh, $user_id, $login, $password_hash, $signup_time)
                {
                        $u = new User($dbh);
                        $u->user_id = $user_id;
                        $u->login = $login;
                        $u->password_hash = $password_hash;
                        $u->signup_time = $signup_time;

                        return $u;
                }

                public function create_login_cookie ()
                {
                        try {
                                $login_token = openssl_random_pseudo_bytes(10);
                                $login_token = base64_encode($login_token);

                                $stmt = $this->dbh->prepare("update users set login_token = :login_token where user_id = :id");
                                $stmt->bindParam(":login_token", $login_token);
                                $stmt->bindParam(":id", $this->user_id);
                                if (!$stmt->execute())
                                        return "Cannot execute statement";
                        } catch (PDOException $ex) {
                                return "Cannot connect to database";
                        }
                        $r1 = setcookie(self::USERNAME_COOKIE_NAME , $this->login, time() + 30*24*3600, '/', NULL, FALSE, TRUE);
                        if (!$r1) {
                                return "Cannot set login cookie.";
                        }
                        $r2 = setcookie(self::LOGIN_SECRET_COOKIE_NAME, $login_token, time() + 30*24*3600, '/', NULL, FALSE, TRUE);
                        if (!$r2) {
                                return "Cannot set login_cookie cookie.";
                        }

                        return $r1 && $r2;
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
