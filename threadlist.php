<?php
        header('Content-Type: text/html; charset=utf-8');
        include "common_functions.php";
?>
<!DOCTYPE html>
<html>
<head>
        <title> My forum </title>
        <meta charset="utf-8">
</head>
<body>
<table style="width: 70%" >
<?php
try {
        include "database_connection.php";
        $dbh = get_database_connection();
        $stmt = $dbh->prepare('SELECT id, name from threads');
        if ($stmt->execute()) { 
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
<form action="new_thread.php" method="post">
        <p>Create new thread</p>
        <p>Name: <input type="text" name="name"></input></p>
        <p><input type="submit"></p>
</form>

</body>
</html>
