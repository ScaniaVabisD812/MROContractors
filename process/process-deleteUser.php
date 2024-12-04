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
        if($_GET["userID"] != "")
        {
            $querystring = "DELETE FROM Users WHERE UserID = :UserID;";
            $stmt = $pdo->prepare($querystring);
            $stmt->bindParam(":UserID", $_GET["userID"]);
            try
            {
                $stmt->execute();
                $success = "Användaren har tagits bort!";
            }
            catch(Exception $e)
            {
                $errors = "Något gick fel: " . $e->getMessage();
            }
        }
        else
        {
            $errors = "Du måste välja en användare!";
        }
    }
    else
    {
        $errors = "Något gick fel...";
    }
    header("Location: ../users.php?success=" . $success . "&error=" . $errors);
?>