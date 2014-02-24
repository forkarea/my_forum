<?php
        include_once './init_classloader.php';
        include_once './common_functions.php';
        include_once './page_header.php';
        use domain\User;
        use domain\UserManager;

        session_start();

        $dbh = utility\DatabaseConnection::getDatabaseConnection();
        $dbh->beginTransaction();

        $um = new UserManager($dbh);
        $user = $um->get_logged_in_user();

        if ($user === NULL)
                my_redirect('/');

        $ret = array('old_password_error' => null,
                        'password_error' => null,
                        'password_repeat_error' => null);

	if (array_key_exists('old_password', $_POST)) {
                $old_password = sanitize_password_input($_POST['old_password'], $ret['old_password_error']);
                $password = sanitize_password_input($_POST['password'], $ret['password_error']);
                $password_repeat = sanitize_password_input($_POST['password_repeat'], $ret['password_repeat_error']);

                $succeeded = false;
                if ($old_password === NULL || $password === NULL || $password_repeat === NULL) {
                        $succeeded = false;
                } else if ($password !== $password_repeat) {
                        $ret['password_repeat_error'] = 'Passwords do not match!';
                        $succeeded = false;
                } else if (!$user->verify_password($old_password)){
                        $ret['old_password_error'] = 'Old password is incorrect!';
                        $succeeded = false;
                } else {
                        if ($user->change_password($password, $ret['password_error'])) {
                                $succeeded = true;
                                $dbh->commit();
                        }
                }
                if ($succeeded !== true) {
                        $dbh->rollBack();
                }
                $display_form = !$succeeded;
        } else {
                $dbh->rollBack();
                $display_form = true;
        }

        generate_page_header_with_user("My forum - change password", $user);
?>

<?php if ($display_form) {
        include_once './form_support.php';
?>

        <h2>Add a new user</h2>

        <form action="change_password.php" method="post" accept-charset="UTF-8">
<?php
                display_form_field('old_password', 'Old password:', 'password');
                display_form_field('password', 'New password:', 'password');
                display_form_field('password_repeat', 'Repeat new password:', 'password');
?>
                <p><input type="submit" value="Submit"></p>
        </form>
<?php } else { ?>
        <h2> Password for user <?php echo escape_str_in_usual_html_pl($user->login, true) ?> was changed</h2>
<?php }?>
        <form action="threadlist.php" method="get">
                <p><input type="submit" value="Go back to the thread list"></p>
        </form>
</body>
</html>
