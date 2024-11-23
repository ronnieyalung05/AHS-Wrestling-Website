<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accessCode = trim($_POST["access-code"]); // Sanitize the input

    // Include the database connection file
    $enterDbPath = "../../db/enterDB.php";
    require_once $enterDbPath;

    try {
        // Prepare the query to retrieve the head coach's password
        $stmt = $pdo->prepare("SELECT pwd FROM schools ORDER BY id ASC LIMIT 1");
        $stmt->execute();

        // Fetch the result for the head coach's password
        $dbCode = $stmt->fetch(PDO::FETCH_ASSOC);
        $hcCode = $dbCode['pwd'];

        // Prepare the query to check for a coach's code and fetch the associated school name
        $stmt = $pdo->prepare("SELECT pwd, school_name FROM schools WHERE pwd = :access_code");
        $stmt->bindParam(':access_code', $accessCode);
        $stmt->execute();
        $coachData = $stmt->fetch(PDO::FETCH_ASSOC);

        /*
            $coachData = [
                'pwd' => 'some_password',
                'school_name' => 'Some School'
            ];
        */

        // Determine if the access code matches
        if ($accessCode === $hcCode) {
            $_SESSION['authenticated'] = true; // Head coach authenticated
            header("Location: ../../manager/create_enter_delete/create_enter_delete.php"); // Redirect to the manager page
            exit();
        } else if ($coachData) {
            $schoolName = $coachData['school_name']; // Fetch the school name
            $_SESSION['authenticated'] = true; // Set the session
            $_SESSION['school_name'] = $schoolName; // Store the school name in the session if needed

            header("Location: ../../other_coaches/coach_wrestlers.php"); // Redirect to coach dashboard
            exit();
        } else {
            // Invalid access code
            header("Location: ../login.php?error=1"); // Redirect back to login page with error
            exit();
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    // Redirect to the home page if accessed directly
    header("Location: ../../home/home.html");
    exit();
}
