<?php
        include_once "common_functions.php";
        function generate_page_header($title)
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
        }
        
