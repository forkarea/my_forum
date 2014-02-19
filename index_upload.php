<?php
try {
//INPUT VALIDATION
        include "common_functions.php";
        $thread_id =  sanitize_nonzero_integer_input($_POST['thread_id'], 'threadlist.php');
        $text = assert_not_null($_POST['text']);

        $_POST=NULL;
//END OF INPUT VALIDATION

        include "database_connection.php";

        $dbh = get_database_connection();
        $stmt = $dbh->prepare('insert into posts (text, thread_id) values (:text, :thread_id)');
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':thread_id', $thread_id);
        if(! $stmt->execute())
                echo 'Execution failed';

        my_redirect('show_thread.php?thread_id='.$thread_id);

} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}
