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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        if(isset($_POST["articleID"]))
        {
            if($_POST["articleID"] != "")
            {
                $querystring = "UPDATE Articles SET Status = 1 WHERE ArticleID = :articleID;";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":articleID", $_POST["articleID"]);
                
                try{
                    $stmt->execute();
                    $success = "Artikeln har nekats!";
                }
                catch(PDOException $e)
                {
                    $errors = "Något gick fel...";
                }
                
                $querystring = "INSERT INTO ArticleStatusChanges (ArticleID, NewStatus, POT, Message, UserID) VALUES (:articleID, '1', :pot, :message, :userID);";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":articleID", $_POST["articleID"]);
                $stmt->bindParam(":pot", date("Y-m-d H:i:s"));
                $stmt->bindParam(":message", $_POST["message"]);
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
        header("Location: ../assess.php?success=" . $success . "&error=" . $errors);
        die();
    }
    header("Location: ../assess.php?success=" . $success . "&error=" . $errors);
?>