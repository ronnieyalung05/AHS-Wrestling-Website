<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start(); // used to verify session

    $authenticateCheck = "../../functions/authenticated_check.php";
    $enterDB = "../../../db/enterDB.php";

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

    // Get HC school name and abbrv
    $stmt = $pdo->prepare("SELECT school_name, school_abbrv FROM schools ORDER BY id ASC LIMIT 1");
    $stmt->execute();
    $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);

    $schoolName = $firstRow['school_name'];
    $schoolAbbrv = $firstRow['school_abbrv'];


    for ($i = 0; $i < $wrestlerCount; $i++) {
        $query = "INSERT INTO head_coach_wrestlers 
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

<form id="redirectForm" method="POST" action="../my_wrestlers.php"></form>

<script type="text/javascript">
    document.getElementById("redirectForm").submit();
</script>