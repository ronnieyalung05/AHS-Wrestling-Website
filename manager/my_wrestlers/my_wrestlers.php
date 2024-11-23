<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // used to check if user is authenticated

// check if user is authenticated & enter our db
$authenticateCheck = "../functions/authenticated_check.php";
$enterDB = "../../db/enterDB.php";
require_once $authenticateCheck;
require_once $enterDB;


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
                $table = 'head_coach_wrestlers'; // table name for HC Wrestlers

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

                <input type="hidden" name="deleted_values" id="deleted_data" value="">
                
                <div class="form-actions">
                    <button type="submit" name="save_changes">Save Changes</button>
                    <button type="button" onclick="resetTable()">Reset</button>
                </div>
            </form>

            <script>
                const originalData = <?= $original_data_json ?>;
                const deletedData = [];

                function removeRow(button) {
                    const row = button.closest('tr');
                    // Extract data from the row before removing it
                    const data = {
                        first_name: row.querySelector('[name*="first_name"]') ? row.querySelector('[name*="first_name"]').value : '',
                        last_name: row.querySelector('[name*="last_name"]') ? row.querySelector('[name*="last_name"]').value : '',
                        weight: row.querySelector('[name*="weight"]') ? row.querySelector('[name*="weight"]').value : '',
                        school_name: row.querySelector('[name*="school_name"]') ? row.querySelector('[name*="school_name"]').value : '',
                        school_abbrv: row.querySelector('[name*="school_abbrv"]') ? row.querySelector('[name*="school_abbrv"]').value : ''
                    };

                    // Add the data to the deleted array
                    deletedData.push(data);
                    document.getElementById('deleted_data').value = JSON.stringify(deletedData); // Update the hidden input


                    row.remove(); // Completely remove the row from the DOM
                }

                function resetTable() {
                    // Reset the table and deletedData logic
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

                    // Reset deleted data
                    deletedData.length = 0; // Clear the deletedData array
                    document.getElementById('deleted_data').value = ''; // Reset the hidden input
                }
            </script>
        </div>


    </section>


    <br>
    <section id="code+brackets+tourney-section">

        <form action="other_schools.php" method="post"><button type="submit">Add/View Schools</button></form>
        <form action="" method="post"><button type="submit">Create Brackets</button></form>
        <form action="" method="post"><button type="submit">View Brackets</button></form>
        <form action="" method="post"><button type="submit">Start Tournament</button></form>
    </section>






    <script src="js_functions/add_wrestler_row.js"></script>


    
</body>

</html>



