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

//Check if value was posted and sets bound variables values. After submitting data
//The form rediects to this page with values passed via POST
if (isset($_POST['title'])) {
	//prepared statement to input values into database.
	if (!($stmt = $mysqli->prepare("INSERT INTO store_inventory(name, category, length) VALUES (?,?,?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	$name = $_POST['title'];
	$category = $_POST['category'];
	$length = $_POST['length'];

	//Binds variables 
	if (!$stmt->bind_param("sss", $name, $category, $length)) {
   		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
    	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	} 
}

//If the delete button is called it redirects to this page passing that ID via url
//This row is then deleted from the database via prepared statement.
if(isset($_GET['deleted'])) {
	$deleteID = $_GET['deleted'];
	if (!($stmt = $mysqli->prepare("DELETE FROM store_inventory WHERE id = ?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

	if (!$stmt->bind_param("i", $deleteID)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}

	if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	} 
}

//If the check in/out button is pressed, it passes values via url.
//These values are checked here and a prepared statement is executed to 
//switch the values on the database.
if(isset($_GET['rented'])) {
	$rentalID = $_GET['rented'];
	$status = $_GET['status'];
	$newStatus = NULL;
	if ($status == 1) {
		$newStatus = 0;
	} else {
		$newStatus = 1;
	}

	if (!($stmt = $mysqli->prepare("UPDATE store_inventory SET rented = ? WHERE id = ?"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param("ii", $newStatus, $rentalID)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	}
	if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
	} 
}

//If delete all button is pressed this action occurs.
if(isset($_POST['delete_all'])) {
	!$mysqli->query("DELETE FROM store_inventory");
}

//Form to add videos.
echo "<h2>Add a video into inventory</h2>";
echo "<form action = 'interface.php' method = 'post'>";
echo "Title: <input name = 'title' type = 'text'><br>";
echo "Cateory: <input name = 'category' type = 'text'><br>";
echo "Length: <input name = 'length' type = 'text'><br>";
echo "<input name = 'add_movie' type = 'submit' value = 'Add Movie'>";
echo "</form>";
?>

<h2>Delete all Records</h2>
<form action = 'interface.php' method = 'post'>
<input type = 'hidden' name = 'delete_all' value = 1>
<input type = 'submit' value = 'Delete All'>
</form>

<h2>Store Inventory</h2>

<?php
$menu_cat = NULL;

//This section creates a drop down menu to filter by category. Pulls all categories
//And creates options in a while loop.
if (!($stmt = $mysqli->prepare("SELECT category FROM store_inventory"))) {
   echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}
if (!$stmt->execute()) {
  	echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
if (!$stmt->bind_result($menu_cat)) {
    echo "Binding output parameters failed: (" . $stmt->errno . ") " . $stmt->error;
}
echo "<form action = 'interface.php' method = 'GET'>";
echo "<select name = 'filter'>";
echo "<option value = All>All</option>";
while ($stmt->fetch()) {
	echo "<option value =" . $menu_cat . ">" . $menu_cat . "</option>";
}
echo "<input type = 'submit' value = 'Filter'>";
echo "</form>";


//This section resets prepared statement to fetch data from database and print to the table.
$cat_filter = 'All';
//If user has passed a filter choice it sets variable here, otherwise defaults to all.
if(isset($_GET['filter'])) {
	$cat_filter = $_GET['filter'];
} 

if($cat_filter == 'All') {
	if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM store_inventory ORDER BY name"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
} else {
	if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM store_inventory WHERE category = ? ORDER BY name"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	if (!$stmt->bind_param("s", $cat_filter)) {
    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
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
echo "<th>Status</th>";
echo "<th>Delete</th>";
while ($stmt->fetch()) {
	if ($out_rented == 1) {
		$status = "Checked in";
	} else {
		$status = "Checked out";
	}
echo "<tr>";
echo "<td>" . $out_name . "</td>";
echo "<td>" . $out_cateory . "</td>";
echo "<td>" . $out_length . "</td>";
echo "<td>" . $status . "</td>";
echo "<td><a href='interface.php?rented=$out_id&status=$out_rented'><button>Check in/out</button></a></td>";
echo "<td><a href='interface.php?deleted=$out_id'><button>Delete</button></a></td>";
echo "</tr>";
}
echo "</table>";

?>