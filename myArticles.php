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

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
                        echo("<li><a class='' href='createArticle.php'><span class='material-symbols-filled'>add</span>Skapa artikel</a></li>");
                        echo('<li><a class="active" href=""><span class="material-symbols-filled">edit_note</span>Mina artiklar</a></li>');
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
                <?php
                /*
                if(isset($_GET["category"]) && $_GET["category"] != "" && $_GET["category"] != "none")
                {
                    $parentCategories = (getParentCategories($categories, $_GET["category"], array(), $pdo));
                    echo("<h3 class='categoryHeader'>");
                    for($i = count($parentCategories) - 1; $i >= 0; $i--)
                    {
                        echo(getCategoryName($categories, $parentCategories[$i]));
                        echo(" <span class='material-symbols-filled'>arrow_forward</span> ");
                    }
                    echo(getCategoryName($categories, $_GET["category"]));
                    echo("</h3>");
                }

                if(isset($_GET["category"]) && $_GET["category"] != "" && $_GET["category"] != "none")
                {
                    $childCategories = (getChildCategories($categories, $_GET["category"], array(), $pdo));
                    echo("<ul>");
                    foreach($childCategories as $childCategory)
                    {
                        echo("<li>");
                        echo(getCategoryName($categories, $childCategory));
                        echo("</li>");
                    }
                    echo("</ul>");
                }
                */
                ?>

                <span id="toggle" class="filterButton material-symbols-filled">arrow_downward</span>
                <section id="section" <?php
                   if(isset($_GET["formUsed"]))
                    {
                        echo "data-state=0";
                    }
                    else
                    {
                        echo "data-state=1";
                    }
                ?>>
                    <a href="articles.php">Rensa filter</a>
                    <form action="" method="GET">
                        <label for="title">Titel</label>
                        <?php
                            if(isset($_GET["title"]))
                            {
                                echo '<input type="text" name="title" id="title" placeholder="..." value="' . $_GET["title"] . '">';
                            }
                            else
                            {
                                echo '<input type="text" name="title" id="title" placeholder="...">';
                            }
                        ?>
    
                        <label for="category">Kategori</label>
                        <?php
                            echo '<select name="category" id="category">';
                            if(isset($_GET["category"]) && $_GET["category"] != "")
                            {
                                echo('<option value="' . $_GET["category"] . '">' . getCategoryName($categories, $_GET["category"]) . '</option>');
                                echo('<option value="">Alla kategorier</option>');
                            }
                            else
                            {
                                echo('<option value="none">Alla kategorier</option>');
                            }
                            echoCategories($categories);
                            echo '</select>';
                        ?>
    
                        <label for="firmName">Företagsnamn</label>
                        <?php 
                            if(isset($_GET["firmName"]))
                            {
                                echo '<input type="text" name="firmName" id="firmName" placeholder="..." value="' . $_GET["firmName"] . '">';
                            }
                            else
                            {
                                echo '<input type="text" name="firmName" id="firmName" placeholder="...">';
                            }
                        ?>

                        <label for="vehicle" id="vehicle">Fordon</label>
                        <?php 
                            if(isset($_GET["vehicle"]))
                            {
                                echo '<input type="text" name="vehicle" id="vehicle" placeholder="..." value="' . $_GET["vehicle"] . '">';
                            }
                            else
                            {
                                echo '<input type="text" name="vehicle" id="vehicle" placeholder="...">';
                            }
                        ?>
                        <input type="hidden" name="formUsed" value="articles.php">

                        <button type="submit">Filtrera</button>
                    </form>
                </section>
            </div>
            <script src="javascript/dropdown.js"></script>
            <div class="container">
                <h2>Artiklar</h2>
                <?php 
                    if(isset($_GET["error"])) 
                    {
                        if($_GET["error"] != "") 
                        {
                            echo "<div class='errorContainer'>" . $_GET["error"] . "</div>";
                        }
                    }
                    if(isset($_GET["success"])) 
                    {
                        if($_GET["success"] != "") 
                        {
                            echo "<div class='successContainer'>" . $_GET["success"] . "</div>";
                        }
                    }
                    $querystringStart = "SELECT A.ArticleID AS ArticleID, A.Title AS Title, A.FirmName AS FirmName, A.WrittenDate AS WrittenDate, U.Name AS UserName, S.Name AS AssociationName FROM Articles A INNER JOIN Users U ON A.AuthorID = U.UserID INNER JOIN Associations S ON U.AssociationID = S.AssociationID WHERE A.AuthorID = :authorID";
                    $querystringEnd = ";";
                    $params = array();

                    if(isset($_GET["title"]) && $_GET["title"] != "") 
                    {
                        $querystringStart .= " AND A.Title LIKE :title";
                        $params[':title'] = '%' . $_GET["title"] . '%';
                    }
                    if(isset($_GET["category"]) && $_GET["category"] != "" && $_GET["category"] != "none") 
                    {
                        $childCategories = (getChildCategories($categories, $_GET["category"], array(), $pdo));

                        $querystringStart .= " AND A.CategoryID = :category";
                        $params[':category'] = $_GET["category"];

                        if(count($childCategories) > 0)
                        {
                            for($i = 0; $i < count($childCategories); $i++)
                            {
                                $querystringStart .= " OR A.CategoryID = :category" . $i;
                                $params[':category' . $i] = $childCategories[$i];
                            }
                        }
                    }
                    if(isset($_GET["firmName"]) && $_GET["firmName"] != "") 
                    {
                        $querystringStart .= " AND A.FirmName LIKE :firmName";
                        $params[':firmName'] = '%' . $_GET["firmName"] . '%';
                    }
                    if(isset($_GET["vehicle"]) && $_GET["vehicle"] != "") 
                    {
                        $querystringStart .= " AND A.Vehicle LIKE :vehicle";
                        $params[':vehicle'] = '%' . $_GET["vehicle"] . '%';
                    }

                    $querystring = $querystringStart . $querystringEnd;

                    $stmt = $pdo->prepare($querystring);
                    $params[':authorID'] = $_SESSION["userID"];
                    foreach ($params as $key => &$val) {
                        $stmt->bindParam($key, $val);
                    }

                    $stmt->execute();
                    $articles = $stmt->fetchAll();

                    if(count($articles) == 0)
                    {
                        echo("<div>Inga artiklar att visa!</div>");
                    }

                    foreach($articles as $article)
                    {
                        $querystring = "SELECT * FROM ArticleStatusChanges WHERE ArticleID = :articleID ORDER BY POT DESC LIMIT 1;";
                        $stmt = $pdo->prepare($querystring);
                        $stmt->bindParam(":articleID", $article["ArticleID"]);
                        $stmt->execute();
                        $statusChanges = $stmt->fetchAll();

                        /* 
                        Status:
                        0 - Inskickad, inte än behandlad
                        1 - Behandlad, nekad
                        2 - Behandlad, godkänd
                        3 - Borttagen av användare
                        4 - Borttagen av moderator
                        */
                        $color = "";
                        $status = "";
                        if($statusChanges[0]["NewStatus"] == 0)
                        {
                            $color = "waiting";
                            $status = "Väntar på bedömning";
                        }
                        else if($statusChanges[0]["NewStatus"] == 1)
                        {
                            $color = "denied";
                            $status = "Nekad";
                        }
                        else if($statusChanges[0]["NewStatus"] == 2)
                        {
                            $color = "approved";
                            $status = "Godkänd";
                        }
                        else if($statusChanges[0]["NewStatus"] == 3 || $statusChanges[0]["NewStatus"] == 4)
                        {
                            $color = "deleted";
                            $status = "Borttagen";
                        }

                        echo("<a href='article.php?articleID=" . $article["ArticleID"] . "' class='articleContainer " . $color . "'>");
                        echo("<h3>" . $article["Title"] . " - " . $status . "</h3>");
                        echo("<div class='articleDetailsContainer'>");
                        echo("<div>" . $article["FirmName"] . "</div>");
                        echo("<div>" . $article["WrittenDate"] . "</div>");
                        echo("<div>Senast ändrad: " . $statusChanges[0]["POT"] . "</div>");
                        echo("</div>");
                        echo("<span class='openArticle material-symbols-filled'>arrow_forward</span>");
                        echo("</a>");
                    }
                ?>
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