<?php
namespace utility;
use PDO;

class DatabaseConnection {

        public static function getDatabaseConnection() {
                try {
                        $dbh = new PDO('mysql:host=localhost;dbname=my_php;charset=UTF8', 'my_php', 'abc', array(
                                    PDO::ATTR_PERSISTENT => true
                            ));
                        if ($dbh === NULL)
                                die('Cannot connect to the database');

                        return $dbh;


                } catch (PDOException $e) {
                        die('Cannot connect to the database');
                }
        }       

        function getCurrentDateForDb()
        {
                return date('Y-m-d G:i:s');
        }
};
