<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
        die();
    }
    
    if($_SESSION["changePassword"])
    {
        header("Location: changePassword.php");
        die();
    }

    if($_SESSION["author"] == "0")
    {
        header("Location: login.php");
    }

    require_once '../../httpd.private/config.php';

    $DBServer = DB_SERVER;
    $DBUsername = DB_USERNAME;
    $DBPassword = DB_PASSWORD;
    $DBName = DB_NAME;

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

    include 'process/process-orderCategories.php';
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
        <nav>
            <ul>
                <li>
                    <a href="index.php">
                        <div class="material-symbols-outlined menuIcon">
                            home
                        </div>
                        <div>Hem</div>
                    </a>
                </li>
                <li>
                    <a href="articles.php">
                        <div class="material-symbols-outlined menuIcon">
                            article
                        </div>
                        <div>Artiklar</div>
                    </a>
                </li>
                <?php
                    if($_SESSION["author"] == "1")
                    {
                        echo("<li><a href='createArticle.php'><div class='material-symbols-filled menuIcon'>add</div><div>Skapa artikel</div></a></li>");
                        echo("<li><a href='myArticles.php'><div class='material-symbols-outlined menuIcon'>edit_note</div><div>Mina artiklar</div></a></li>");
                    }
                    if($_SESSION["moderator"] == "1")
                    {
                        echo("<li><a href='assess.php'><div class='material-symbols-outlined menuIcon'>shield</div><div>Väntande artiklar</div></a></li>");
                    }
                    if($_SESSION["admin"] == "1")
                    {
                        echo("<li><a href='associations.php'><div class='material-symbols-outlined menuIcon'>group</div><div>Föreningar</div></a></li>");
                        echo("<li><a href='users.php'><div class='material-symbols-outlined menuIcon'>person_edit</div><div>Användare</div></a></li>");
                        echo("<li><a href='createUser.php'><div class='material-symbols-outlined menuIcon'>person_add</div><div>Skapa användare</div></a></li>");
                        echo("<li><a href=''><div class='material-symbols-outlined menuIcon'>history</div><div>Historik</div></a></li>");
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
                        <label for="categoryID>">Kategori: <span class="reqStar">*</span></label>
                        <select name="categoryID" id="categoryID" required>
                            <option value="">Välj kategori</option>
                            <?php
                                echoCategories($categories);
                            ?>
                        </select>
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
                        <label for="firmWebsite">Hemsida:</label>
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

                    <label for="risk-assessment">Riskbedömning:</label>
                    <div>Tillåtna format: PDF, DOCX, DOC och ODT</div>
                    <input type="file" name="risk-assessment" id="risk-assessment">

                    <label for="image">Bilder/dokument:</label>
                    <div>Tillåtna format: PDF, DOCX, DOC, ODT, JPG, JPEG, PNG och GIF</div>
                    <input type="file" name="files[]" id="image" multiple>

                    <div>Fält markerade med "<span class="reqStar">*</span>" måste vara ifyllda!</div>
                    <div>Artiklar skickas inledningsvis in för godkännande.</div>
                    <button class="primaryContainer" type="submit">Skicka in artikel</button>
                </form>
            </div>
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