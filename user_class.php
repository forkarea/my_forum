<?php
include_once "database_connection.php";
include_once "common_functions.php";
if (PHP_VERSION_ID < 50500) {
        //fallback for functions unavailable in PHP <5.5
        include_once "password_compat/lib/password.php";
}

        class User {
                public $id;
                public $login;
                public $password_hash;
                public $creation_date;

                public function __construct ($id, $login, $password_hash, $creation_date) {
                        $this->id = $id;
                        $this->login = $login;
                        $this->password_hash = $password_hash;
                        $this->creation_date = $creation_date;
                }

        };
