
<?php
/**
 *  * This code will benchmark your server to determine how high of a cost you can
 *   * afford. You want to set the highest cost that you can without slowing down
 *    * you server too much. 10 is a good baseline, and more is good if your servers
 *     * are fast enough.
 *      */
        include_once "password_compat/lib/password.php";
$timeTarget = 0.2;

$cost = 4;
do {
            $cost++;
                $start = microtime(true);
                $ar = array( "cost" => $cost );
                password_hash("test", PASSWORD_BCRYPT, $ar);
                    $end = microtime(true);
} while (($end - $start) < $timeTarget);

echo "Appropriate Cost Found: " . $cost . "\n";
?>

