<?php
        include_once './init_classloader.php';

        $um = new domain\UserManager(NULL);
        $um->clear_login_cookies();

        \utility\SecFun::my_redirect('/');

