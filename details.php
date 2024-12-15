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
                        echo('<li><a class="" href=""><span class="material-symbols-filled">edit_note</span>Mina artiklar</a></li>');
                    }
                    if($_SESSION["moderator"] == "1")
                    {
                        echo('<li><a class="active" href="assess.php"><span class="material-symbols-filled">shield</span>Väntande artiklar</a></li>');
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


                if(count($articles) == 0)
                {
                    echo("<div class='errorContainer'>Välj artikel först!</div>");
                }
                else
                {
                    echo("<h2>" . $articles[0]["Title"] . "</h2>");
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
                }
            ?>
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