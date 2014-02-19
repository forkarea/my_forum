<?php
try {
//INPUT VALIDATION
        include_once "common_functions.php";
        $name = assert_not_null($_POST['name']);
        $contents = assert_not_null($_POST['contents']);        
//END OF INPUT VALIDATION

        if (strlen($name) == 0) {
                $error = 'New thread must have a name!';
        } else if (strlen($contents) == 0) {
                $error = 'Please write some message!';
        } else {
                include "database_connection.php";
                $dbh = get_database_connection();
                $stmt = $dbh->prepare('insert into threads (name) values (:text)');
                $stmt->bindParam(':text', $name);
                if(! $stmt->execute())
                        echo 'Execution failed';
                
                $new_thread_id = $dbh->lastInsertId();
                $stmt = $dbh->prepare('insert into posts (text, thread_id) values (:text, :thread_id)');
                $stmt->bindParam(':text', $contents);
                $stmt->bindParam(':thread_id', $dbh->lastInsertId());
                $stmt->execute();

                
                my_redirect('show_thread.php?thread_id='.$new_thread_id);
        }
} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}

        include_once "page_header.php";
        generate_page_header("Incorrect new thread submission");
?>

<h2>

<?php
        echo $error;
?>
</h2>

</body>
</html>
