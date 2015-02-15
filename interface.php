<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Connect to sql server
$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thomasw-db", "", "thomasw-db");
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
	if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM store_inventory"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
}

$out_id = NULL;
$out_name = NULL;
$out_cateory = NULL;
$out_length = NULL;
$out_rented = NULL;

if (!$stmt->execute()) {
  	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

if (!$stmt->bind_result($out_id, $out_name, $out_cateory, $out_length, $out_rented)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}

echo "<table border>";
echo "<th>Title</th>";
echo "<th>Category</th>";
echo "<th>Length</th>";
echo "<th>Rented</th>";
echo "<th>Check in/out</th>";
echo "<th>Delete</th>";
while ($stmt->fetch()) {
	if ($out_rented == 1) {
		$out_rented = "Checked in";
	} else {
		$out_rented = "Checked out";
	}
echo "<tr>";
echo "<td>" . $out_name . "</td>";
echo "<td>" . $out_cateory . "</td>";
echo "<td>" . $out_length . "</td>";
echo "<td>" . $out_rented . "</td>";
echo "<td><a href='interface.php?rented=$out_id'><button>Check in/out</button></a></td>";
echo "<td><a href='interface.php?deleted=$out_id'><button>Delete</button></a></td>";
echo "</tr>";
}
echo "</table>";



?>