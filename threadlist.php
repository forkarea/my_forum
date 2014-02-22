<?php
        include_once "./common_functions.php";
        include_once "./page_header.php";

        include_once "./database_connection.php";
        include_once "./classes/ForumSection.php";

        $dbh = $um = $user = NULL;
        $dbh = get_database_connection();
        $um = new UserManager($dbh);
        if ($um!= NULL) {
                $user = $um->get_logged_in_user();
        } else {
                $user = NULL;
        }

        generate_page_header_with_user("My forum", $user);
?>
<h2> List of threads </h2>
<table style="width: 70%" >
<?php
try {
        $section = new ForumSection($dbh);
	if (($stmt = $section->get_all_threads())) {
	        while($row = $stmt->fetch()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                        $post_time = $row[2];
                        if (!is_null($post_time)) {
                               echo "<p>$post_time</p>";
                        }
                        echo "<a href=show_thread.php?thread_id=$row[0]>";
                        echo    escape_str_in_usual_html_pl($row[1]);
                        echo '</a>';
                        echo '</td></tr>';
                }
        }
} catch (PDOException $e) {
        echo "Error!: cannot connect to the database!";
}

?>

</table>
<a href="new_thread.php">Create a new thread</a>

</body>
</html>
