<?php
        include_once "common_functions.php";
        include_once "page_header.php";
        generate_page_header("My forum");
?>
<h2>Create a new thread</h2>

<form action="new_thread.php" method="post">
        <p>Name: <input type="text" name="name"></input></p>
        <p><textarea rows="5" cols="100" name="contents"></textarea></p>
        
        <p><input type="submit" value="Submit a new thread"></p>
</form>
<form action="threadlist.php" method="get">
								<p><input type="submit" value="Go back to the thread list"></p>
</form>

</body>
</html>

