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
            
        $querystring = "INSERT INTO Associations (Name, Website) VALUES (:name, :website);";
        $stmt = $pdo->prepare($querystring);

        $stmt->bindParam(":name", $_POST["name"]);
        $stmt->bindParam(":website", $_POST["website"]);
        
        if($stmt->execute())
        {
            $success = "Föreningen lades till";
        }
        else
        {
            $errors = "Något gick fel";
        }
        header("Location: ../associations.php?success=" . $success . "&error=" . $errors);
    }
?>