<?php
        include_once "./common_functions.php";
        include_once "./classes/UserManager.php";

        $um = new UserManager();
        $um->clear_login_cookies();

        my_redirect('/');

