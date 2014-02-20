<?php
try {
        include_once "common_functions.php";

        $error = NULL;
        $name = "";
        $contents = "";
        $thread_name_error_msg = NULL;
        $contents_error_msg = NULL;
        

        if (array_key_exists('name', $_POST)) {
        //INPUT VALIDATION
                $name = sanitize_string_input($_POST['name']);
                $contents = sanitize_string_input($_POST['contents']);
        //END OF INPUT VALIDATION

                
                //we have to count the length in bytes,
                //because the DB column is of varbinary type
                //to allow for easy storing of Unicode characters
                $name_length = strlen($name);
                $content_length = strlen($contents);
                if ($name_length === 0) {
                        $thread_name_error_msg = "Please write the thread name!";
                } else if ($name_length > 950) {
                        $thread_name_error_msg = "The thread name is too long!";
                } else if ($content_length == 0) {
                        $contents_error_msg = "Please write the message contents!";
                } else if ($content_length > 9990) {
                        $contents_error_msg = "The message is too long!";
                } else {
                        include "database_connection.php";
                        include_once "forum_section_class.php";
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
        }
} catch (PDOException $e) {
        $error = "Database error";
}

        include_once "page_header.php";
        generate_page_header("My forum - Create a new thread");
?>


<h2>Create a new thread</h2>

<?php
        if ($error !== NULL) {
                echo '<h2>';
                echo $error;
                echo '</h2>';
        }
?>
<form action="new_thread.php" method="post" accept-charset="UTF-8">
        <?php  if ($thread_name_error_msg !== NULL) echo "<p>$thread_name_error_msg</p>" ?>
        <p>Name: <input type="text" name="name" maxlength="950" 
                value="<?php echo escape_str_in_usual_html_pl($name) ?>">
        </p>
        <?php  if ($contents_error_msg !== NULL) echo "<p>$contents_error_msg</p>" ?>
        <p><textarea rows="5" cols="100" name="contents" maxlength="9990" 
                        value="<?php echo escape_str_in_usual_html_pl($contents) ?>">
           </textarea>
        </p>
        <p><input type="submit" value="Submit a new thread"></p>
</form>
<form action="threadlist.php" method="get">
        <p><input type="submit" value="Go back to the thread list"></p>
</form>
</body>
</html>
