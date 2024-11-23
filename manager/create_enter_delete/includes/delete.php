<?php
session_start();    // used to check if user is authenticated

// check if user is authenticated & enter our db
$authenticateCheck = "../../functions/authenticated_check.php";
$enterDB = "../../../db/enterDB.php";

require_once $authenticateCheck;
require_once $enterDB;

// What needs to happen to delete tournament? look below

try {
    // Step 1: Delete all tables except 'activity' and 'tournament_name'
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        if ($table !== 'activity' && $table !== 'tournament_name' && $table !== 'schools') {
            $pdo->exec("DROP TABLE `$table`");
        }
    }
    echo "All other tables deleted successfully except 'activity' and 'tournament_name'.<br>";

    // Step 2: Delete all values in 'tournament_name' table
    $pdo->exec("DELETE FROM tournament_name");

    // Step 3: Alter 'activity' table to set 'active' to false
    $pdo->exec("UPDATE activity SET active = FALSE WHERE type = 'tournament_exists'");

    // Step 3.5: Alter 'activity' table to set active to false for tournament_started
    $pdo->exec("UPDATE activity SET active = FALSE WHERE type = 'tournament_started'");

    // Step 14: Delete all other rows from schools except the first one.
    $stmt = $pdo->prepare("SELECT id FROM schools ORDER BY id ASC LIMIT 1");
    $stmt->execute();
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);


    $stmt = $pdo->prepare("DELETE FROM schools WHERE id > :id");
    $stmt->bindParam(':id', $firstRow['id'], PDO::PARAM_INT);
    $stmt->execute();



} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

header("Location: ../create_enter_delete.php");