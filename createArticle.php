<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }
    if($_SESSION["author"] == "0")
    {
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skapa artikel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="statisk/layout.css">
    <link rel="stylesheet" href="statisk/styling.css">
</head>
<body>
    <div class="GridContainer">
        <header>
            <img src="statisk/logotyp.png" alt="logotyp">
            <h1>Leverantördatabas</h1>
            <div class="sessionInfo">
                <div class="container">
                    <!-- <div>Du är <span style="color: green; font-weight: 900;">inloggad</span>!</div>
                    <div>Du är <span style="color: red; font-weight: 900;">inte inloggad</span>!</div> -->
                    <?php
                        echo("<div>" . $_SESSION["name"] . "</div>");
                        echo("<div>" . $_SESSION["associationName"] . "</div>");
                    ?>
                    <p><a href="process/processLogins.php?logoutReq=true">Logga ut</a></p>
                </div>
            </div>
        </header>
        <nav>
            <ul>
                <li><a class="" href="index.php"><span class="material-symbols-filled">home</span>Hem</a></li>
                <li><a class="" href="articles.php"><span class="class material-symbols-filled">article</span>Artiklar</a></li>
                <?php
                    if($_SESSION["author"] == "1")
                    {
                        echo("<li><a class='active' href='createArticle.php'><span class='material-symbols-filled'>add</span>Skapa artikel</a></li>");
                        echo('<li><a class="" href=""><span class="material-symbols-filled">edit_note</span>Mina artiklar</a></li>');
                    }
                    if($_SESSION["moderator"] == "1")
                    {
                        echo('<li><a class="" href="assess.php"><span class="material-symbols-filled">shield</span>Väntande artiklar</a></li>');
                    }
                    if($_SESSION["admin"] == "1")
                    {
                        echo('<li><a class="" href="associations.php"><span class="material-symbols-filled">group</span>Föreningar</a></li>');
                        echo('<li><a class="" href="users.php"><span class="material-symbols-filled">person_edit</span>Användare</a></li>');
                        echo('<li><a class="" href="createUser.php"><span class="material-symbols-filled">person_add</span>Skapa användare</a></li>');
                        echo('<li><a class="" href=""><span class="material-symbols-filled">history</span>Historik</a></li>');
                    }
                ?>
            </ul>
        </nav>
        <main>
            <div class="container">
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
                <h2>Skapa artikel</h2>
                <form action="process/process-addarticle.php" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="title">Titel: <span class="reqStar">*</span></label>
                        <input type="text" name="title" id="title" placeholder="Ex. Takstagsbyte" required>
                    </div>
                    <div>
                        <label for="firmName">Företagsnamn: <span class="reqStar">*</span></label>
                        <input type="text" name="firmName" id="firmName" placeholder="Ex. Gössäter mekaniska verkstad" required>
                    </div>
                    <div>
                        <label for="firmAddress">Företagsaddress: <span class="reqStar">*</span></label>
                        <input type="text" name="firmAddress" id="firmAddress" placeholder="Ex. Gössäter Stationsvägen 4, 533 94 Hällekis" required>
                    </div>
                    <div>
                        <label for="firmWebsite">Ev. Hemsida:</label>
                        <input type="text" name="firmWebsite" id="firmWebsite" placeholder="Ex. www.gossatermekaniska.se">
                    </div>
                    <div>
                        <label for="background">Bakgrund:</label>
                        <textarea name="background" id="background" cols="30" rows="7" placeholder="Varför?"></textarea>
                    </div>
                    <div>
                        <label for="content">Innehåll: <span class="reqStar">*</span></label>
                        <textarea name="content" id="content" cols="30" rows="7" placeholder="Hur, när, resultat?" required></textarea>
                    </div>
                    <div>
                        <label for="Vehicle">Fordon: </label>
                        <input type="text" name="vehicle" id="Vehicle" placeholder="Ex. VGJ 4">
                    </div>
                    <div>
                        <label for="cost">Kostnad: </label>
                        <input style="width: auto;" type="number" name="cost" id="cost" placeholder="">
                        <span style="font-weight: 900;">kr</span>
                    </div>

                    <label for="image">Bilder:</label>
                    <div>Tillåtna format: JPG, JPEG, PNG och GIF!</div>
                    <input type="file" name="files[]" id="image" multiple>

                    <div>Fält markerade med "<span class="reqStar">*</span>" måste vara ifyllda!</div>
                    <div>Artiklar skickas inledningsvis in för godkännande.</div>
                    <button type="submit">Skicka in artikel</button>
                </form>
            </div>
        </main>
        <footer>
            <div>Prototyp 1</div>
            <div>Det ser bättre ut med tre texter</div>
            <div>Uppdaterad: 2024-10-23</div>
        </footer>
    </div>
</body>
</html>