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

        if(isset($_POST["author"]))
        {
            if($_POST["author"] == "on")
            {
                $_POST["author"] = 1;
            }
            else
            {
                $_POST["author"] = 0;
            }
        }

        if(isset($_POST["moderator"]))
        {
            if($_POST["moderator"] == "on")
            {
                $_POST["moderator"] = 1;
            }
            else
            {
                $_POST["moderator"] = 0;
            }
        }

        if(isset($_POST["admin"]))
        {
            if($_POST["admin"] == "on")
            {
                $_POST["admin"] = 1;
            }
            else
            {
                $_POST["admin"] = 0;
            }
        }

        if($_POST["associationID"] != "")
        {
            $querystring = "INSERT INTO Users (Username, Passwordz, Name, Author, Moderator, Admin, AssociationID) VALUES (:username, :password, :name, :author, :moderator, :admin, :associationID);";
            $stmt = $pdo->prepare($querystring);

            $stmt->bindParam(":username", $_POST["username"]);
            $stmt->bindParam(":password", $_POST["password"]);
            $stmt->bindParam(":name", $_POST["name"]);
            $stmt->bindParam(":author", $_POST["author"]);
            $stmt->bindParam(":moderator", $_POST["moderator"]);
            $stmt->bindParam(":admin", $_POST["admin"]);
            $stmt->bindParam(":associationID", $_POST["associationID"]);

            try
            {
                $stmt->execute();
                $success = "Användaren har lagts till!";
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
    header("Location: ../users.php?success=" . $success . "&error=" . $errors);
?>