<?php
        include_once "./common_functions.php";

        use domain\UserManager;
        use domain\User;

        function generate_page_header_with_user($title, $user = false)
        {
                header('Content-Type: text/html; charset=utf-8');

                if ($user !== false) {
                        echo '<p style="text-align: right">';
                        if ($user !== NULL) {
                                echo "Welcome, {$user->login} ";
                                echo '<a href="logout.php">Log out</a>';
                        } else {
                                echo '<a href="new_user_form.php">Sign up</a> ';
                                echo '<a href="login_form.php">Log in</a>';
                        }
                        echo '</p>';
                }

                echo '<!DOCTYPE html>';
                echo '<html><head><meta charset="utf-8"><title>';
                if ($title === NULL) {
                        echo "My forum";
                } else {
                        echo escape_str_in_usual_html_pl($title, false);
                }
                echo '</title></head>';
                echo '<body>';
                echo '<h1>My forum</h1>';

        }


        function generate_page_header($title, $dbh = NULL)
        {
                if ($dbh !== NULL) {
                        $um = new UserManager($dbh);
                        $u = $um->get_logged_in_user();
                } else {
                        $u = NULL;
                }
                generate_page_header_with_user($title, $u);
        }

