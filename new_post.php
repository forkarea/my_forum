<?php
try {
//INPUT VALIDATION
        include "common_functions.php";
        $thread_id =  sanitize_nonzero_integer_input($_POST['thread_id'], 'threadlist.php');
        $text = assert_not_null($_POST['text']);

        $_POST=NULL;
//END OF INPUT VALIDATION

        include "database_connection.php";
        include "thread_class.php";

        $dbh = get_database_connection();
        $thread = new ForumThread($thread_id);
        $thread->add_post($dbh, $text);
        
        my_redirect('show_thread.php?thread_id='.$thread_id);

} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}
