<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '512M');

echo "Before wp-load: " . ini_get('memory_limit') . "<br>";
require_once('wp-load.php');
echo "After wp-load: " . ini_get('memory_limit') . "<br>";