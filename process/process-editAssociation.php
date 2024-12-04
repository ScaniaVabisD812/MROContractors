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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $changes = "";
        $comma = false;

        if($_POST["associationID"] != "")
        {
            if(isset($_POST["name"]))
            {
                if($_POST["name"] != "")
                {
                    $changes .= "Name = :name";
                    $comma = true;
                }
            }

            if(isset($_POST["website"]))
            {
                if($_POST["website"] != "")
                {
                    if($_POST["website"] != "")
                    {
                        if($comma)
                        {
                            $changes .= ", ";
                        }
                        $changes .= "Website = :website";
                    }
                }
            }

            if($changes != "")
            {
                $querystring = "UPDATE Associations SET " . $changes . " WHERE AssociationID = :associationID;";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":associationID", $_POST["associationID"]);
                
                if(isset($_POST["name"]))
                {
                    if($_POST["name"] != "")
                    {
                        $stmt->bindParam(":name", $_POST["name"]);
                    }
                }
                if(isset($_POST["website"]))
                {
                    if($_POST["website"] != "")
                    {
                        $stmt->bindParam(":website", $_POST["website"]);
                    }
                }
    
                try
                {
                    $stmt->execute();
                    $success = "Förändringarna har sparats!";
                }
                catch(Exception $e)
                {
                    $errors = "Något gick fel: " . $e->getMessage();
                }
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
    if($errors != "")
    {
        header("Location: ../editAssociation.php?success=" . $success . "&error=" . $errors . "&associationID=" . $_POST["associationID"]);
        die();
    }
    header("Location: ../associations.php?success=" . $success . "&error=" . $errors);
?>