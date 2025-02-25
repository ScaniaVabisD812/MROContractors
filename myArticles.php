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

    require_once '../../httpd.private/config.php';

    $DBServer = DB_SERVER;
    $DBUsername = DB_USERNAME;
    $DBPassword = DB_PASSWORD;
    $DBName = DB_NAME;

    include 'process/process-orderCategories.php';

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $querystring = "INSERT INTO Log(Page, Interaction, UserID) VALUES ('4', '1', :userID);";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":userID", $_SESSION["userID"]);
    try{
        $stmt->execute();
    }
    catch(PDOException $e)
    {
        
    }
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
                        echo("<li><a href='createArticle.php'><div class='material-symbols-outlined menuIcon'>add</div><div>Skapa artikel</div></a></li>");
                        echo("<li><a href='myArticles.php'><div class='material-symbols-filled menuIcon'>edit_note</div><div>Mina artiklar</div></a></li>");
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
                <h2 class="filterTitle" id="filterTitle">Filter</h2>
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
                            if(isset($_GET["category"]) && $_GET["category"] != "" && $_GET["category"] != "none")
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
                        <a href="articles.php" class="button primaryContainer">Rensa filter</a>
                        <button type="submit" class="primaryContainer">Filtrera</button>
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

                        $getString = "";
                        if(isset($_GET["title"]))
                        {
                            $getString .= "title=" . $_GET["title"] . "&";
                        }
                        if(isset($_GET["category"]))
                        {
                            $getString .= "category=" . $_GET["category"] . "&";
                        }
                        if(isset($_GET["firmName"]))
                        {
                            $getString .= "firmName=" . $_GET["firmName"] . "&";
                        }
                        if(isset($_GET["vehicle"]))
                        {
                            $getString .= "vehicle=" . $_GET["vehicle"] . "&";
                        }
                        if(isset($_GET["formUsed"]))
                        {
                            $getString .= "formUsed=articles.php&";
                        }
                        echo("<a href='article.php?articleID=" . $article["ArticleID"] . "&from=myArticles.php&" . $getString ."' class='articleContainer " . $color . "'>");
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