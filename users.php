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
    <title>Användare</title>
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
                        echo("<li><a href='users.php'><div class='material-symbols-filled menuIcon'>person_edit</div><div>Användare</div></a></li>");
                        echo("<li><a href='createUser.php'><div class='material-symbols-outlined menuIcon'>person_add</div><div>Skapa användare</div></a></li>");
                        echo("<li><a href=''><div class='material-symbols-outlined menuIcon'>history</div><div>Historik</div></a></li>");
                    }
                ?>
            </ul>
        </nav>
        <main>
            <div class="container">
                <h2 id="filterTitle" class="filterTitle">Befintliga användare</h2>
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
                <span id="toggle" class="filterButton material-symbols-filled">arrow_downward</span>
                <section id="section">
                    <form method="GET" action="">
                        <label for="name">Namn</label>
                        <input type="text" name="name" id="name" placeholder="Ex. Magnus Karlsson">
                        <label for="username">Användarnamn</label>
                        <input type="text" name="username" id="username" placeholder="Användarnamn">
                        <label for="association"></label>
                        <?php
                            $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);
                            $querystring = "SELECT * FROM Associations ORDER BY Name ASC;";
                            $stmt = $pdo->prepare($querystring);
                            $stmt->execute();
                            $queryResult = $stmt->fetchAll();
                        ?>
                        <select name="association" id="association">
                            <option value="" disabled selected>Förening</option>
                            <?php
                                foreach($queryResult as $row)
                                {
                                    echo("<option value='" . $row["AssociationID"] . "'>" . $row["Name"] . "</option>");
                                }
                            ?>
                        </select>
                        <button class="primaryContainer" type="submit">Filtrera</button>
                    </form>
                </section>
                <script src="javascript/dropdown.js"></script>
                    <table>
                        <thead>
                            <tr>
                                <th>Namn</th>
                                <th>Användarnamn</th>
                                <th>Lösenord</th>
                                <th>Förening</th>
                                <th>Författare</th>
                                <th>Moderator</th>
                                <th>Admin</th>
                                <th>Redigera</th>
                                <th>Ta bort</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $querystring = "SELECT UserID, Users.Name AS UName, Username, AssociationRole, Passwordz, Associations.Name AS AName, Author, Moderator, Admin FROM Users INNER JOIN Associations ON Users.AssociationID = Associations.AssociationID";

                                if(isset($_GET["name"]))
                                {
                                    if($_GET["name"] != "")
                                    {
                                        $querystring .= " WHERE Users.Name LIKE '%" . $_GET["name"] . "%'";
                                    }
                                }
                                if(isset($_GET["username"]))
                                {
                                    if($_GET["username"] != "")
                                    {
                                        if(strpos($querystring, "WHERE") === false)
                                        {
                                            $querystring .= " WHERE Username LIKE '%" . $_GET["username"] . "%'";
                                        }
                                        else
                                        {
                                            $querystring .= " AND Username LIKE '%" . $_GET["username"] . "%'";
                                        }
                                    }
                                }
                                if(isset($_GET["association"]))
                                {
                                    if($_GET["association"] != "")
                                    {
                                        if(strpos($querystring, "WHERE") === false)
                                        {
                                            $querystring .= " WHERE Users.AssociationID = " . $_GET["association"];
                                        }
                                        else
                                        {
                                            $querystring .= " AND Users.AssociationID = " . $_GET["association"];
                                        }
                                    }
                                }

                                $querystring .= " ORDER BY Users.Name ASC;";

                                $stmt = $pdo->prepare($querystring);
                                $stmt->execute();
                                $queryResult = $stmt->fetchAll();

                                foreach($queryResult as $row)
                                {
                                    echo("<tr>");
                                    echo("<td>" . $row["UName"] . "</td>");
                                    echo("<td>" . $row["Username"] . "</td>");
                                    echo("<td>" . $row["Passwordz"] . "</td>");
                                    echo("<td> <div>" . $row["AssociationRole"] . "</div> <div>" . $row["AName"] . "</div> </td>");
                                    if($row["Author"] == 1)
                                    {
                                        echo("<td class='greenRole'>Ja</td>");
                                    }
                                    else
                                    {
                                        echo("<td class='redRole'>Nej</td>");
                                    }
                                    if($row["Moderator"] == 1)
                                    {
                                        echo("<td class='greenRole'>Ja</td>");
                                    }
                                    else
                                    {
                                        echo("<td class='redRole'>Nej</td>");
                                    }
                                    if($row["Admin"] == 1)
                                    {
                                        echo("<td class='greenRole'>Ja</td>");
                                    }
                                    else
                                    {
                                        echo("<td class='redRole'>Nej</td>");
                                    }
                                    if($row["UserID"] != $_SESSION["userID"])
                                    {
                                        echo("<td><div class='materialButton'><a href='editUser.php?userID=" . $row["UserID"] . "'><span class='material-symbols-outlined'>edit</span></a></div></td>");
                                        echo("<td><div class='materialButton'><a href='process/process-deleteUser.php?userID=" . $row["UserID"] . "'><span class='material-symbols-outlined'>delete</span></a></div></td>");
                                    }
                                    else
                                    {
                                        echo("<td></td>");
                                        echo("<td></td>");
                                    }
                                    echo("</tr>");
                                }
                            ?>
                        </tbody>
                    </table>
                </form>
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