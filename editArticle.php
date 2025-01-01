<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
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
                        echo("<li><a href=''><div class='material-symbols-outlined menuIcon'>history</div><div>Historik</div></a></li>");
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
                            echo '<select name="categoryID" id="categoryID">';
                            if(isset($articles[0]["CategoryID"]) && $articles[0]["CategoryID"] != "")
                            {
                                echo('<option value="' . $articles[0]["CategoryID"] . '">' . getCategoryName($categories, $articles[0]["CategoryID"]) . '</option>');
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