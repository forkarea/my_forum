<?php
        function my_redirect($target_address)
        {
                header('Location: '.$target_address);
                die();
        }

        function assert_not_null($variable, $target_address = '/')
        {
                if ($variable === NULL) {
                        my_redirect($target_address);
                }
                return $variable;
        }

        function sanitize_nonzero_integer_input($variable, $target_address = '/')
        {
                if ($variable === NULL) {
                        my_redirect($target_address);
                }
                $value = (int)$variable;
                if ($value === 0) {
                        my_redirect($target_address);
                }
                return $value;
        }

        function sanitize_integer_input($variable, $target_address = '/')
        {
                if ($variable === NULL) {
                        my_redirect($target_address);
                }
                $value = (int)$variable;
                return $value;
        }
        
        function sanitize_string_input($variable, $target_address = '/')
        {

                if ($variable === NULL) {
                        my_redirect($target_address);
                }
                $variable = (string)$variable;

                if (PHP_VERSION_ID < 50400) {
                        $variable = htmlspecialchars($variable, ENT_QUOTES, "UTF-8");
                } else {
                        $variable = htmlspecialchars($variable, ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8");
                }

                return $value;
        }

        function escape_str_in_usual_html_pl($variable)
        {
                if (PHP_VERSION_ID < 50400) {
                        $variable = htmlspecialchars($variable, ENT_QUOTES, "UTF-8");
                } else {
                        $variable = htmlspecialchars($variable, ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8");
                }
                
                return $variable;
        }
