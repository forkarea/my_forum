<?php
        include_once "page_header.php";

        include_once "common_functions.php";
        $thread_id = sanitize_nonzero_integer_input($_GET['thread_id'], 'threadlist.php');


        include_once "database_connection.php";
        include_once "thread_class.php";
        $dbh = get_database_connection();
        
        $my_thread = new ForumThread($thread_id);
        $thread_name = $my_thread->get_name($dbh);
        
        generate_page_header(escape_str_in_usual_html_pl($thread_name));
?>
<table style="width: 70%" >
        <h1> 
                <?php echo escape_str_in_usual_html_pl($thread_name); ?>
        </h1>
<?php
try {
        $stmt = $my_thread->get_all_posts($dbh);
        if (!is_null($stmt)) {
                while($row = $stmt->fetch()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                                echo escape_str_in_usual_html_pl($row[0]);
                        echo '</td></tr>';
                }
        } else {
        	echo NULL;
        }

} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}

?>
</table>
<h2> Add a new post </h2>
	<form action="new_post.php" method="post">
	<p><textarea rows="5" cols="20" name="text">New post</textarea></p>
	<input type="hidden" name="thread_id" value="<?php echo $thread_id ?>">
	<p><input type="submit" value="Submit"></p>
</form>
<form action="threadlist.php" method="get">
	<p><input type="submit" value="Go back to the thread list"></p>
</form>

</body>
</html>
