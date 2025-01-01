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
                <h2>Väntande artiklar</h2>
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
                <table>
                    <thead>
                        <tr>
                            <th>Rubrik</th>
                            <th>Firma</th>
                            <th>Skriven av</th>
                            <th>Datum</th>
                            <th>Neka</th>
                            <th>Gå vidare</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
                            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                            $querystring = "SELECT Articles.Title AS Title, Articles.FirmName AS FirmName, Users.Name AS FullName, Users.AssociationRole AS AssociationRole, Associations.Name AS AssociationName, Articles.WrittenDate AS WrittenDate, Articles.ArticleID AS ArticleID FROM Articles INNER JOIN Users ON Articles.AuthorID = Users.UserID INNER JOIN Associations ON Users.AssociationID = Associations.AssociationID WHERE Articles.Status = 0;";
                            $stmt = $pdo->prepare($querystring);
                            $stmt->execute();
                            $articles = $stmt->fetchAll();

                            $printThis = "";

                            if(count($articles) == 0)
                            {
                                echo("<tr>");
                                echo("<td colspan='6'>Inga artiklar att bedöma!</td>");
                                echo("</tr>");
                            }

                            foreach($articles as $article)
                            {
                                $querystring = "SELECT POT FROM ArticleStatusChanges WHERE ArticleID = :articleID ORDER BY POT DESC LIMIT 1;";
                                $stmt = $pdo->prepare($querystring);
                                $stmt->bindParam(":articleID", $article["ArticleID"]);
                                $stmt->execute();
                                $pot = $stmt->fetch();

                                echo("<tr>");
                                echo("<td>" . $article["Title"] . "</td>");
                                echo("<td>" . $article["FirmName"] . "</td>");
                                echo("<td>" . $article["FullName"] . "<br/>" . $article["AssociationRole"] . " - " . $article["AssociationName"] . "</td>");
                                if(isset($pot[0]["POT"]))
                                {
                                    echo("<td>" . $pot[0]["POT"] . "</td>");
                                }
                                else
                                {
                                    echo("<td>" . $article["WrittenDate"] . "</td>");
                                }
                                
                                echo('<td class="denyButton"><a class="denyButton" href="assessDenyMessage.php?articleID=' . $article["ArticleID"] . '"><span class="material-symbols-outlined">cancel</span></a></td>');
                                echo('<td class="approveButton"><a class="approveButton" href="article.php?articleID=' . $article["ArticleID"] . '&from=assess.php"><span class="material-symbols-outlined">arrow_forward</span></a></td>');
                                echo("</tr>");
                            }
                        ?>
                    </tbody>
                </table>
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