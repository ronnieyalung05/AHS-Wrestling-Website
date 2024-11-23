<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start(); // used to verify session

    $authenticateCheck = "../../manager/functions/authenticated_check.php";
    $enterDB = "../../db/enterDB.php";

    require_once $authenticateCheck;
    require_once $enterDB;

    // get this school's name
    if (isset($_SESSION['school_name'])) {
        $schoolName = $_SESSION['school_name']; // Retrieve the value
        echo "The school name is: " . htmlspecialchars($schoolName); // Use it as needed
    } else {
        echo "No school name is set in the session.";
    }
    
    $tableName = $schoolName . "_wrestlers"; // Dynamically create the table name

    // Handle deletions
    if (!empty($_POST['deleted_ids'])) {
        $deletedIds = json_decode($_POST['deleted_ids'], true); // Decoding the JSON string
        
        if (!empty($deletedIds)) {
            $placeholders = implode(',', array_fill(0, count($deletedIds), '?'));

            $deleteQuery = "DELETE FROM {$tableName} WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute($deletedIds); // Execute deletion query



            // DONT DELETE BY ID BELOW, DELETE BY VALUES


            // Delete from other tables if necessary (all_wrestlers)
            $deleteQuery = "DELETE FROM all_wrestlers WHERE id IN ($placeholders)";
            $stmt = $pdo->prepare($deleteQuery);
            $stmt->execute($deletedIds); // Execute deletion query





        }
    }
} else {
    header('Location: ../../home/home.html');
    exit();
}
?>
<form id="redirectForm" method="POST" action="../coach_wrestlers.php"></form>

<script type="text/javascript">
    document.getElementById("redirectForm").submit();
</script>