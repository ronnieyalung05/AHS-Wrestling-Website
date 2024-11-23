<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // used to check if user is authenticated

// check if user is authenticated & enter our db
$authenticateCheck = "../manager/functions/authenticated_check.php";
$enterDB = "../db/enterDB.php";
require_once $authenticateCheck;
require_once $enterDB;


if (isset($_SESSION['school_name'])) {
    $schoolName = $_SESSION['school_name']; // Retrieve the value
    echo "The school name is: " . htmlspecialchars($schoolName); // Use it as needed
} else {
    echo "No school name is set in the session.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="shortcut icon" type="x-icon" href=""> </link>

    <link rel="stylesheet" href="">
</head>

<body>
    
    <section id="info-section">
        <div>
            <h2>Tournament:  </h2>
            <h4>School name: </h4>
            <h4>School abbreviation: </h4>
        </div>
    </section>

    <a href="../../home/home.html"><button>Back to Home</button></a>


    <section id="wrestlers-section">
        <div id="add-wrestlers-form">
            <form action="includes/add_wrestlers.php" method="post" id="add-wrestler-form">
                <table id="add-wrestler-table">

                    <thead class="row-headers-container">
                        <th class="row-header">First Name</th>
                        <th class="row-header">Last Name</th>
                        <th class="row-header">Weight</th>
                    </thead>

                    <tbody id="wrestler-table-body">
                        <tr class="table-row" id="template-row">

                            <td class="table-cell">
                                <input type="text" name="first-name[]" placeholder="First Name" class="input-cell" autocomplete="off" required>
                            </td>

                            <td class="table-row">
                                <input type="text" name="last-name[]" placeholder="Last Name" class="input-cell" autocomplete="off" required>
                            </td>

                            <td class="table-row">
                                <input type="number" name="weight[]" placeholder="Weight" class="input-cell"  min="70" max="300" step="0.1" autocomplete="off" required>
                            </td>

                            <td>
                                <button class="delete-btn" onclick="removeRow_addForm(this)"> DELETE </button>
                            </td>

                        </tr>

                        <div>
                            <button class="submit-form-btn"> Submit Wrestlers </button>
                        </div>
                    </tbody>
                </table>

            </form>

            <button id="add-row"> + Add Row </button>
        </div>





        <div id="my-wrestlers-form">
            <?php
                $table = $schoolName . "_wrestlers"; // table name for HC Wrestlers

                // Query to fetch all rows from the specified table
                $sql = "SELECT * FROM $table";
                $stmt = $pdo->query($sql);

                // Initialize an array to hold the row data
                $row_data = [];

                // Fetch row data and store in the array
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Optionally, you can process or sanitize the row data
                    $row_data[] = $row; // Store the entire row as an associative array
                }
                $original_data_json = json_encode($row_data);
            ?>


            <h1>My Wrestlers</h1>

            <form action="includes/wrestler_changes.php" method="POST" id="wrestler-changes">
                <table>
                    <thead>
                        <tr>
                            <?php if (!empty($row_data)): ?>
                                <?php foreach (array_keys($row_data[0]) as $column_name): ?>
                                    <?php if ($column_name !== 'id' && $column_name !== 'wins' && $column_name !== 'losses'): ?>
                                        <th><?= htmlspecialchars(str_replace('_', ' ', $column_name)) ?></th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <?php if (!empty($row_data)): ?>
                            <?php foreach ($row_data as $row): ?>
                                <tr data-id="<?= htmlspecialchars($row['id']) ?>">
                                    <?php foreach ($row as $column_name => $cell): ?>
                                        <?php if ($column_name === 'school_name' || $column_name === 'school_abbrv' || $column_name === 'first_name' || $column_name === 'last_name' || $column_name === 'weight'): ?>
                                            <td><input type="text" name="data[<?= $row['id'] ?>][<?= htmlspecialchars($column_name) ?>]" value="<?= htmlspecialchars($cell) ?>" readonly></td>
                                        <?php elseif ($column_name !== 'id' && $column_name !== 'wins' && $column_name !== 'losses'): ?>
                                            <td><input type="text" name="data[<?= $row['id'] ?>][<?= htmlspecialchars($column_name) ?>]" value="<?= htmlspecialchars($cell) ?>"></td>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <td><button type="button" class="delete-btn" onclick="removeRow(this)">DELETE</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="100%">No data found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <input type="hidden" name="deleted_ids" id="deleted_ids" value="">
                <input type="hidden" name="tourney-name-input" value="<?php echo htmlspecialchars($tournamentName)?>">
                <input type="hidden" name="school-name-input" value="<?php echo htmlspecialchars($schoolName)?>">
                <input type="hidden" name="school-abbrv-input" value="<?php echo htmlspecialchars($schoolAbbrv)?>">
                
                <div class="form-actions">
                    <button type="submit" name="save_changes">Save Changes</button>
                    <button type="button" onclick="resetTable()">Reset</button>
                </div>
            </form>

            <script>
                const originalData = <?= $original_data_json ?>;
                const deletedIds = [];

                function removeRow(button) {
                    const row = button.closest('tr');
                    const id = row.getAttribute('data-id'); // Get the row's ID

                    if (id) { 
                        // Add the ID to the deleted array if it's valid
                        deletedIds.push(id);
                        document.getElementById('deleted_ids').value = JSON.stringify(deletedIds); // Update the hidden input
                    }

                    row.remove(); // Completely remove the row from the DOM
                }

                function resetTable() {
                    // Reset the table and deletedIds logic
                    const tableBody = document.getElementById('table-body');
                    tableBody.innerHTML = ''; // Clear current rows

                    originalData.forEach(row => {
                        const tr = document.createElement('tr');
                        tr.setAttribute('data-id', row.id); // Set data-id attribute

                        for (const column_name in row) {
                            if (column_name !== 'id' && column_name !== 'wins' && column_name !== 'losses') {
                                const td = document.createElement('td');
                                const input = document.createElement('input');
                                input.type = 'text';
                                input.name = `data[${row.id}][${column_name}]`;
                                input.value = row[column_name]; // Set the original value
                                td.appendChild(input);
                                tr.appendChild(td);
                            }
                        }

                        // Add the delete button
                        const actionTd = document.createElement('td');
                        const deleteButton = document.createElement('button');
                        deleteButton.type = 'button';
                        deleteButton.className = 'delete-btn';
                        deleteButton.textContent = 'DELETE';
                        deleteButton.onclick = function() { removeRow(this); };
                        actionTd.appendChild(deleteButton);
                        tr.appendChild(actionTd);
                        tableBody.appendChild(tr);
                    });

                    // Reset deleted IDs
                    deletedIds.length = 0; // Clear the deletedIds array
                    document.getElementById('deleted_ids').value = ''; // Reset the hidden input
                }
            </script>
        </div>


    </section>


    <br>







    <script src="js_functions/add_wrestler_row.js"></script>


    
</body>

</html>