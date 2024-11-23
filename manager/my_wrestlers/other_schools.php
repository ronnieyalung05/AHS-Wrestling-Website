<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session
$authenticateCheck = "../functions/authenticated_check.php";
$enterDB = "../../db/enterDB.php";

require_once $authenticateCheck;
require_once $enterDB;

// Initialize an array to hold school data
$all_school_data = [];

try {
    // Query to get all columns except 'id' from the 'schools' table
    $sql = "SELECT id, school_name, school_abbrv, pwd FROM `schools`"; // Adjust the columns as needed
    $stmt = $pdo->query($sql);

    // Fetch all the data from the table and add it to the array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Store the row data
        $all_school_data[] = $row;
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


// Generate a random password of a given length
function generateRandomPassword($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&?0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}




// Handle adding a new school
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_school'])) {
    $schoolName = substr(trim($_POST['school_name']), 0, 60);
    $schoolAbbrv = substr(trim($_POST['school_abbrv']), 0, 6);
    $accessCode = generateRandomPassword(10);
    $schoolName = str_replace(' ', '_', $schoolName); // remove spaces
    

    try {
        $stmt->execute();

        // Check if the table already exists
        $tableName = "{$schoolName}_wrestlers";
        $checkTableSQL = "SELECT COUNT(*) FROM information_schema.tables 
                          WHERE table_schema = :dbName AND table_name = :tableName";
        $checkTableStmt = $pdo->prepare($checkTableSQL);
        $checkTableStmt->bindValue(':dbName', $pdo->query("SELECT DATABASE()")->fetchColumn());
        $checkTableStmt->bindValue(':tableName', $tableName);
        $checkTableStmt->execute();
        $tableExists = $checkTableStmt->fetchColumn() > 0;

        if (!$tableExists) {
            // Create the table if it doesn't exist
            $newSchool = "CREATE TABLE `{$tableName}` (
                id INT(11) NOT NULL AUTO_INCREMENT,
                first_name VARCHAR(20) NOT NULL,
                last_name VARCHAR(20) NOT NULL,
                wrestler_weight DECIMAL(4,1) NOT NULL,
                school_name VARCHAR(50) NOT NULL,
                school_abbrv VARCHAR(6) NOT NULL,
                wins INT(2) DEFAULT 0 NOT NULL,
                losses INT(2) DEFAULT 0 NOT NULL,
                PRIMARY KEY(id)
            )";

            $addTable = $pdo->prepare($newSchool);
            $addTable->execute();

            // Insert the new school into the schools table
            $sql = "INSERT INTO schools (school_name, school_abbrv, pwd) VALUES (:school_name, :school_abbrv, :pwd)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':school_name', $schoolName);
            $stmt->bindParam(':school_abbrv', $schoolAbbrv);
            $stmt->bindParam(':pwd', $accessCode);

            echo "Table `{$tableName}` created successfully.";
        } else {
            echo "Table `{$tableName}` already exists.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
 


    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']); // Refresh page
        //exit();
    } else {
        echo "<p>Error adding school. Please try again.</p>";
    }
}

// Handle deleting a school
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_school'])) {
    $schoolId = $_POST['school_id'];
    $schoolName = str_replace(' ', '_', $_POST['school_name']);

    try {
        // Delete the school from the database
        $sql = "DELETE FROM schools WHERE id = :school_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':school_id', $schoolId, PDO::PARAM_INT);
        $stmt->execute();

        // Sanitize the table name before using it in the query
        $schoolName = preg_replace('/[^a-zA-Z0-9_]/', '', $schoolName);

        // Drop the corresponding table
        $sql = "DROP TABLE IF EXISTS `{$schoolName}_wrestlers`"; // Use dynamic table name
        $pdo->exec($sql); // Direct execution since prepared statements don't work for table names

        header("Location: " . $_SERVER['PHP_SELF']); // Refresh the page
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
<?php
// Display the school data
echo "<h2>Schools</h2>";
echo "<table border='1'>";
echo "<h4>Schools in tournament:</h4><tr>";

// Display headers
if (!empty($all_school_data)) {
    foreach (array_keys($all_school_data[0]) as $column_name) {
        if ($column_name !== 'id') { // Exclude 'id' column
            echo "<th>" . htmlspecialchars(ucwords(str_replace('_', ' ', $column_name))) . "</th>";
        }
    }
}
echo "</tr>";

// Display each row of data, excluding 'id'
foreach ($all_school_data as $school) {
    echo "<tr>";
    foreach ($school as $column => $value) {
        if ($column !== 'id') { // Exclude 'id' column
            echo "<td>" . str_replace('_', ' ', htmlspecialchars($value)) . "</td>";
        }
    }
    echo "</tr>";
}

echo "</table><br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arroyo Dons Wrestling | Add Schools</title>
    <link rel="shortcut icon" type="x-icon" href="">
    <link rel="stylesheet" href="">
</head>
<body>

<!-- Add School Form -->
<h2>Add School</h2>
<form action="" method="POST">  <!-- action to redirect.php -->
    <label for="school_name">School Name:</label>
    <input type="text" id="school_name" name="school_name" maxlength="60" autocomplete="off" required>
    <br><br>

    <label for="school_abbrv">School Abbreviation:</label>
    <input type="text" id="school_abbrv" name="school_abbrv" maxlength="6" autocomplete="off" required>
    <br><br>

    <button type="submit" name="add_school">Add School</button>
</form>

<!-- Display Schools with Delete Buttons -->
<h2>Existing Schools</h2>
<table>
    <tr>
        <th>School Name</th>
        <th>School Abbreviation</th>
        <th>Access Code</th>
        <th>Action</th>
    </tr>
    <?php foreach ($all_school_data as $index => $school): ?>
        <tr>
            <td><?= htmlspecialchars($school['school_name']) ?></td>
            <td><?= htmlspecialchars($school['school_abbrv']) ?></td>
            <td><?= htmlspecialchars($school['pwd']) ?></td>
            <td>
                <?php if ($index > 0): // Skip delete button for the first row ?>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="school_id" value="<?= $school['id'] ?>">
                        <input type="hidden" name="school_name" value="<?=$school['school_name']?>">
                        <button type="submit" name="delete_school">Delete</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Back Button Form -->
<form action="my_wrestlers.php" method="post">
    <button>back</button>
</form>

</body>
</html>