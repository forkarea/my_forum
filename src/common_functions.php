<?php
        //A thin wrapper over utility\SecFun
        use utility\SecFun;
        function my_redirect($target_address)
        {
                return SecFun::my_redirect($target_address);
        }

        function assert_not_null($variable, $target_address = '/')
        {
                return SecFun::assert_not_null($variable, $target_address);
        }

        function sanitize_nonzero_integer_input($variable, $target_address = '/')
        {
                return SecFun::sanitize_nonzero_integer_input($variable, $target_address);
        }

        function sanitize_integer_input($variable, $target_address = '/')
        {
                return SecFun::sanitize_integer_input($variable, $target_address);
        }

        function is_valid_utf8($variable)
        {
                return SecFun::is_valid_utf8($variable);
        }

        function sanitize_password_input($variable, &$error_message)
        {
                return SecFun::sanitize_password_input($variable, $error_message);
        }

        function sanitize_string_input($variable, $target_address = '/')
        {
                return SecFun::sanitize_string_input($variable, $target_address);
        }

        function escape_str_in_usual_html_pl($variable, $double_encode = true)
        {
                return SecFun::escape_str_in_usual_html_pl($variable, $double_encode);
        }
