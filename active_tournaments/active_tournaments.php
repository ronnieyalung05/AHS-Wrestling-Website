<?php
$enterDB = "../db/enterDB.php";

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
    <link rel="shortcut icon" type="x-icon" href=""> </link>
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
        <h1>sorry, there are no tournaments active at the moment</h1>
    <?php endif; ?>


    


    <script src="functions.js"></script>
</body>


</html>