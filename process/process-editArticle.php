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

        if($_FILES["files"]["name"][0] != "")
        {
            for($i = 0; $i < count($_FILES["files"]["name"]); $i++)
            {
                if(uploadFile($_FILES["files"]["name"][$i], $_FILES["files"]["tmp_name"][$i], "files", $_FILES["files"]["error"][$i], $target_dir, $pdo, $_POST["articleID"], "std", $errors))
                {
                    $uploadedFiles++;
                }
                else
                {
                    $errors .= "Kunde inte spara filen " . $_FILES["files"]["name"][$i] . "AAA <br/>";
                }
            }
            $success .= $uploadedFiles . " fil(er) sparade<br/>";
        }
        
        if($_FILES["risk-assessment"]["name"] != "")
        {
            $querystring = "SELECT FileID FROM Images WHERE ArticleID = :articleID AND Type = 'risk'";
            $stmt = $pdo->prepare($querystring);
            $stmt->bindParam(":articleID", $_POST["articleID"]);
            $stmt->execute();
            $old = $stmt->fetch();

            if(isset($old["FileID"]) && $old["FileID"] != "")
            {
                if(deleteFile($old["FileID"], $errors, $success, $pdo))
                {
                    if(uploadFile($_FILES["risk-assessment"]["name"], $_FILES["risk-assessment"]["tmp_name"], "risk-assessment", $_FILES["risk-assessment"]["error"], $target_dir, $pdo, $_POST["articleID"], "risk", $errors))
                    {
                        $success .= "Riskbedömning sparad<br/>";
                    }
                    else
                    {
                        $errors .= "Kunde inte spara riskbedömning<br/>";
                    }
                }
            }
            else
            {
                if(uploadFile($_FILES["risk-assessment"]["name"], $_FILES["risk-assessment"]["tmp_name"], "risk-assessment", $_FILES["risk-assessment"]["error"], $target_dir, $pdo, $_POST["articleID"], "risk", $errors))
                {
                    $success .= "Riskbedömning sparad<br/>";
                }
                else
                {
                    $errors .= "Kunde inte spara riskbedömning<br/>";
                }
            }
        }
    }
}

function uploadFile($fileName, $tmp_name, $formFieldName, $error, $target_dir, $pdo, $articleID, $type, &$errors)
{
    $target_file = $target_dir . basename($fileName);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


    // Kontrollera om filen har laddats upp
    if(isset($_FILES[$formFieldName]) && $error == 0)
    {
        // Kontrollera filstorlek, filtyp och andra säkerhetskontroller
        // ...

        $fileNameParts = explode(".", $fileName);
        $fileFormat = $fileNameParts[count($fileNameParts) - 1];
        $allowedFormats = array();
        $formatType = "";
        $allowedDocFormats = array("pdf", "docx", "doc", "odt");
        $allowedImgFormats = array("jpg", "jpeg", "png", "gif");
        
        if($type == "std")
        {
            if(in_array($fileFormat, $allowedDocFormats))
            {
                $formatType = "doc";
            }
            else if(in_array($fileFormat, $allowedImgFormats))
            {
                $formatType = "img";
            }
            $allowedFormats = array("pdf", "docx", "doc", "odt", "jpg", "jpeg", "png", "gif");
        }
        else if($type == "risk")
        {
            $formatType = "risk";
            $allowedFormats = array("pdf", "docx", "doc", "odt");
        }

        if (!in_array($fileFormat, $allowedFormats)) 
        {
            $allowedString = "";
            for($i = 0; $i < count($allowedFormats); $i++)
            {
                $allowedString .= $allowedFormats[$i];
                if($i < count($allowedFormats) - 1)
                {
                    $allowedString .= ", ";
                }
            }
            $errors .= "Endast formaten $allowedString är tillåtna<br/>";
            $errors .= "Filen " . $fileName . " är inte av tillåten filtyp<br/>";
            $uploadOk = 0;
        } 
        else 
        {
            // Generera ett unikt filnamn för att undvika duplicering
            $unique_filename = uniqid() . "." . $fileType;
            $target_file = $target_dir . $unique_filename;

            // Flytta filen till dess permanenta plats
            if (move_uploaded_file($tmp_name, $target_file)) {
                // Spara information om bilden i databasen
                $stmt = "INSERT INTO Images (Filenamez, Searchway, ArticleID, Type) VALUES ('$unique_filename', '$target_file', '$articleID', '$formatType')";

                if ($pdo->exec($stmt)) {
                    return true;
                } 
                else 
                {
                    return false;
                }
            } 
            else 
            { 
                return false;
            }
        }
    }
}


if(isset($_POST["deleteImage"]))
{
    foreach($_POST["deleteImage"] as $imageID)
    {
        deleteFile($imageID, $errors, $success, $pdo);
    }
}

function deleteFile($fileID, &$errors, &$success, $pdo)
{
    if($fileID != "")
    {
        $querystring = "SELECT * FROM Images WHERE FileID = :fileID";
        $stmt = $pdo->prepare($querystring);
        $stmt->bindParam(":fileID", $fileID);

        if($stmt->execute())
        {
            $file = $stmt->fetch();
            if($file)
            {
                if(file_exists($file["Searchway"]))
                {
                    try{
                        unlink($file["Searchway"]);
                    }
                    catch(Exception $e)
                    {
                        $errors .= "Kunde inte ta bort filen " . $file["Filenamez"] . "<br/>";
                    }
                    $success .= "Filen " . $file["Filenamez"] . " har tagits bort<br/>";
                }
                else
                {
                    $errors .= "Kunde inte hitta filen " . $file["Filenamez"] . "<br/>";
                }
            }
            else
            {
                $errors .= "Kunde inte hitta filen med ID " . $fileID . "<br/>";
            }
        }
        else
        {
            $errors .= "Kunde inte ta bort filen " . $file["Filenamez"] . "<br/>";
        }        
        $querystring = "DELETE FROM Images WHERE FileID = :imageID";
        $stmt = $pdo->prepare($querystring);
        $stmt->bindParam(":imageID", $fileID);
        return $stmt->execute();
    }
}

header("Location: ../article.php?success=" . $success . "&error=" . $errors . "&articleID=" . $_POST["articleID"]);
?>