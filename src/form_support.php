<?php
        function display_error($name)
        {
                global $ret;
                if (!is_null($ret[$name.'_error']))
                        echo "<p>".$ret[$name.'_error']."</p>";
        }

        function display_old_value($name)
        {
                global $ret;
                global $$name;
                if (isset($$name))
                        echo 'value="' . escape_str_in_usual_html_pl($$name, false) . '" ';
        }

        function display_form_field($name, $description, $type = "text")
        {
                display_error($name);
                echo "<p>$description <input type=\"$type\" name=\"$name\" ";
                if ($type !== 'password')
                        display_old_value($name);
                echo "></p>\n";
        }
