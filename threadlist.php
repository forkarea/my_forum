<?php
        include_once "common_functions.php";
        include "page_header.php";
        generate_page_header("My forum");
?>
<h2> List of threads </h2>
<table style="width: 70%" >
<?php
try {
        include "database_connection.php";
        $dbh = get_database_connection();
        include_once "forum_section_class.php";
        $section = new ForumSection($dbh);
	if (($stmt = $section->get_all_threads())) {
	        while($row = $stmt->fetch()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                        echo "<a href=show_thread.php?thread_id=$row[0]>";
                        echo    escape_str_in_usual_html_pl($row[1]);
                        echo '</a>';
                        echo '</td></tr>';
                }
        }
} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}

?>

</table>
<a href="new_thread.php">Create a new thread</a>

</body>
</html>
