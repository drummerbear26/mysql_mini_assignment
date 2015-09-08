<?php
require('../../dbconnect.php');
$title = "MySQL Car Database";
include("header.php");
$row_class = "odd";
// Make Connection
$conn = new mysqli($host, $db_user, $db_pw, $db_name);
// Check Connection
if($conn->connect_error){
	die("Can not establish connection" . $conn->connect_error);
}

// Insert new data
if($_SERVER["REQUEST_METHOD"] == "POST") {
	
	$type = $_POST["vtype"];
	$engine = $_POST["vengine"];
	$year = $_POST["vyear"];
	$fuel = $_POST["vfuel"];
	$model = $_POST["vmodel"];
	$make_id = $_POST["vmake"];
		
	$sql_insert = "INSERT INTO vehicles (id, type, engine, year, fuel, model, make_id) VALUES (NULL, '$type', '$engine', '$year', '$fuel', '$model', '$make_id')";

	if ($conn->query($sql_insert) === TRUE){
	echo "New record created successfully";
	} else {
	echo "Error: " . $sql_insert . "<br />" . $conn_error;
	}
}
if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete_id"])){
	$delete_id = $_GET["delete_id"];
	$sql_delete = "DELETE FROM vehicles WHERE id = '$delete_id'";
	if($conn->query($sql_delete) == TRUE) {
		echo "Record deleted";
	} else {
		echo "Error on delete:" . $sql_delete . "<br />" . $conn->error;
	}
}

$sql = "SELECT vehicles.id, vehicles.type, vehicles.engine, vehicles.year, vehicles.fuel, vehicles.model, makers.name FROM vehicles
			LEFT OUTER JOIN makers
			ON makers.id = vehicles.make_id";
			
$result = $conn->query($sql);

$sql_makers = "SELECT * FROM makers";
$result_makers = $conn->query($sql_makers);

echo "<table class='vehicles'>\n";
echo "<tr class='table_header'>\n";
echo "\t<th>Make</th>\n";
echo "\t\t<th>Model</th>\n";
echo "\t\t<th>Year</th>\n";
echo "\t\t<th>Type</th>\n";
echo "\t\t<th>Engine</th>\n";
echo "\t\t<th>Fuel</th>\n";
echo "\t\t<th>Action</th>\n";
echo "</tr>\n";

if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		if(isset($_GET["update_id"]) && $_GET["update_id"] == $row['id']){
			?>
			<tr class="table_row update">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
					<input type="hidden" name="update_flag" value="<?php echo $row['id']; ?>">
					<td><select name="vmake">
			<?php
				if($result_makers->num_rows > 0){
					while($maker_row = $result_makers->fetch_assoc()){
						echo "<option value='".$maker_row["id"]."'>".$maker_row["name"]."</option>";
					}
				}
			?>
					</select></td>

					<td><input name="vmodel" value="<?php echo $row["model"] ?>"></td>
					<td><input name="vyear" value="<?php echo $row["year"] ?>"></td>
					<td><input name="vengine" value="<?php echo $row["engine"] ?>"></td>
					<td><input name="vtype" value="<?php echo $row["type"] ?>"></td>
					<td><input name="vfuel" value="<?php echo $row["fuel"] ?>"></td>
					<td><button type="submit"> Update row </button></td>
				</form>
			</tr>
			<?php
		} else {
				echo "<tr class='table_row $row_class'>";
				echo "<td>" . $row["name"] . "</td>";
				echo "<td>" . $row["model"] . "</td>";
				echo "<td>" . $row["year"] . "</td>";
				echo "<td>" . $row["type"] . "</td>";
				echo "<td>" . $row["engine"] . "</td>";
				echo "<td>" . $row["fuel"] . "</td>";
				echo "<td>" . " <a href=". $_SERVER["PHP_SELF"] . "?delete_id=" . $row['id'] . "> Delete</a> |"
							   . " <a href=". $_SERVER["PHP_SELF"] . "?update_id=" . $row['id'] . "> Update</a>" . "</td>";
				echo "</tr>";

				if($row_class == "odd"){
					$row_class = "even";
				} else if($row_class == "even"){
					$row_class = "odd";
				}
			}
		}
} else {
	echo "0 results; nope";
}
echo "</table>";
	
$conn->close();
?>
<div class="input_form">
	<br />
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<label for="newCarMake"> Make: 
			<select name="vmake">
				<?php
					if($result_makers->num_rows > 0){
						while($maker_row = $result_makers->fetch_assoc()){
							echo "<option value='".$maker_row["id"]."'>".$maker_row["name"]."</option>";
						}
					}
				?>
			</select>
		</label>
		<label for="newCarModel"> Model: 
			<input type="text" name="vmodel" id="newCarModel" />
		</label>
		<label for="newCarYear"> Year: 
			<input type="text" name="vyear" id="newCarYear" />
		</label>
		<label for="newCarType"> Type: 
			<input type="text" name="vtype" id="newCarType" />
		</label>
		<label for="newCarEngine"> Engine: 
			<input type="text" name="vengine" id="newCarEngine" />
		</label>
		<label for="newCarFuel"> Fuel: 
			<input type="text" name="vfuel" id="newCarFuel" />
		</label>
		<br /><br />
		<button type="submit">Insert new car</button>
	</form>
</div>
<?php
include("footer.php");
?>
