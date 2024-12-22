<?php
session_start();

if(!isset($_SESSION["username"]))
{
    header("Location: login.php");
}
if($_SESSION["author"] == "0")
{
    header("Location: index.php");
}

require_once '../../../httpd.private/config.php';
$errors = "";
$success = "";
$uploadedFiles = 0;

$articleSaved = false;

$DBServer = DB_SERVER;
$DBUsername = DB_USERNAME;
$DBPassword = DB_PASSWORD;
$DBName = DB_NAME;

$pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

$querystring = "SELECT * FROM Articles WHERE ArticleID = :articleID";
$stmt = $pdo->prepare($querystring);
$stmt->bindParam(":articleID", $_POST["articleID"]);
$stmt->execute();
$article = $stmt->fetch();

$qualify = false;
if($_SESSION["moderator"] == "1" || $_SESSION["admin"] == "1") 
{
    $qualify = true;
} 
else if($article["AuthorID"] == $_SESSION["userID"]) 
{
    $qualify = true;
}

if(!$qualify) {
    header("Location: ../article.php?articleID=" . $_POST["articleID"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $status = 0;
    if($_SESSION["moderator"] == "1" || $_SESSION["admin"] == "1") 
    {
        $status = $article["Status"];
    }
    if($article["AuthorID"] == $_SESSION["userID"]) 
    {
        $status = 0;
    }

    $fields = "";
    $params = array(
        ':title' => $_POST["title"],
        ':status' => $status,
        ':firmName' => $_POST["firmName"],
        ':firmAddress' => $_POST["firmAddress"],
        ':content' => $_POST["content"],
        ':categoryID' => $_POST["categoryID"],
        ':writtenDate' => $article["WrittenDate"],
        ':articleID' => $_POST["articleID"]
    );

    if($_POST["firmWebsite"] != "") {
        $fields .= ", FirmWebsite = :firmWebsite";
        $params[':firmWebsite'] = $_POST["firmWebsite"];
    }
    if($_POST["background"] != "") {
        $fields .= ", Background = :background";
        $params[':background'] = $_POST["background"];
    }
    if($_POST["vehicle"] != "") {
        $fields .= ", Vehicle = :vehicle";
        $params[':vehicle'] = $_POST["vehicle"];
    }
    if($_POST["cost"] != "") {
        $fields .= ", Cost = :cost";
        $params[':cost'] = $_POST["cost"];
    }

    $querystring = "UPDATE Articles SET Title = :title, Status = :status, FirmName = :firmName, FirmAddress = :firmAddress, Content = :content, CategoryID = :categoryID, WrittenDate = :writtenDate" . $fields . " WHERE ArticleID = :articleID";
    $stmt = $pdo->prepare($querystring);

    foreach ($params as $key => &$val) {
        $stmt->bindParam($key, $val);
    }

    if ($stmt->execute()) {
        $success .= "Artikeln har skickats in för granskning<br/>";
        $articleSaved = true;
    } 
    else 
    {
        $errors .= "Kunde inte spara artikeln i databasen<br/>";
    }

    $querystring = "INSERT INTO ArticleStatusChanges (ArticleID, NewStatus, POT, UserID, Message) VALUES (:articleID, $status, :pot, :userID, 'Redigering');";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":articleID", $_POST["articleID"]);
    $stmt->bindParam(":pot", date("Y-m-d H:i:s"));
    $stmt->bindParam(":userID", $_SESSION["userID"]);

    if($articleSaved)
    {
        try{
            $stmt->execute();
        }
        catch(PDOException $e)
        {
            $errors .= "Kunde inte spara statusändring<br/>";
            $articleSaved = false;
        }
    }
}

if($articleSaved)
{
    // Kontrollera om formuläret har skickats
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Mapp där bilderna ska sparas
        $target_dir = "../../../httpd.private/uploads/";

        for($i = 0; $i < count($_FILES["files"]["name"]); $i++)
        {

            $target_file = $target_dir . basename($_FILES["files"]["name"][$i]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


            // Kontrollera om filen har laddats upp
            if(isset($_FILES["files"]) && $_FILES["files"]["error"][$i] == 0)
            {
                // Kontrollera filstorlek, filtyp och andra säkerhetskontroller
                // ...

                $fileNameParts = explode(".", $_FILES["files"]["name"][$i]);
                $fileFormat = $fileNameParts[count($fileNameParts) - 1];
                $allowedFormats = array("jpg", "jpeg", "png", "gif");
                if (!in_array($fileFormat, $allowedFormats)) {
                    $errors .= "Endast JPG, JPEG, PNG och GIF-filer är tillåtna<br/>";
                    $errors .= "Filen " . $_FILES["files"]["name"][$i] . " är inte av tillåten filtyp<br/>";
                    $uploadOk = 0;
                } 
                else 
                {
                    // Generera ett unikt filnamn för att undvika duplicering
                    $unique_filename = uniqid() . "." . $imageFileType;
                    $target_file = $target_dir . $unique_filename;

                    // Flytta filen till dess permanenta plats
                    if (move_uploaded_file($_FILES["files"]["tmp_name"][$i], $target_file)) {
                        // Spara information om bilden i databasen
                        $articleID = $_POST["articleID"];
                        $stmt = "INSERT INTO Images (Filenamez, Searchway, ArticleID) VALUES ('$unique_filename', '$target_file', '$articleID')";

                        if ($pdo->exec($stmt)) {
                            $uploadedFiles++;
                        } 
                        else 
                        {
                            $errors .= "Kunde inte spara " . $_FILES["files"]["name"][$i] . " i databasen<br/>";
                        }
                    } 
                    else 
                    { 
                        $errors .= "Kunde inte spara " . $_FILES["files"]["name"][$i] . " på servern<br/>";
                    }
                }
            }
        }
        $success .= $uploadedFiles . " bild(er) sparade<br/>";
    }
}


if(isset($_POST["deleteImage"]))
{
    foreach($_POST["deleteImage"] as $imageID)
    {
        if($imageID != "")
        {
            $querystring = "SELECT * FROM Images WHERE FileID = :imageID";
            $stmt = $pdo->prepare($querystring);
            $stmt->bindParam(":imageID", $imageID);
    
            if($stmt->execute())
            {
                $image = $stmt->fetch();
                if(file_exists($image["Searchway"]))
                {
                    try{
                        unlink($image["Searchway"]);
                    }
                    catch(Exception $e)
                    {
                        $errors .= "Kunde inte ta bort bilden " . $image["Filenamez"] . "<br/>";
                    }
                    $success .= "Bilden " . $image["Filenamez"] . " har tagits bort<br/>";
                }
                else
                {
                    $errors .= "Kunde inte hitta bilden " . $image["Filenamez"] . "<br/>";
                }
            }
            else
            {
                $errors .= "Kunde inte ta bort bilden " . $image["Filenamez"] . "<br/>";
            }        
            $querystring = "DELETE FROM Images WHERE FileID = :imageID";
            $stmt = $pdo->prepare($querystring);
            $stmt->bindParam(":imageID", $imageID);
            $stmt->execute();
        }
    }
}


header("Location: ../article.php?success=" . $success . "&error=" . $errors . "&articleID=" . $_POST["articleID"]);
?>