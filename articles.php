<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }

    require_once '../../httpd.private/config.php';

    $DBServer = DB_SERVER;
    $DBUsername = DB_USERNAME;
    $DBPassword = DB_PASSWORD;
    $DBName = DB_NAME;

    include 'process/process-orderCategories.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Väntande artiklar</title>
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
                <!-- <div>Du är <span style="color: green; font-weight: 900;">inloggad</span>!</div>
                <div>Du är <span style="color: red; font-weight: 900;">inte inloggad</span>!</div> -->
                <div>Användare: Arvid</div>
                <div>Förening: SkLJ</div>
                <a href="">Logga ut</a>
            </div>
        </header>
        <nav>
        <ul>
                <li><a class="" href="index.php"><span class="material-symbols-filled">home</span>Hem</a></li>
                <li><a class="active" href="articles.php"><span class="class material-symbols-filled">article</span>Artiklar</a></li>
                <?php
                    if($_SESSION["author"] == "1")
                    {
                        echo("<li><a class='' href='createArticle.php'><span class='material-symbols-filled'>add</span>Skapa artikel</a></li>");
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
                <h2>Filter</h2>
                <span id="toggle" class="filterButton material-symbols-filled">arrow_downward</span>
                <section id="section">
                    <form action="" method="GET">
                        <label for="title">Titel</label>
                        <?php

                        ?>
                        <input type="text" name="title" id="title" placeholder="...">
    
                        <label for="category">Kategori</label>

                        <?php
                            echo '<select>';
                            echo('<option value="">Välj kategori</option>');
                            echoCategories($categories);
                            echo '</select>';
                        ?>
    
                        <label for="firmName">Företagsnamn</label>
                        <input type="text" name="firmName" id="firmName" placeholder="...">
    
                        <label for="vehicle" id="vehicle">Fordon</label>
                        <input type="text" name="vehicle" id="vehicle" placeholder="...">
    
                        <label for="author">Författare</label>
                        <input type="text" name="author" id="author" placeholder="...">

                        <button type="submit">Filtrera</button>
                    </form>
                </section>
            </div>
            <script src="javascript/dropdown.js"></script>
            <div class="container">
                <h2>Artiklar</h2>
                
                <a href="" class="articleContainer tertiaryContainer">
                    <h3>Ägrids eget tåg</h3>
                    <div class="articleDetailsContainer">
                        <div>Ägrids verkstad</div>
                        <div>2024-10-08</div>
                        <div>Skara-Lundsbrunns Järnvägar</div>
                        <div>Arvid Nordström</div>
                    </div>
                    <span class="openArticle material-symbols-filled">arrow_forward</span>                
                </a>
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