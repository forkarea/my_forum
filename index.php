<!DOCTYPE html>
<html>
<head>
        <title> My forum </title>
        <meta charset="utf-8">
</head>
<body>
<?php
try {
        //phpinfo();
        $dbh = new PDO("mysql:host=localhost;dbname=my_php", "my_php", "abc", array(
                    PDO::ATTR_PERSISTENT => true
            ));
        $stmt = $dbh->prepare('SELECT text from posts');
        if ($stmt->execute()) { 
                while($row = $stmt->fetch()) {
                        echo '<p>';
                                echo $row[0];
                        echo '</p>';
                }
        }
} catch (PDOException $e) {
        print "Error!: cannot connect to the database!";
}

?>
<form action="index_upload.php" method="post">
<p><textarea rows="5" cols="20" name="text">New post</textarea></p>
<p><input type="submit"></p>
</form>

</body>
</html>
