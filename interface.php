<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Connect to sql server
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thomasw-db", "s824hShW4EKidis5", "thomasw-db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection worked!<br>";
}

//prepared statement to input values into database.
if (!($stmt = $mysqli->prepare("INSERT INTO store_inventory(name, category, length) VALUES (?,?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

//Check if value was posted and sets bound variables values
if (isset($_POST['title'])) {
$name = $_POST['title'];
$category = $_POST['category'];
$length = $_POST['length'];
}

//Binds variables 
if (!$stmt->bind_param("sss", $name, $category, $length)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

//If input is valid, executes statement and adds content to database
if (isset($_POST['title']) ) {
	if (!$stmt->execute()) {
    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	} 
}

echo "<h2>Add a video into inventory</h2>";
echo "<form action = 'interface.php' method = 'post'>";
echo "Title: <input name = 'title' type = 'text'><br>";
echo "Cateory: <input name = 'category' type = 'text'><br>";
echo "Lenth: <input name = 'length' type = 'text'><br>";
echo "<input name = 'add_movie' type = 'submit' value = 'Add Movie'>";
echo "</form>";

echo "<h2>Store Inventory</h2>";

//This section resets prepared statement to fetch data from database and print to the table.
$cat_filter = 'all';

if(isset($_GET['filter'])) {
	$cat_filter = $_GET['filter'];
} else {
	$cat_filter = 'all';
}

if($cat_filter == 'all') {
	if (!($stmt = $mysqli->prepare("SELECT name, category, length, rented FROM store_inventory"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
}

$out_name = NULL;
$out_cateory = NULL;
$out_length = NULL;
$out_rented = NULL;

if (!$stmt->execute()) {
  	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!$stmt->bind_result($out_name, $out_cateory, $out_length, $out_rented)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

if ($rented == 0) {
	$rented = "Checked in";
} else {
	$rented = "Checked out";
}

echo "<table border>";
while ($stmt->fetch()) {
echo "<tr>";
echo "<td>" . $out_name . "</td>";
echo "<td>" . $out_cateory . "</td>";
echo "<td>" . $out_length . "</td>";
echo "<td>" . $out_rented . "</td>";
echo "</tr>";
}
echo "</table>";



?>