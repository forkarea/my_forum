<?php
        include_once "./init_classloader.php";
        include_once "./common_functions.php";
        include_once "./page_header.php";
        include_once "./database_connection.php";

        use domain\User;
        use domain\UserManager;

        $ret = UserManager::get_empty_error_state();
        $dbh = get_database_connection();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $um = new UserManager($dbh);
        $user = $um->get_logged_in_user();
        if ($user !== NULL)
                my_redirect('/');

	if (array_key_exists('login', $_POST)) {
                $login = sanitize_string_input($_POST['login']);
                $error = NULL;
                $password = sanitize_password_input($_POST['password'], $error);

                if ($password !== NULL) {
                        $user = $um->log_in_user($login, $password);
                        if ($user !== NULL) {
                                $user->create_login_cookie();
                                my_redirect('/');
                        } else {
                                $error = "Incorrect username or password";
                                $display_login_error = true;
                        }
                }
        } else {
                $user = NULL;
                $display_login_error = false;
        }

        generate_page_header_with_user("My forum - add a new user", NULL);
?>

<?php 
        if ($display_login_error) { 
                function display_old_value($name) {
                        global $ret;
                        global $$name;
                        if (isset($$name))
                                echo escape_str_in_usual_html_pl($$name, false); 
                }
        } else {
                function display_old_value($name) {}
        }

?>
        <h2>Log in</h2>

        <form action="login_form.php" method="post" accept-charset="UTF-8">
                <?php if ($display_login_error) echo "<p>$error</p>" ?>
                <p>Login: <input type="text" name="login" 
                        value="<?php display_old_value('login'); ?>"
                ></input></p>

                <p>Password: <input type="password" name="password" 
                        value="<?php display_old_value('password'); ?>"
                ></input></p>

                <p><input type="submit" value="Log in"></p>
        </form>

        <form action="threadlist.php" method="get">
                <p><input type="submit" value="Go back to the thread list"></p>
        </form>
</body>
</html>

