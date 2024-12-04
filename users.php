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
                        echo('<li><a class="" href="assess.php"><span class="material-symbols-filled">shield</span>Väntande artiklar</a></li>');
                    }
                    if($_SESSION["admin"] == "1")
                    {
                        echo('<li><a class="" href="associations.php"><span class="material-symbols-filled">group</span>Föreningar</a></li>');
                        echo('<li><a class="active" href="users.php"><span class="material-symbols-filled">person_edit</span>Användare</a></li>');
                        echo('<li><a class="" href="createUser.php"><span class="material-symbols-filled">person_add</span>Skapa användare</a></li>');
                        echo('<li><a class="" href=""><span class="material-symbols-filled">history</span>Historik</a></li>');
                    }
                ?>
            </ul>
        </nav>
        <main>
            <div class="container">
                <h2>Befintliga användare</h2>
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
                <form method="GET" action="">
                    <label for="name">Namn</label>
                    <input type="text" name="name" id="name" placeholder="Ex. Magnus Karlsson">
                    <label for="userName">Användarnamn</label>
                    <input type="text" name="password" id="password" placeholder="Lösenord">
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
                    <button type="submit">Filtrera</button>
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
                                    echo("<td><div class='materialButton'><a href='editUser.php?userID=" . $row["UserID"] . "'><span class='material-symbols-filled'>edit</span></a></div></td>");
                                    echo("<td><div class='materialButton'><a href='process/process-deleteUser.php?userID=" . $row["UserID"] . "'><span class='material-symbols-filled'>delete</span></a></div></td>");
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