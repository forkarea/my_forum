<?php
try {
//INPUT VALIDATION
        include "common_functions.php";
        $name = assert_not_null($_POST['name']);
//END OF INPUT VALIDATION
        
        echo $name;
        include "database_connection.php";
        $dbh = get_database_connection();
        $stmt = $dbh->prepare('insert into threads (name) values (:text)');
        $stmt->bindParam(':text', $name);
        if(! $stmt->execute())
                echo 'Execution failed';

        my_redirect('threadlist.php');

} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}
