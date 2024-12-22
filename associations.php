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
    <title>Föreningar</title>
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
                        echo('<li><a class="active" href="associations.php"><span class="material-symbols-filled">group</span>Föreningar</a></li>');
                        echo('<li><a class="" href="users.php"><span class="material-symbols-filled">person_edit</span>Användare</a></li>');
                        echo('<li><a class="" href="createUser.php"><span class="material-symbols-filled">person_add</span>Skapa användare</a></li>');
                        echo('<li><a class="" href=""><span class="material-symbols-filled">history</span>Historik</a></li>');
                    }
                ?>
            </ul>
        </nav>
        <main>
            <div class="container">
                <h2>Lägg till förening</h2>
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
                <form method="POST" action="process/process-addAssociation.php">
                    <div>
                        <label for="name">Namn:</label>
                        <input type="text" name="name" id="name" placeholder="Namn" required>
                    </div>
                    <div>
                        <label for="website">Hemsida:</label>
                        <input type="text" name="website" id="website" placeholder="Hemsida" required>
                    </div>
                    <div>
                        <button type="submit">Lägg till förening</button>
                    </div>
                </form>
            </div>

            <div class="container">
                <h2>Föreningar</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>Hemsida</th>
                                <th>Redigera</th>
                                <th>Ta bort</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

                                $querystring = "SELECT AssociationID, Name, Website FROM Associations ORDER BY Name ASC;";

                                $stmt = $pdo->prepare($querystring);
                                $stmt->execute();
                                $queryResult = $stmt->fetchAll();

                                foreach($queryResult as $row)
                                {
                                    echo("<tr>");
                                    echo("<td>" . $row["Name"] . "</td>");
                                    echo("<td><a href='" . $row["Website"] . "'>" . $row["Website"] . "</a></td>");

                                    echo("<td><div class='materialButton'><a href='editAssociation.php?associationID=" . $row["AssociationID"] . "'><span class='material-symbols-filled'>edit</span></a></div></td>");
                                    echo("<td><div class='materialButton'><a href='process/process-deleteAssociation.php?associationID=" . $row["AssociationID"] . "'><span class='material-symbols-filled'>delete</span></a></div></td>");
                                    echo("</tr>");
                                }
                            ?>
                        </tbody>
                    </table>
                </form>
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