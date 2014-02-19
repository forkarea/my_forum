<?php
function get_database_connection()
{
        try {
                $dbh = new PDO("mysql:host=localhost;dbname=my_php;charset=UTF8", "my_php", "abc", array(
                            PDO::ATTR_PERSISTENT => true
                    ));
                if ($dbh === NULL)
                        die("Cannot connect to the database");

                $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		//$dbh->query ('SET NAMES utf8');
		//$dbh->query ('SET CHARACTER_SET utf8_unicode_ci');

                return $dbh;


        } catch (PDOException $e) {
                die("Cannot connect to the database");
        }

}
