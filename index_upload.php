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
        echo $_POST['text'];
        $dbh = new PDO("mysql:host=localhost;dbname=my_php", "my_php", "abc", array(
                    PDO::ATTR_PERSISTENT => true
            ));

        $stmt = $dbh->prepare('insert into posts (text) values (:text)');
        $stmt->bindParam(':text', $_POST['text']);
        if(! $stmt->execute())
                echo 'Execution failed';

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