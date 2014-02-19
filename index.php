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
        $stmt = $dbh->prepare('SELECT text from posts');
        if ($stmt->execute()) { 
                while($row = $stmt->fetch()) {
                        echo '<tr><td style="border:1px solid black; padding:10px">';
                                echo $row[0];
                        echo '</td></tr>';
                }
        }
} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}

?>
</table>
<form action="index_upload.php" method="post">
<p><textarea rows="5" cols="20" name="text">New post</textarea></p>
<p><input type="submit"></p>
</form>

</body>
</html>
