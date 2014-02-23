<?php
        include_once "./init_classloader.php";
        include_once "./common_functions.php";

        $um = new classes\UserManager(NULL);
        $um->clear_login_cookies();

        my_redirect('/');

