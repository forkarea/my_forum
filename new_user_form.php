<?php
        include_once './init_classloader.php';
        include_once './common_functions.php';
        include_once './page_header.php';
        use domain\User;
        use domain\UserManager;

        session_start();

        $ret = User::get_empty_error_state();
        $dbh = utility\DatabaseConnection::getDatabaseConnection();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->beginTransaction();


        $um = new UserManager($dbh);
        $user = $um->get_logged_in_user();

        if ($user !== NULL)
                my_redirect('/');

	if (array_key_exists('login', $_POST)) {
                $login = sanitize_string_input($_POST['login']);
                $password = sanitize_password_input($_POST['password'], $ret['password_error']);
                $password_repeat = sanitize_password_input($_POST['password_repeat'], $ret['password_repeat_error']);


                $succeeded = false;
                if ($password === NULL || $password_repeat === NULL) {
                        $display_form = true;
                } else {
                        $user = User::create_as_new($dbh, $login, $password, $password_repeat, $ret);
                        if ($user === NULL) {
                                $display_form = true;
                        } else {
                                if ($user->persist($ret['error']) === true) {
                                        $dbh->commit();
                                        $succeeded = true;
                                        $r = $user->create_login_cookie();
                                        $display_form = false;
                                } else {
                                        $display_form = true;
                                }
                        }
                }
                if (!$succeeded) {
                        $dbh->rollBack();
                }
        } else {
                $dbh->rollBack();
                $user = NULL;
                $display_form = true;
        }

        generate_page_header_with_user("My forum - add a new user", $user);
?>

<?php if ($display_form) {
        include_once './form_support.php';
?>


        <h2>Add a new user</h2>

        <?php if (!is_null($ret['error'])) echo "<h2>ERROR: $ret[error]</h2>" ?>
        <form action="new_user_form.php" method="post" accept-charset="UTF-8">
<?php 
                display_form_field('login', 'Login:', 'login');
                display_form_field('password', 'Password:', 'password');
                display_form_field('password_repeat', 'Repeat password:', 'password');
?>

                <p><input type="submit" value="Submit"></p>
        </form>
<?php } else { ?>
        <h2> User <?php echo escape_str_in_usual_html_pl($user->login, true) ?> was added to the database</h2>
<?php }?>
        <form action="threadlist.php" method="get">
                <p><input type="submit" value="Go back to the thread list"></p>
        </form>
</body>
</html>
