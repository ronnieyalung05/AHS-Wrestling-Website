<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // enter db
    $enterDbPath = "../../../db/enterDB.php";
    require_once $enterDbPath;

     // retrieve info set by user
    $tournamentName = $_POST["tournament_name"];
    $schoolName = str_replace(' ', '_', $_POST["school_name"]);
    $schoolAbbrv = str_replace(' ', '_', $_POST["school_abbrv"]);

    // insert tournament name into table
    $query = "INSERT INTO tournament_name (name, active) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        $tournamentName,
        TRUE
    ]);

    // update tournament_exists from false to true;
    $query = "UPDATE activity SET active = TRUE WHERE type = 'tournament_exists'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // query create tables for all wrestlers' info
    $all_wrestlers = "CREATE TABLE all_wrestlers (
        id INT(11) NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        wrestler_weight DECIMAL(4,1) NOT NULL,
        school_name VARCHAR(50) NOT NULL,
        school_abbrv VARCHAR(6) NOT NULL,
        wins INT(2) DEFAULT 0 NOT NULL,
        losses INT(2) DEFAULT 0 NOT NULL,
        PRIMARY KEY(id)
    )";

    
    $head_coach_wrestlers = "CREATE TABLE head_coach_wrestlers (
        id INT(11) NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(20) NOT NULL,
        last_name VARCHAR(20) NOT NULL,
        wrestler_weight DECIMAL(4,1) NOT NULL,
        school_name VARCHAR(50) NOT NULL,
        school_abbrv VARCHAR(6) NOT NULL,
        wins INT(2)  DEFAULT 0 NOT NULL,
        losses INT(2) DEFAULT 0 NOT NULL,
        PRIMARY KEY(id)
        )";
    
    try {
        $pdo->exec($all_wrestlers);
        $pdo->exec($head_coach_wrestlers);
    
        // Update the name and abbrv of the HC School.
        $stmt = $pdo->prepare("SELECT id FROM schools ORDER BY id ASC LIMIT 1");
        $stmt->execute();
        $firstRow = $stmt->fetch(PDO::FETCH_ASSOC);

        $query = "UPDATE schools 
                SET school_name = ?, school_abbrv = ? 
                WHERE id = ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $schoolName,           // New school name
            $schoolAbbrv,          // New school abbreviation
            $firstRow['id'],       // ID of the first row
        ]);
        
        $stmt = null;
    } catch (PDOException $e) {
        echo "Error creating table: " . $e->getMessage();
    }

    header("Location: ../../my_wrestlers/my_wrestlers.php");

    exit();
}