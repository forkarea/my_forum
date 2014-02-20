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

                if (preg_match('%^(?:
                                [\x09\x0A\x0D\x20-\x7E]              # ASCII
                                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                                | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
                                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                                | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
                                | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
                                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                                | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
                                )*$%xs', $variable))
                        return $variable;
                else
                        return iconv('CP1252', 'UTF-8', $variable);
        }

        function escape_str_in_usual_html_pl($variable, $double_encode = true)
        {
                if (PHP_VERSION_ID < 50400) {
                        $variable = htmlspecialchars($variable, 
                                        ENT_QUOTES, "UTF-8", $double_encode);
                } else {
                        $variable = htmlspecialchars($variable, 
                                        ENT_QUOTES | ENT_DISALLOWED | ENT_HTML5, "UTF-8", $double_encode);
                }
                
                return $variable;
        }
