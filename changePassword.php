<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
        die();
    }

    if($_SESSION["changePassword"] == false)
    {
        header("Location: index.php");
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ändra lösenord</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="statisk/styling.css">
</head>
<body>
    <div class="GridContainer">
        <header>
            <h1>Leverantördatabas</h1>
            <div class="sessionInfo">
                <div>
                    <?php
                        echo("<div>Användare: " . $_SESSION["username"] . "</div>");
                        echo("<div>Förening: " . $_SESSION["associationName"] . "</div>");
                    ?>
                    <a href="process/processLogins.php?logoutReq=true">Logga ut</a>
                </div>
            </div>
        </header>
        <main>
            <h2>Byt lösenord!</h2>
            <?php
                if(isset($_GET["error"])) {
                    if($_GET["error"] != "") {
                        echo "<div class='errorContainer'>" . $_GET["error"] . "</div>";
                    }
                }
                if(isset($_GET["success"])) {
                    if($_GET["success"] != "") {
                        echo "<div class='successContainer'>" . $_GET["success"] . "</div>";
                    }
                }
            ?>
            <p>Antingen är ditt konto helt nytt och du behöver ange lösenord, eller så har en administratör tvingat ett byte.</p>
            <p>Det är viktigt att du väljer ett säkert lösenord som du inte använder någon annanstans.</p>
            <form action="process/process-newPassword.php" method="post">
                <label for="password">Nytt lösenord:</label>
                <input type="password" name="password" id="password" required>
                <label for="password2">Upprepa lösenord:</label>
                <input type="password" name="password2" id="password2" required>
                <button class="primaryContainer" type="submit">Byt lösenord</button>
            </form>
        </main>
        <footer>
            <?php
                $json = file_get_contents("json/footer.json");
                $footer = json_decode($json, true);

                foreach($footer["footer"] as $item)
                {
                    echo("<div>" . $item["text"] . "</div>");
                }
            ?>
        </footer>
    </div>
</body>
</html>