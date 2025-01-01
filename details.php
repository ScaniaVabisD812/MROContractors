<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
        die();
    }

    if($_SESSION["moderator"] == "0" AND $_SESSION["admin"] == "0")
    {
        header("Location: index.php");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $querystring = "SELECT Articles.Content AS Content, Articles.Background AS Background, Articles.Vehicle AS Vehicle, Articles.Cost AS Cost, Articles.FirmName AS FirmName, Articles.FirmWebsite AS FirmWebsite, Articles.Title AS Title, Articles.FirmName AS FirmName, Users.Name AS FullName, Users.AssociationRole AS AssociationRole, Associations.Name AS AssociationName, Articles.WrittenDate AS WrittenDate, Articles.ArticleID AS ArticleID FROM Articles INNER JOIN Users ON Articles.AuthorID = Users.UserID INNER JOIN Associations ON Users.AssociationID = Associations.AssociationID WHERE Articles.Status = 0 AND ArticleID = :articleID;";
        $stmt = $pdo->prepare($querystring);
        $stmt->bindParam(":articleID", $_GET["articleID"]);
        $stmt->execute();
        $articles = $stmt->fetchAll();
        
        if(count($articles) == 0)
        {
            echo("<title>Välj artikel först!</title>");
        }
        else
        {
            echo("<title>" . $articles[0]["Title"] . "</title>");
        }
    ?>
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
                        echo("<li><a href='myArticles.php'><div class='material-symbols-outlined menuIcon'>edit_note</div><div>Mina artiklar</div></a></li>");
                    }
                    if($_SESSION["moderator"] == "1")
                    {
                        echo("<li><a href='assess.php'><div class='material-symbols-filled menuIcon'>shield</div><div>Väntande artiklar</div></a></li>");
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


                if(count($articles) == 0)
                {
                    echo("<div class='errorContainer'>Välj artikel först!</div>");
                }
                else
                {
                    echo("<h2>" . $articles[0]["Title"] . "</h2>");

                    echo("<h3>" . $articles[0]["FirmName"] . "</h3>");
                    echo("<p>" . $articles[0]["FirmName"] . " - " . $articles[0]["FirmWebsite"] . "</p>");
                    if(isset($articles[0]["Background"]))
                    {
                        echo("<h3>Bakgrund</h3>");
                        echo("<p>" . $articles[0]["Background"] . "</p>");
                    }

                    echo("<h3>Innehåll</h3>");
                    echo("<p>" . $articles[0]["Content"] . "</p>");
                    if(isset($articles[0]["Vehicle"]))
                    {
                        echo("<h3>Fordon</h3>");
                        echo("<p>" . $articles[0]["Vehicle"] . "</p>");
                    }

                    if(isset($articles[0]["Cost"]))
                    {
                        echo("<h3>Kostnad</h3>");
                        echo("<p>" . $articles[0]["Cost"] . "</p>");
                    }

                    $querystring = "SELECT FileID, Filenamez FROM Images WHERE ArticleID = :articleID;";
                    $stmt = $pdo->prepare($querystring);
                    $stmt->bindParam(":articleID", $_GET["articleID"]);
                    $stmt->execute();
                    $images = $stmt->fetchAll();

                    echo("<div class='imageContainer'>");
                    $num = 0;
                    foreach($images as $image)
                    {
                        echo("<a href='fullPic.php?image=" . $image["Filenamez"] ."'><img src='process/process-fetchImage.php?image=" . $image["Filenamez"] . "' alt='Bild " . $num . "'></a>");
                    }
                    echo("</div>");

                    echo("<table>");
                    echo("<thead>");
                    echo("<tr>");
                    echo("<th>Neka med kommentar</th>");
                    echo("<th>Godkänn</th>");
                    echo("</tr>");
                    echo("</thead>");
                    echo("<tbody>");
                    echo("<tr>");
                    echo('<td class="equalWidthCell denyButton"><a class="denyButton" href="assessDenyMessage.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">cancel</span></a></td>');
                    echo('<td class="equalWidthCell approveButton"><a class="approveButton" href="process/process-acceptArticle.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">arrow_forward</span></a></td>');
                    echo("</tr>");
                    echo("</tbody>");
                    echo("</table>");
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