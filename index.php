<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leverantördatabas</title>
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
                        <div class="material-symbols-filled menuIcon">
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
                        echo("<li><a href='createArticle.php'><div class='material-symbols-outlined menuIcon'>add</div><div>Skapa artikel</div></a></li>");
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
            <div class="container primaryContainer">
                <h2>Välkommen!</h2>
                <div>Detta är ett försök att underlätta för varandra att dela med sig av erfarenheter av externa firmor.</div>
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