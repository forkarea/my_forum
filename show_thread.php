<?php
        include_once "page_header.php";

        include_once "common_functions.php";
        $thread_id = sanitize_nonzero_integer_input($_GET['thread_id'], 'threadlist.php');


        include_once "database_connection.php";
        $dbh = get_database_connection();
        
        $stmt = $dbh->prepare('SELECT name FROM threads WHERE id=:thread_id');
        $stmt->bindParam(':thread_id', $thread_id);
        if ($stmt->execute()) {
                $row = $stmt->fetch();
                $thread_name = $row[0];
        }

        generate_page_header(escape_str_in_usual_html_pl($thread_name));
?>
<table style="width: 70%" >
        <h1> 
                <?php echo escape_str_in_usual_html_pl($thread_name); ?>
        </h1>
<?php
try {
        $stmt = $dbh->prepare('SELECT text FROM posts WHERE thread_id=:thread_id');
        $stmt->bindParam(':thread_id', $thread_id);
        if ($stmt->execute()) { 
                while($row = $stmt->fetch()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                                echo escape_str_in_usual_html_pl($row[0]);
                        echo '</td></tr>';
                }
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
