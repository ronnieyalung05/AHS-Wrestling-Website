<?php
session_start();    // used to check if user is authenticated

// check if user is authenticated & enter our db
$authenticateCheck = "../functions/authenticated_check.php";
$enterDB = "../../db/enterDB.php";

require_once $authenticateCheck;
require_once $enterDB;


// Check to see if an active tournament exists
$query = "SELECT * FROM activity WHERE type = 'tournament_exists' AND active = true LIMIT 1";
$stmt = $pdo->prepare($query);
$stmt->execute();

// Check if the tournament exists
$tournamentExists = $stmt->fetch(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arroyo Dons Wrestling</title>
    <link rel="shortcut icon" type="x-icon" href="../../imgs/ArroyoDonsLogo.png"> </link>
</head>


<body>

    <div class="welcome-msg">
        <h3 id="welcome">Welcome head coach!</h3>
    </div>

    <!-- Display create or enter tournament -->
    <?php if ($tournamentExists): ?>
        <!-- Tournament exists -->
        <h2> Tournament Exists: </h2>
        <form method="post">
            <button type="submit" formaction='includes/enter.php' name="enter">Enter</button>
            <button type="submit" formaction='includes/delete.php'name="delete" onclick="return confirmDelete()">Delete</button>
            <!-- get a confirmation message when we try to delete a tournament -->
        </form>
    <?php else: ?>
        <!-- No active tournament -->
        <form action="includes/create.php" method="post">
            <label for="tournamentName">Tournament Name:</label>
            <input type="text" id="tournament_name" name="tournament_name" autocomplete="off" maxlength="50" required>

            <label for="schoolName">School Name:</label>
            <input type="text" id="school_name" name="school_name" autocomplete='off' maxlength="50" required>

            <label for="tournamentName">School Abbreviation:</label>
            <input type="text" id="school_abbrv" name="school_abbrv" autocomplete='off' maxlength="6" required>

            <button type="submit" name="create">Create</button>
        </form>
    <?php endif; ?>


    


    <script src="functions.js"></script>
</body>


</html>