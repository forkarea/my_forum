<?php
        include_once './init_classloader.php';
        include_once './common_functions.php';
        include_once './page_header.php';
        use domain\ForumSection;
        use domain\UserManager;

        session_start();

        $dbh = $um = $user = NULL;
        $dbh = utility\DatabaseConnection::getDatabaseConnection();
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
	if ($section->init_get_all_threads()) {
	        while ($thread = $section->get_next_thread()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                        $post_time = $thread->time;
                        if (!is_null($post_time)) {
                                echo "<p>$post_time";
                                $user_creator = $thread->get_user_creator($um);
                                if (is_object($user_creator)) {
                                        echo ' by ' . $user_creator->login;
                               }
                               echo '</p>';
                        }
                        echo "<a href=\"show_thread.php?thread_id={$thread->id}\">";
                        echo    escape_str_in_usual_html_pl($thread->name);
                        echo '</a>';
                        echo '</td></tr>';
                }
        }
} catch (PDOException $e) {
        echo "Error!: cannot connect to the database!";
}

?>

</table>
<?php if (is_object($user)) { ?>
<a href="new_thread.php">Create a new thread</a>
<?php } else { ?>
<p>Please login to create new threads</p>
<?php } ?>
</body>
</html>
