<?php 
// Resets session when entering login page
session_unset();
session_destroy();
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arroyo Dons Wrestling</title>
    <link rel="shortcut icon" type="x-icon" href="../imgs/ArroyoDonsLogo.png"> </link>

    <link rel="stylesheet" href="login.css">
</head>

<body>
    
    <main>
        <!-- back to home img -->
        <div>
            <a href="../home/home.html"><img src="../imgs/ArroyoDonsLogo.png" alt="arroyo logo" width="100" height="100" id="nav-logo"></a>
        </div>

        <div>
            <h3>Usage is for the tournament manager or head coach ONLY</h3>


            <form action="includes/login_handler.php" method="post">
                <div>
                    <label for="">LOGIN</label>

                    <?php
                    if (isset($_GET['error'])) {
                        echo '<p style="color:red;">Invalid password. Please try again.</p>';
                    }
                    ?>

                    <input required type="text" id="access-code" placeholder="Access Code" name="access-code" autocomplete="off">
                    <button class="submit-form-btn"> Continue </button>
                </div>
            </form>
        </div>
    </main>





</body>

</html>