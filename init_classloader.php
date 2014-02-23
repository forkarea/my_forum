<?php
include_once 'SplClassLoader/SplClassLoader.php';
$classLoader = new SplClassLoader('classes', '.'); 
$classLoader->register();
 
