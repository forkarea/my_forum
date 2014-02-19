<?php
        include_once "common_functions.php";
        function generate_page_header($escaped_title)
        {
                header('Content-Type: text/html; charset=utf-8');
                
                echo '<!DOCTYPE html>';
                echo '<html><head><meta charset="utf-8"><title>';
                echo $escaped_title; 
                echo '</title></head>';
                echo '<body>';
                echo '<h1>My forum</h1>';
        }
        
