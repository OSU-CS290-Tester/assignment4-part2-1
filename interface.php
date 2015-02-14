<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "thomasw-db", "", "thomasw-db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection worked!<br>";
}

if (!($stmt = $mysqli->prepare("INSERT INTO store_inventory(name, category, length) VALUES (?,?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

if (isset($_POST['title'])) {
$name = $_POST['title'];
$category = $_POST['category'];
$length = $_POST['length'];
}

if (!$stmt->bind_param("sss", $name, $category, $length)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (isset($_POST['title'])) {
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

?>