<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }
    if($_SESSION["author"] == "0")
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
    $querystring = "SELECT Articles.FirmAddress AS FirmAddress, Articles.CategoryID AS CategoryID, Articles.Content AS Content, Articles.Background AS Background, Articles.Vehicle AS Vehicle, Articles.Cost AS Cost, Articles.FirmName AS FirmName, Articles.FirmWebsite AS FirmWebsite, Articles.Title AS Title, Articles.FirmName AS FirmName, Users.Name AS FullName, Users.AssociationRole AS AssociationRole, Associations.Name AS AssociationName, Articles.WrittenDate AS WrittenDate, Articles.ArticleID AS ArticleID, Articles.AuthorID AS AuthorID, Articles.Status AS Status FROM Articles INNER JOIN Users ON Articles.AuthorID = Users.UserID INNER JOIN Associations ON Users.AssociationID = Associations.AssociationID WHERE ArticleID = :articleID;";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":articleID", $_GET["articleID"]);
    $stmt->execute();
    $articles = $stmt->fetchAll();
    if(count($articles) == 0)
    {
        Header("Location: article.php?articleID=" . $_GET["articleID"]);
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
    if(!$qualify)
    {
        Header("Location: article.php?articleID=" . $_GET["articleID"]);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redigera artikel</title>
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
                <h2>Redigera artikel</h2>
                <form action="process/process-editArticle.php" method="POST" enctype="multipart/form-data">
                    <?php
                        echo("<input type='hidden' name='articleID' value='" . $articles[0]["ArticleID"] . "'>");
                    ?>

                    <div>
                        <label for="title">Titel: <span class="reqStar">*</span></label>
                        <?php echo("<input type='text' name='title' id='title' placeholder='Ex. Takstagsbyte' value='" . $articles[0]["Title"] . "'>")?>
                    </div>
                    <div>
                    <label for="category">Kategori</label>
                        <?php
                            echo '<select name="category" id="category">';
                            if(isset($articles[0]["category"]) && $articles[0]["category"] != "")
                            {
                                echo('<option value="' . $articles[0]["category"] . '">' . getCategoryName($categories, $_GET["category"]) . '</option>');
                            }
                            echoCategories($categories);
                            echo '</select>';
                        ?>
                    </div>
                    <div>
                        <label for="firmName">Företagsnamn: <span class="reqStar">*</span></label>
                        <?php echo("<input type='text' name='firmName' id='firmName' placeholder='Ex. Gössäter mekaniska verkstad' value='" . $articles[0]["FirmName"] . "'>")?>
                    </div>
                    <div>
                        <label for="firmAddress">Företagsaddress: <span class="reqStar">*</span></label>
                        <?php echo("<input type='text' name='firmAddress' id='firmAddress' placeholder='Ex. Gössäter Stationsvägen 4, 533 94 Hällekis' value='" . $articles[0]["FirmAddress"] . "'>")?>
                    </div>
                    <div>
                        <label for="firmWebsite">Ev. Hemsida:</label>
                        <?php echo("<input type='text' name='firmWebsite' id='firmWebsite' placeholder='Ex. www.gossatermekaniska.se' value='" . $articles[0]["FirmWebsite"] . "'>")?>
                    </div>
                    <div>
                        <label for="background">Bakgrund:</label>
                        <?php echo("<textarea name='background' id='background' cols='30' rows='7' placeholder='Varför?'>" . $articles[0]["Background"] . "</textarea>")?>
                    </div>
                    <div>
                        <label for="content">Innehåll: <span class="reqStar">*</span></label>
                        <?php echo("<textarea name='content' id='content' cols='30' rows='7' placeholder='Hur, när, resultat?' required>" . $articles[0]["Content"] . "</textarea>")?>
                    </div>
                    <div>
                        <label for="Vehicle">Fordon: </label>
                        <?php echo("<input type='text' name='vehicle' id='Vehicle' placeholder='Ex. VGJ 4' value='" . $articles[0]["Vehicle"] . "'>")?>
                    </div>
                    <div>
                        <label for="cost">Kostnad: </label>
                        <?php echo("<input style='width: auto;' type='number' name='cost' id='cost' placeholder='' value='" . $articles[0]["Cost"] . "'>")?>
                        <span style="font-weight: 900;">kr</span>
                    </div>

                    <?php
                        $querystring = "SELECT FileID, Filenamez FROM Images WHERE ArticleID = :articleID;";
                        $stmt = $pdo->prepare($querystring);
                        $stmt->bindParam(":articleID", $articles[0]["ArticleID"]);
                        $stmt->execute();
                        $images = $stmt->fetchAll();
        
                        echo("<div class='imageContainer'>");
                        $num = 0;
                        foreach($images as $image)
                        {
                            echo("<div class='imgContainer' data-imageID='" . $image["FileID"] . "'>");
                            echo("<a class='articleImageEdit' target='_blank' href='fullPic.php?image=" . $image["Filenamez"] ."'><img src='process/process-fetchImage.php?image=" . $image["Filenamez"] . "' alt='Bild " . $num . "'>");
                            echo("</a>");
                            echo("<div class='deleteImage' style=''>");
                            echo("<span class='material-symbols-filled'>delete</span>");
                            echo("<input class='deleteInput' type='hidden' name='deleteImage[]' value=''>");
                            echo("</div>");
                            echo("</div>");
                        }
                        echo("</div>");
                    ?>

                    <script src="javascript/deletePicture.js"></script>

                    <label for="image">Bilder:</label>
                    <div>Tillåtna format: JPG, JPEG, PNG och GIF!</div>
                    <input type="file" name="files[]" id="image" multiple>

                    <div>Fält markerade med "<span class="reqStar">*</span>" måste vara ifyllda!</div>
                    <div>Artikeln skickas in för granskning när den sparas.</div>
                    <button type="submit">Spara</button>
                </form>
            </div>
        </main>
        <footer>
            <div>Prototyp 1</div>
            <div>Det ser bättre ut med tre texter</div>
            <div>Uppdaterad: 2024-10-23</div>
        </footer>
    </div>
</body>
</html>