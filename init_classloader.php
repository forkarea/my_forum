<?php
include_once 'SplClassLoader/SplClassLoader.php';
$classLoader = new SplClassLoader('domain', '.'); 
$classLoader->register();

$utilityClassLoader = new SplClassLoader('utility', '.'); 
$utilityClassLoader->register();
