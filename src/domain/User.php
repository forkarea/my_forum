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

                private static function check_password_is_sensible($password, &$ret)
                {
                        if (!is_string($password)) {
                                $ret = "The password is in an incorrect format";
                                return false;
                        }

                        if (strlen($password) < 10) {
                                $ret = "The password is too short (minimum 10 characters)";
                                return false;
                        }
                        return true;
                }

                private static function check_new_user_parameters($dbh, $login, $password, $password_repeat)
                {
                        $ret = User::get_empty_error_state();
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
                                        $stmt = $dbh->prepare("SELECT user_id FROM users WHERE login=:login");
                                        $stmt->bindParam(":login", $login);
                                        $r = $stmt->execute();

                                        $row = $stmt->fetch();
                                        if ($row !== false) {
                                                $ret['login_error'] = "The user login already exists";
                                                $ok = false;
                                        }
                                } catch (\PDOException $ex) {
                                        $ret['error'] = "Cannot connect to the database.";
                                        $ok = false;
                                }
                        }

                        if ($password !== $password_repeat) {
                               $ret['password_repeat_error'] = "The passwords don't match";
                               $ok = false;
                        }

                        if (!User::check_password_is_sensible($password, $ret['password_error'])) {
                                $ok = false;
                        }

                        if ($ok !== true) {
                                return $ret;
                        }

                        return NULL;
                }

                public static function get_empty_error_state()
                {
                        return array('login_error' => NULL,
                                         'password_error' => NULL,
                                         'password_repeat_error' =>NULL,
                                         'error' => NULL);
                }

                public static function create_as_new($dbh, $login, $password, $password_repeat, &$error)
                {
                        $ret = User::check_new_user_parameters($dbh, $login, $password, $password_repeat);
                        if (!is_null($ret)) {
                                $error = $ret;

                                return NULL;
                        }

                        $error = User::get_empty_error_state();

                        //PHP manual suggests to store hashes in a column 255 chars wide
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        if ($hashed_password === FALSE) {
                                $error['error'] = "Cannot create password hash!";
                                return NULL;
                        }

                        $user = new User($dbh);
                        $user->login = $login;
                        $user->password_hash = $hashed_password;
                        $user->signup_time = \utility\DatabaseConnection::getCurrentDateForDb();

                        return $user;
                }

                public function persist(&$error_msg = null)
                {
                        try {
                                $stmt = $this->dbh->prepare(
                                        "insert into users (login, password_hash, signup_time) " .
                                        "values (:login, :password_hash, :time)");
                                $stmt->bindParam(":login", $this->login);
                                $stmt->bindParam(":password_hash", $this->password_hash);
                                $stmt->bindParam(":time", $this->signup_time);
                                $stmt->execute();
                                $this->user_id = $this->dbh->lastInsertId();

                                return true;
                        } catch (\PDOException $ex) {
                                $error_msg = "Cannot create user in the database.";

                                return false;
                        }
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
                        return true;
                }

                public function verify_password($password)
                {
                        if (!is_string($this->password_hash))
                                return false;
                        if (strlen($this->password_hash) === 0)
                                return false;
                        return password_verify($password, $this->password_hash);
                }

                public function change_password($new_password, &$error_msg)
                {
                        if (!User::check_password_is_sensible($new_password, $error_msg)) {
                                return false;
                        }

                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        if ($hashed_password === FALSE) {
                                $error_msg = "Cannot create password hash!";
                                return NULL;
                        }

                        if ($this->user_id === null) {
                                $error_msg = 'Database query failed';
                        }

                        try {
                                $stmt = $this->dbh->prepare(
                                        'update users set password_hash = :password_hash ' .
                                        'where user_id = :user_id AND password_hash = :old_password_hash');
                                $stmt->bindValue(':password_hash', $hashed_password);
                                $stmt->bindValue(':old_password_hash', $this->password_hash);
                                $stmt->bindValue(':user_id', $this->user_id);
                                $stmt->execute();

                        } catch (\PDOException $ex) {
                                $error_msg = 'Database query failed';
                                return false;
                        }

                        $this->password_hash = $hashed_password;
                        return true;
                }

                public function get_new_CSRF_protection_token()
                {
                        $csrf = sha1(openssl_random_pseudo_bytes(10));
                        $_SESSION['csrf'] = $csrf;
                        return $csrf;
                }


                public function check_CSRF_protection_token($token)
                {
                        if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
                                return false;
                        }

                        if (!is_string($token)) {
                                return false;
                        }

                        return $_SESSION['csrf'] === $token;
                }

                public function clear_CSRF_protection_token()
                {
                        $_SESSION['csrf'] = null;
                }
        };
