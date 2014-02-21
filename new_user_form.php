<?php
        include_once "./common_functions.php";
        include_once "./page_header.php";
        include_once "./database_connection.php";
        include_once "./classes/User.php";
        include_once "./classes/UserManager.php";

        $ret = UserManager::get_empty_error_state();
        $dbh = get_database_connection();
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        
        $um = new UserManager($dbh);
        if ($um->get_logged_in_user() !== NULL)
                my_redirect('/');

	if (array_key_exists('login', $_POST)) {
                $login = sanitize_string_input($_POST['login']);
                $password = sanitize_string_input($_POST['password']);
                $password_repeat = sanitize_string_input($_POST['password_repeat']);

                $user = $um->create_user($login, $password, $password_repeat);
                if ($user === NULL) {
                        $ret = $um->get_last_error();
                        $display_form = true;
                } else {
                        $r = $user->create_login_cookie();
                        if ($r !== true) {
                                $ret['error'] = $r;
                                $display_form = true;
                        } else {
                                $display_form = false;
                        }
                }
        } else {
                $display_form = true;
        }

        generate_page_header("My forum - add a new user", $dbh);
?>

<?php if ($display_form) { 
        function display_error($name) {
                global $ret;
                if (!is_null($ret[$name.'_error'])) 
                        echo "<p>".$ret[$name.'_error']."</p>";
        }

        function display_old_value($name) {
                global $ret;
                global $$name;
                if (isset($$name))
                        echo escape_str_in_usual_html_pl($$name, false); 
        }

?>


        <h2>Add a new user</h2>

        <?php if (!is_null($ret['error'])) echo "<h2>ERROR: $ret[error]</h2>" ?>
        <form action="new_user_form.php" method="post" accept-charset="UTF-8">
                <?php display_error('login') ?>
                <p>Login: <input type="text" name="login" 
                        value="<?php display_old_value('login'); ?>"
                ></input></p>

                <?php display_error('password') ?>
                <p>Password: <input type="password" name="password" 
                        value="<?php display_old_value('password'); ?>"
                ></input></p>

                <?php display_error('password_repeat') ?>
                <p>Repeat password: <input type="password" name="password_repeat"
                        value="<?php display_old_value('password_repeat'); ?>"
                ></input></p>

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

