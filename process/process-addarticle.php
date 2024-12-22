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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fields = "";
    $parameters = "";

    if($_POST["firmWebsite"] != "")
    {
        $fields .= ", FirmWebsite";
        $parameters .= ", :firmWebsite";
    }
    if($_POST["background"] != "")
    {
        $fields .= ", Background";
        $parameters .= ", :background";
    }
    if($_POST["vehicle"] != "")
    {
        $fields .= ", Vehicle";
        $parameters .= ", :vehicle";
    }
    if($_POST["cost"] != "")
    {
        $fields .= ", Cost";
        $parameters .= ", :cost";
    }

    $querystring = "INSERT INTO Articles (Status, Title, FirmName, FirmAddress, Content, AuthorID, CategoryID, WrittenDate" . $fields . ") VALUES (0, :title, :firmName, :firmAddress, :content, :authorID, :categoryID, :writtenDate" . $parameters . ");";
    $stmt = $pdo->prepare($querystring);

    $stmt->bindParam(":title", $_POST["title"]);
    $stmt->bindParam(":firmName", $_POST["firmName"]);
    $stmt->bindParam(":firmAddress", $_POST["firmAddress"]);
    $stmt->bindParam(":content", $_POST["content"]);
    $stmt->bindParam(":authorID", $_SESSION["userID"]);
    $stmt->bindParam(":categoryID", $_POST["categoryID"]);
    $stmt->bindParam(":writtenDate", date("Y-m-d"));

    if($_POST["firmWebsite"] != "")
    {
        $stmt->bindParam(":firmWebsite", $_POST["firmWebsite"]);
    }
    if($_POST["background"] != "")
    {
        $stmt->bindParam(":background", $_POST["background"]);
    }
    if($_POST["vehicle"] != "")
    {
        $stmt->bindParam(":vehicle", $_POST["vehicle"]);
    }
    if($_POST["cost"] != "")
    {
        $stmt->bindParam(":cost", $_POST["cost"]);
    }

    if ($stmt->execute()) {
        $success .= "Artikeln har skickats in för granskning<br/>";
        $articleSaved = true;
        $articleID = $pdo->lastInsertId();
    } 
    else 
    {
        $errors .= "Kunde inte spara artikeln i databasen<br/>";
    }

    $querystring = "INSERT INTO ArticleStatusChanges (ArticleID, NewStatus, POT, UserID) VALUES (:articleID, '0', :pot, :userID);";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":articleID", $articleID);
    $stmt->bindParam(":pot", date("Y-m-d H:i:s"));
    $stmt->bindParam(":userID", $_SESSION["userID"]);

    if($articleSaved)
    {
        if(!$stmt->execute())
        {
            $errors .= "Kunde inte uppdatera artikelstatus<br/>";
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

header("Location: ../article.php?success=" . $success . "&error=" . $errors . "&articleID=" . $articleID);
?>