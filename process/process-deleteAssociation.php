<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }
    if($_SESSION["admin"] == "0")
    {
        header("Location: index.php");
    }

    require_once '../../../httpd.private/config.php';
    $errors = "";
    $success = "";

    $DBServer = DB_SERVER;
    $DBUsername = DB_USERNAME;
    $DBPassword = DB_PASSWORD;
    $DBName = DB_NAME;

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        if($_GET["associationID"] != "")
        {
            $querystring = "DELETE FROM Associations WHERE AssociationID = :AssociationID;";
            $stmt = $pdo->prepare($querystring);
            $stmt->bindParam(":AssociationID", $_GET["associationID"]);
            try
            {
                $stmt->execute();
                $success = "Föreningen har tagits bort!";
            }
            catch(Exception $e)
            {
                $errors = "Något gick fel: " . $e->getMessage();
            }
        }
        else
        {
            $errors = "Du måste välja en förening!";
        }
    }
    else
    {
        $errors = "Något gick fel...";
    }
    header("Location: ../associations.php?success=" . $success . "&error=" . $errors);
?>