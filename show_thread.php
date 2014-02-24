<?php
        include_once './init_classloader.php';
        include_once './page_header.php';
        include_once './common_functions.php';

        use domain\ForumSection;
        use domain\ForumThread;
        use domain\ForumPost;
        use domain\UserManager;

        session_start();

        $thread_id = sanitize_nonzero_integer_input($_REQUEST['thread_id'], 'threadlist.php');

        $dbh = utility\DatabaseConnection::getDatabaseConnection();
        $dbh->beginTransaction();
        $um = new UserManager($dbh);
        $user = $um->get_logged_in_user();

        $section = new ForumSection($dbh);
        $thread = $section->get_thread($thread_id);

        $text = NULL;
        $text_error = NULL;
        if (array_key_exists('text', $_POST)) {
                $text = sanitize_string_input($_POST['text']);

                $succeeded = false;
                if (is_object($user)) {
                        if (!$user->check_CSRF_protection_token($_POST['csrf_token'])) {
                                $text_error = "You made the request out of sequence. Please repeat it.";
                        } else {
                                $user->clear_CSRF_protection_token();
                                $post = ForumPost::create_as_new($dbh, $text, $user, $text_error);
                                if (is_object($post)) {
                                        $thread->add_post($post, $text_error);
                                        $dbh->commit();
                                        $succeeded = true;
                                }
                        }
                } else {
                        $text_error = "You are not logged in";
                }
                if (!$succeeded) {
                        $dbh->rollBack();
                }

        } else {
                $dbh->rollBack();
        }

        if (is_null($text_error) && array_key_exists('thread_id', $_POST)) {
                //redirect to a website using GET so that
                //the address bar contains thread_id and
                //can be sent to someone/bookmarked
                my_redirect('show_thread.php?thread_id='.$thread_id);
        }

        generate_page_header_with_user(escape_str_in_usual_html_pl($thread->name), $user);
?>
        <h2>
                <?php echo escape_str_in_usual_html_pl($thread->name); ?>
        </h2>
        <p> Created
                <?php
                        $thread_creator = $thread->get_user_creator($um);
                        if (!is_null($thread_creator)) {
                                echo "by ".$thread_creator->login;
                        }
                ?>
                on
                <?php echo $thread->time ?>
        </p>

<table style="width: 70%">
<?php
        if ($thread->initiate_getting_all_posts($text_error) === true) {
                while ($post = $thread->get_next_post()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                        $time = $post->creation_time;
                        if (!is_null($time)) {
                                echo $time;
                                $c_user = $post->get_creator($um);
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
<?php if (is_object($user) || ($text_error !== NULL)) { ?>
<h2> Add a new post </h2>
	<form action="show_thread.php" method="post" accept-charset="UTF-8">
        <?php if ($text_error !== NULL) echo "<p>$text_error</p>" ?>
        <p><textarea rows="5" cols="100" name="text"><?php if ($text !== NULL) echo escape_str_in_usual_html_pl($text) ?></textarea></p>
        <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
        <input type="hidden" name="csrf_token" value="<?= $user->get_new_CSRF_protection_token() ?>">
        <?php if (is_object($user)) { ?>
                <p><input type="submit" value="Submit"></p>
        <?php } ?>
</form>
<?php } ?>
<form action="threadlist.php" method="get">
	<p><input type="submit" value="Go back to the thread list"></p>
</form>

</body>
</html>
