<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logga in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="statisk/styling.css">
</head>
<body>
    <div class="GridContainer">
        <header>
            <img src="statisk/logotyp.png" alt="logotyp">
            <h1>Leverantördatabas</h1>
            <div class="sessionInfo">
            </div>
        </header>
        <main>
            <div class="loginContainer">
                <h2>Logga in</h2>
                <div>
                    <?php
                        if(isset($_GET["error"])) {
                            echo "<div class='errorContainer'>" . $_GET["error"] . "</div>";
                        }
                    ?>
                <div>Inloggningar sköts av sidans administratörer, kontakta dessa vid problem.</div>
                <form action="process/processLogins.php" method="post">
                    <div>
                        <label for="username">Användarnamn:</label>
                        <input type="text" name="username" id="username" placeholder="Användarnamn" required>
                    </div>
                    <div>
                        <label for="password">Lösenord:</label>
                        <input type="password" name="password" id="password" placeholder="Lösenord" required>
                    </div>
                    <div>
                        <button type="submit">Logga in</button>
                    </div>
                </form>
            </div>
        </main>
        <footer>
            <div>Prototyp 1</div>
            <div>...</div>
            <div>Uppdaterad: 2024-10-23</div>
        </footer>
    </div>
</body>
</html>