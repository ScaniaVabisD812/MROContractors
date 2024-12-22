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

    include "process/process-orderCategories.php";

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $querystring = "SELECT Articles.CategoryID AS CategoryID, Articles.Content AS Content, Articles.Background AS Background, Articles.Vehicle AS Vehicle, Articles.Cost AS Cost, Articles.FirmName AS FirmName, Articles.FirmWebsite AS FirmWebsite, Articles.Title AS Title, Articles.FirmName AS FirmName, Users.Name AS FullName, Users.AssociationRole AS AssociationRole, Associations.Name AS AssociationName, Articles.WrittenDate AS WrittenDate, Articles.ArticleID AS ArticleID, Articles.AuthorID AS AuthorID, Articles.Status AS Status FROM Articles INNER JOIN Users ON Articles.AuthorID = Users.UserID INNER JOIN Associations ON Users.AssociationID = Associations.AssociationID WHERE ArticleID = :articleID;";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":articleID", $_GET["articleID"]);
    $stmt->execute();
    $articles = $stmt->fetchAll();
    if(count($articles) == 0)
    {
        Header("Location: articles.php");
    }

    $qualify = false;
    if($articles[0]["AuthorID"] == $_SESSION["userID"])
    {
        $qualify = true;
    }
    if($_SESSION["moderator"] == 1)
    {
        $qualify = true;
    }
    if($_SESSION["admin"] == 1)
    {
        $qualify = true;
    }

    if($articles[0]["Status"] != 2 && !$qualify)
    {
        Header("Location: articles.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
            echo($articles[0]["Title"]);
        ?>
    </title>
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
                        echo('<li><a class="" href="myArticles.php"><span class="material-symbols-filled">edit_note</span>Mina artiklar</a></li>');
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
            <?php
                if($qualify)
                {
                    $querystring = "SELECT * FROM ArticleStatusChanges WHERE ArticleID = :articleID ORDER BY POT DESC LIMIT 1;";
                    $stmt = $pdo->prepare($querystring);
                    $stmt->bindParam(":articleID", $_GET["articleID"]);
                    $stmt->execute();
                    $statusChanges = $stmt->fetchAll();

                    if($articles[0]["Status"] == 0)
                    {
                        echo("<div class='statusContainer waiting'>Artikeln är ännu ej granskad av moderator.</div>");
                    }
                    else if($articles[0]["Status"] == 1)
                    {
                        echo("<div class='statusContainer denied'>Artikeln nekad av moderator " . $statusChanges[0]["POT"] . ".");
                        echo("<div>Kommentar: " . $statusChanges[0]["Message"] . ".</div>");
                        echo("<div><a href=editArticle.php?articleID=" . $_GET["articleID"] . ">Tryck här för att redigera och skicka in på nytt!</a></div>");
                        echo("</div>");
                    }
                    else if($articles[0]["Status"] == 2)
                    {
                        echo("<div class='statusContainer approved'>Artikeln godkänd av moderator " . $statusChanges[0]["POT"] . ".</div>");
                    }
                    else if($articles[0]["Status"] == 3)
                    {
                        echo("<div class='statusContainer deleted'>Artikeln borttagen av författare " . $statusChanges[0]["POT"] . "</div>");
                    }
                    else if($articles[0]["Status"] == 4)
                    {
                        echo("<div class='statusContainer deleted'>Artikeln borttagen av moderator " . $statusChanges[0]["POT"] . "</div>");
                    }

                    echo("<table class='optiontable'>");
                    echo("<thead>");
                    echo("<tr>");
                    echo("<th>Redigera</th>");
                    if($articles[0]["Status"] != 3 && $articles[0]["Status"] != 4)
                    {
                        echo("<th>Radera</th>");
                    }
                    else
                    {
                        echo("<th>Återför</th>");
                    }
                    if($_SESSION["moderator"] == 1 && $articles[0]["Status"] == 0)
                    {
                        echo("<th>Neka</th>");
                        echo("<th>Godkänn</th>");
                    }
                    echo("</tr>");
                    echo("</thead>");
                    echo("<tbody>");
                    echo("<tr>");
                    echo('<td class="blackButton"><a class="blackButton" href="editArticle.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">edit</span></a></td>');

                    if($articles[0]["Status"] != 3 && $articles[0]["Status"] != 4)
                    {
                        echo('<td class="blackButton"><a class="blackButton" href="process/process-deleteArticle.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">delete</span></a></td>');
                    }
                    else
                    {
                        echo('<td class="blackButton"><a class="blackButton" href="process/process-bringBackArticle.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">restore_from_trash</span></a></td>');
                    }
                    
                    if($_SESSION["moderator"] == 1 && $articles[0]["Status"] == 0)
                    {
                        echo('<td class="denyButton"><a class="denyButton" href="assessDenyMessage.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">cancel</span></a></td>');
                        echo('<td class="approveButton"><a class="approveButton" href="process/process-acceptArticle.php?articleID=' . $articles[0]["ArticleID"] . '"><span class="material-symbols-filled">arrow_forward</span></a></td>');
                    }
                    echo("</tr>");
                    echo("</tbody>");
                    echo("</table>");
                }
                echo("<h2>" . $articles[0]["Title"] . "</h2>");

                $parentCategories = (getParentCategories($categories, $articles[0]["CategoryID"], array(), $pdo));
                echo("<h3 class='categoryHeader'>");
                for($i = count($parentCategories) - 1; $i >= 0; $i--)
                {
                    echo(getCategoryName($categories, $parentCategories[$i]));
                    echo(" <span class='material-symbols-filled'>arrow_forward</span> ");
                }
                echo(getCategoryName($categories, $articles[0]["CategoryID"]));
                echo("</h3>");

                echo("<p>Skapad av " . $articles[0]["FullName"] . ", " . strtolower($articles[0]["AssociationRole"]) . " i/för " . $articles[0]["AssociationName"] . " den " . $articles[0]["WrittenDate"] . ".</p>");
                if(isset($statusChanges[0]["POT"]))
                {
                    echo("<p>Senast uppdaterad " . $statusChanges[0]["POT"] . ".</p>");
                }

                echo("<h3>Leverantör</h3>");
                echo("<p>" . $articles[0]["FirmName"]);
                if(isset($articles[0]["FirmWebsite"]))
                {
                    echo(" - <a href='" . $articles[0]["FirmWebsite"] . "'>" . $articles[0]["FirmWebsite"] . "</a>");
                }
                echo("</p>");
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
                    echo("<a target='_blank' href='fullPic.php?image=" . $image["Filenamez"] ."'><img src='process/process-fetchImage.php?image=" . $image["Filenamez"] . "' alt='Bild " . $num . "'></a>");
                }
                echo("</div>");
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