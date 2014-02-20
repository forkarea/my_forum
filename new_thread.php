<?php
try {
//INPUT VALIDATION
        include_once "common_functions.php";
        $name = assert_not_null($_POST['name']);
        $contents = assert_not_null($_POST['contents']);        
//END OF INPUT VALIDATION

        include_once "forum_section_class.php";
        if (strlen($name) == 0) {
                $error = 'New thread must have a name!';
        } else if (strlen($contents) == 0) {
                $error = 'Please write some message!';
        } else {
                include "database_connection.php";
                $dbh = get_database_connection();
                
                $section = new ForumSection($dbh);
                $new_thread = $section->add_thread($name);
                if ($new_thread !== NULL) {
                        if ($new_thread->add_post($dbh, $contents)) {
                                my_redirect('show_thread.php?thread_id='.$new_thread->get_id());
                        } else {
                                $error = "Cannot add post to the thread";
                        }
                } else {
                        $error = "Cannot create the thread!";
                }
        }
} catch (PDOException $e) {
        $error = "Database error";
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
