<?php
include_once 'SplClassLoader/SplClassLoader.php';
$classLoader = new SplClassLoader('domain', '.'); 
$classLoader->register();

$utilityClassLoader = new SplClassLoader('utility', '.'); 
$utilityClassLoader->register();

if (PHP_VERSION_ID < 50500) {
        //fallback for functions unavailable in PHP <5.5
        include_once './password_compat/lib/password.php';
}
