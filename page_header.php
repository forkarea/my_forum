<?php
        include_once "./common_functions.php";
        include_once "./database_connection.php";
        include_once "./classes/User.php";
        include_once "./classes/UserManager.php";


        function generate_page_header_with_user($title, $user)
        {
                header('Content-Type: text/html; charset=utf-8');
                
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

                if ($user !== NULL) {
                        echo "<h2>Welcome, {$user->login}</h2>";
                }
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
        
