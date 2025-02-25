<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }
    if($_SESSION["moderator"] == "0")
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
        
        if(isset($_GET["articleID"]))
        {
            if($_GET["articleID"] != "")
            {
                $querystring = "UPDATE Articles SET Status = 2 WHERE ArticleID = :articleID;";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":articleID", $_GET["articleID"]);
                
                try{
                    $stmt->execute();
                    $success = "Artikeln har godkänts!";
                }
                catch(PDOException $e)
                {
                    $errors = "Något gick fel... A";
                }
                
                $querystring = "INSERT INTO ArticleStatusChanges (ArticleID, NewStatus, POT, UserID) VALUES (:articleID, 2, :pot, :userID);";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":articleID", $_GET["articleID"]);
                $stmt->bindParam(":pot", date("Y-m-d H:i:s"));
                $stmt->bindParam(":userID", $_SESSION["userID"]);

                try{
                    $stmt->execute();
                }
                catch(PDOException $e)
                {
                    $errors = "Något gick fel... " . $e;
                }

                $querystring = "INSERT INTO Log(Interaction, UserID, ObjType, ObjVal) VALUES ('2', :userID, 'Article', :articleID);";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":userID", $_SESSION["userID"]);
                $stmt->bindParam(":articleID", $_GET["articleID"]);
                try{
                    $stmt->execute();
                }
                catch(PDOException $e)
                {
                    
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
        $errors = "Något gick fel... B";
    }
    $success = str_replace(array("\r", "\n"), '', $success);
    $errors = str_replace(array("\r", "\n"), '', $errors);
    header("Location: ../assess.php?success=" . $success . "&error=" . $errors);
    die();
?>