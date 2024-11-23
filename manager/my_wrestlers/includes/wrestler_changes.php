<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); // used to verify session

    $authenticateCheck = "../../functions/authenticated_check.php";
    $enterDB = "../../../db/enterDB.php";

    require_once $authenticateCheck;
    require_once $enterDB;
    
    // Handle deletions
    if (!empty($_POST['deleted_values'])) {
        // Decode the JSON string from the POST data
        $deletedValues = json_decode($_POST['deleted_values'], true); 

        // Check if the decoded values are correct
        if (!empty($deletedValues)) {
            // Prepare the placeholders for the DELETE query
            $placeholders = [];
            $params = [];

            foreach ($deletedValues as $value) {
                // Ensure the data is correctly formatted
                $placeholders[] = "(?, ?, ?, ?, ?)";
                array_push($params, $value['first_name'], $value['last_name'], $value['weight'], $value['school_name'], $value['school_abbrv']);
            }

            // Prepare the DELETE query
            $deleteQuery = "DELETE FROM head_coach_wrestlers WHERE (first_name, last_name, weight, school_name, school_abbrv) IN (" . implode(',', $placeholders) . ")";
            $stmt = $pdo->prepare($deleteQuery);

            // Check if the query executed successfully
            if ($stmt->execute($params)) {
                echo "Rows deleted successfully.";
            } else {
                echo "Error deleting rows.";
            }

            // Optionally, delete from the `all_wrestlers` table as well
            $deleteQuery = "DELETE FROM all_wrestlers WHERE (first_name, last_name, weight, school_name, school_abbrv) IN (" . implode(',', $placeholders) . ")";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute($params); // Execute the deletion query

        } else {
            echo "No values to delete.";
        }
    } else {
        echo "No deleted values received.";
    }
} else {
    header('Location: ../../index.html');
    exit();
}