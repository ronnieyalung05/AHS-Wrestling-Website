<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start(); // used to verify session

    $authenticateCheck = "../../manager/functions/authenticated_check.php";
    $enterDB = "../../db/enterDB.php";

    require_once $authenticateCheck;
    require_once $enterDB;

    if (!isset($_POST['first-name']) || count($_POST['first-name']) === 0) {
        ?>
        <form id="redirectForm" method="POST" action="../my_wrestlers.php"></form>

        <script type="text/javascript">
            document.getElementById("redirectForm").submit();
        </script>
        <?php
    }

    $firstNames = $_POST["first-name"];
    $lastNames = $_POST["last-name"];
    $weights = $_POST["weight"];

    $wrestlerCount = count($firstNames);
    $defaultWinLossValue = 0;

    // get this school's name
    if (isset($_SESSION['school_name'])) {
        $schoolName = $_SESSION['school_name']; // Retrieve the value
        echo "The school name is: " . htmlspecialchars($schoolName); // Use it as needed
    } else {
        echo "No school name is set in the session.";
    }

    // Use the session school name to fetch the school's name and abbreviation from the database
    $stmt = $pdo->prepare("SELECT school_name, school_abbrv FROM schools WHERE school_name = :school_name");
    $stmt->bindParam(':school_name', $schoolName);
    $stmt->execute();

    $schoolData = $stmt->fetch(PDO::FETCH_ASSOC);

    $schoolName = $schoolData['school_name']; // Retrieve the school's name
    $schoolAbbrv = $schoolData['school_abbrv']; // Retrieve the school's abbreviation

    // Prepare the dynamic table name
    $tableName = $schoolName . "_wrestlers"; // Dynamically create the table name



    for ($i = 0; $i < $wrestlerCount; $i++) {
        $query = "INSERT INTO {$tableName} 
        (first_name, last_name, wrestler_weight, school_name, school_abbrv, wins, losses)
        VALUES (?, ?, ?, ?, ?, ?, ?);";

        $stmt = $pdo->prepare($query);

        $stmt->execute([
            $firstNames[$i],
            $lastNames[$i],
            $weights[$i],
            $schoolName,
            $schoolAbbrv,
            $defaultWinLossValue,
            $defaultWinLossValue
        ]);

        $stmt = null;

        $query = "INSERT INTO all_wrestlers 
        (first_name, last_name, wrestler_weight, school_name, school_abbrv, wins, losses)
        VALUES (?, ?, ?, ?, ?, ?, ?);";

        $stmt = $pdo->prepare($query);

        $stmt->execute([
            $firstNames[$i],
            $lastNames[$i],
            $weights[$i],
            $schoolName,
            $schoolAbbrv,
            $defaultWinLossValue,
            $defaultWinLossValue
        ]);

        $stmt = null;
    }

} else {
    exit;
}
?>

<form id="redirectForm" method="POST" action="../coach_wrestlers.php"></form>

<script type="text/javascript">
    document.getElementById("redirectForm").submit();
</script>