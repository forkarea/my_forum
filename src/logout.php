<?php
        include_once './init_classloader.php';

        session_start();

        $um = new domain\UserManager(NULL);
        $um->clear_login_cookies();

        //Copied from an example in http://pl1.php.net/manual/en/function.session-destroy.php

        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                                $params["path"], $params["domain"],
                                $params["secure"], $params["httponly"]
                        );
        }

        // Finally, destroy the session.
        session_destroy();

        \utility\SecFun::my_redirect('/');
