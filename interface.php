<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thomasw-db", "", "thomasw-db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection worked!<br>";
}

?>