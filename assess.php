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
                <li><a class="" href=""><span class="class material-symbols-filled">article</span>Artiklar</a></li>
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
                                
                                echo('<td class="denyButton"><a class="denyButton" href="assessDenyMessage.php?articleID=' . $article["ArticleID"] . '"><span class="material-symbols-filled">cancel</span></a></td>');
                                echo('<td class="approveButton"><a class="approveButton" href="details.php?articleID=' . $article["ArticleID"] . '"><span class="material-symbols-filled">arrow_forward</span></a></td>');
                                echo("</tr>");
                            }
                        ?>
                    </tbody>
                </table>
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