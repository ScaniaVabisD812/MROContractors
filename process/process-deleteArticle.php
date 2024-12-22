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
$stmt->bindParam(":articleID", $_GET["articleID"]);
$stmt->execute();
$article = $stmt->fetch();

$qualify = false;
$status = 3;
if($_SESSION["moderator"] == "1" || $_SESSION["admin"] == "1") 
{
    $qualify = true;
    $status = 4;
} 
if($article["AuthorID"] == $_SESSION["userID"]) 
{
    $qualify = true;
    $status = 3;
}

if(!$qualify) 
{
    header("Location: ../article.php?articleID=" . $_GET["articleID"]);
    exit();
}

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        
        if(isset($_GET["articleID"]))
        {
            if($_GET["articleID"] != "")
            {
                $querystring = "UPDATE Articles SET Status = :status WHERE ArticleID = :articleID;";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":status", $status);
                $stmt->bindParam(":articleID", $_GET["articleID"]);
                
                try{
                    $stmt->execute();
                    $success = "Artikeln har tagits bort!";
                }
                catch(PDOException $e)
                {
                    $errors = "Något gick fel...";
                }
                
                $querystring = "INSERT INTO ArticleStatusChanges (ArticleID, NewStatus, POT, Message, UserID) VALUES (:articleID, :status, :pot, :message, :userID);";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":articleID", $_GET["articleID"]);
                $stmt->bindParam(":pot", date("Y-m-d H:i:s"));
                $stmt->bindParam(":status", $status);
                if($status == 4)
                {
                    $message = "Borttagen av moderator";
                    $stmt->bindParam(":message", $message);
                }
                else
                {
                    $message = "Borttagen av författare";
                    $stmt->bindParam(":message", $message);
                }
                $stmt->bindParam(":userID", $_SESSION["userID"]);

                try{
                    $stmt->execute();
                }
                catch(PDOException $e)
                {
                    $errors = "Något gick fel... " . $e;
                }
            }
        }

        else
        {
            $errors = "Du måste välja en artikel!";
        }
    }
    else
    {
        $errors = "Något gick fel...";
    }
    if($errors != "")
    {
        header("Location: ../article.php?success=" . $success . "&error=" . $errors . "&articleID=" . $_GET["articleID"]);
        die();
    }
    header("Location: ../article.php?success=" . $success . "&error=" . $errors . "&articleID=" . $_GET["articleID"]);
?>