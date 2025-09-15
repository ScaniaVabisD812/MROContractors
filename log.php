<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
        die();
    }

    if($_SESSION["admin"] == "0")
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

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $querystring = "INSERT INTO Log(Page, Interaction, UserID) VALUES ('8', '1', :userID);";
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
    <title>Skapa användare</title>
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
                        echo("<li><a href='assess.php'><div class='material-symbols-outlined menuIcon'>shield</div><div>Väntande artiklar</div></a></li>");
                    }
                    if($_SESSION["admin"] == "1")
                    {
                        echo("<li><a href='associations.php'><div class='material-symbols-outlined menuIcon'>group</div><div>Föreningar</div></a></li>");
                        echo("<li><a href='users.php'><div class='material-symbols-outlined menuIcon'>person_edit</div><div>Användare</div></a></li>");
                        echo("<li><a href='createUser.php'><div class='material-symbols-outlined menuIcon'>person_add</div><div>Skapa användare</div></a></li>");
                        echo("<li><a href='log.php'><div class='material-symbols-filled menuIcon'>history</div><div>Historik</div></a></li>");
                    }
                ?>
            </ul>
        </nav>
        <main>
            <div class="container">
                <h2>Historik</h2>
                <table>
                    <?php
                    $querystring = "SELECT LogID, Time, Page, Interaction, ObjVal, ObjType, Log.UserID, Users.Name AS UserName FROM Log LEFT JOIN Users ON Log.userID = Users.userID ORDER BY Log.Time DESC";

                    $stmt = $pdo->prepare($querystring);
                    $stmt->execute();
                    $queryResult = $stmt->fetchAll();
                ?>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tidsstämpel</th>
                        <th>Sida</th>
                        <th>Interaktion</th>
                        <th>Objekt</th>
                        <th>AnvändarID</th>
                        <th>Namn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach($queryResult as $row)
                        {
                            $pageID = $row["Page"];
                            $pageName = "";
                            switch ($pageID)
                            {
                                case 0:
                                    $pageName = "Login";
                                    break;
                                case 1:
                                    $pageName = "Index";
                                    break;
                                case 2:
                                    $pageName = "Artiklar";
                                    break;
                                case 3:
                                    $pageName = "Artikel";
                                    break;
                                case 4:
                                    $pageName = "Mina artiklar";
                                    break;
                                case 5:
                                    $pageName = "Väntande artiklar";
                                    break;
                                case 6:
                                    $pageName = "Föreningar";
                                    break;
                                case 7:
                                    $pageName = "Användare";
                                    break;
                                case 8:
                                    $pageName = "Historik";
                                    break;
                                default:
                                    $pageName = "Okänd sida";
                            }
                            $interactionID = $row["Interaction"];
                            $interactionName = "";
                            switch ($interactionID)
                            {
                                case 0:
                                    $interactionName = "Loggade in";
                                    break;
                                case 1:
                                    $interactionName = "Öppnade sida";
                                    break;
                                case 2:
                                    $interactionName = "Godkände";
                                    break;
                                case 3:
                                    $interactionName = "Nekade";
                                    break;
                                default:
                                    $interactionName = "Okänd interaktion";
                            }

                            echo("<tr>");
                            echo("<td>" . $row["LogID"] . "</td>");
                            echo("<td>" . $row["Time"] . "</td>");
                            echo("<td>" . $pageName . "</td>");
                            echo("<td>" . $interactionName . "</td>");
                            echo("<td>" . $row["ObjType"] . " #" . $row["ObjVal"] . "</td>");
                            echo("<td>" . $row["UserID"] . "</td>");
                            if(isset($row["UserName"]))
                                echo("<td>" . $row["UserName"] . "</td>");
                            else
                                echo("<td>Troligen borttagen...</td>");
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