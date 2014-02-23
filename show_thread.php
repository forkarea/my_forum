<?php
        include_once './init_classloader.php';
        include_once './page_header.php';
        include_once './common_functions.php';

        use domain\ForumSection;
        use domain\ForumThread;
        use domain\ForumPost;
        use domain\UserManager;

        $thread_id = sanitize_nonzero_integer_input($_REQUEST['thread_id'], 'threadlist.php');

        $dbh = utility\DatabaseConnection::getDatabaseConnection();
        $um = new UserManager($dbh);
        $user = $um->get_logged_in_user();

        $section = new ForumSection($dbh);
        $thread = $section->get_thread($thread_id);

        $text = NULL;
        $text_error = NULL;
        if (array_key_exists('text', $_POST)) {
                $text = sanitize_string_input($_POST['text']);

                if (is_object($user)) {
                        $post = ForumPost::create_as_new($dbh, $text, $user, $text_error);
                        if ($post != NULL) {
                                $thread->add_post_raw($post);
                        };
                } else {
                        $text_error = "You are not logged in";
                }

        };
        if (is_null($text_error) && array_key_exists('thread_id', $_POST)) {
                //redirect to a website using GET so that
                //the address bar contains thread_id and
                //can be sent to someone/bookmarked
                my_redirect('show_thread.php?thread_id='.$thread_id);
        }

        $thread_name = $thread->get_name();

        generate_page_header_with_user(escape_str_in_usual_html_pl($thread_name), $user);
?>
        <h2>
                <?php echo escape_str_in_usual_html_pl($thread_name); ?>
        </h2>
        <p> Created
                <?php
                        $thread_creator = $thread->get_user_creator();
                        if (!is_null($thread_creator)) {
                                echo "by ".$thread_creator->login;
                        }
                ?>
                on
                <?php echo $thread->time ?>
        </p>

<table style="width: 70%">
<?php
        $stmt = $thread->get_all_posts();
        if ($thread->initiate_getting_all_posts() === true) {
                while ($post = $thread->get_next_post()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                        $time = $post->creation_time;
                        if (!is_null($time)) {
                                echo $time;
                                $c_user = $post->get_creator();
                                if ($c_user !== NULL) {
                                        echo ' by ' . escape_str_in_usual_html_pl($c_user->login);
                                }
                        }
                        echo '<br>';
                        echo escape_str_in_usual_html_pl($post->text);
                        echo '</td></tr>';
                }
        }
?>
</table>
<?php if(is_object($user) || ($text_error !== NULL)) { ?>
<h2> Add a new post </h2>
	<form action="show_thread.php" method="post" accept-charset="UTF-8">
        <?php if ($text_error !== NULL) echo "<p>$text_error</p>" ?>
        <p><textarea rows="5" cols="20" name="text"><?php if ($text !== NULL) echo escape_str_in_usual_html_pl($text) ?></textarea></p>
        <input type="hidden" name="thread_id" value="<?php echo $thread_id ?>">
        <?php if(is_object($user)) { ?>
                <p><input type="submit" value="Submit"></p>
        <?php } ?>
</form>
<?php } ?>
<form action="threadlist.php" method="get">
	<p><input type="submit" value="Go back to the thread list"></p>
</form>

</body>
</html>
