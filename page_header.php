<?php
        include_once "common_functions.php";
        include_once "database_connection.php";
        include_once "user_class.php";
        include_once "UserManager.php";

        function generate_page_header($title, $dbh = NULL)
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

                if ($dbh !== NULL) {
                        $um = new UserManager();
                        $u = $um->get_logged_in_user($dbh);
                        if ($u !== NULL) {
                                echo "<h2>Welcome, {$u->login}</h2>";
                        }
                }
        }
        
