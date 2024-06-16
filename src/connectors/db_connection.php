<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';
$db_name = 'calendar';

$db_server = "mysql:host=$db_host;dbname=$db_name";

try {
	$dbconnection = new PDO($db_server, $db_user, $db_pass);
}
catch (PDOException $e) {
	print $e->getMessage();
}

?>