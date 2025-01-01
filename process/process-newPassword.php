<?php
    session_start();

    if(!isset($_SESSION["username"]))
    {
        header("Location: login.php");
    }

    if(!$_SESSION["changePassword"])
    {
        header("Location: index.php");
        die();
    }

    require_once '../../../httpd.private/config.php';
    $errors = "";
    $success = "";

    $DBServer = DB_SERVER;
    $DBUsername = DB_USERNAME;
    $DBPassword = DB_PASSWORD;
    $DBName = DB_NAME;

    $pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

    if ($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        if($_POST["password"] != $_POST["password2"])
        {
            $errors .= "Lösenorden matchar inte<br>";

            header("Location: ../changePassword.php?error=$errors");
            die();
        }

        $querystring = "UPDATE Users SET Passwordz = :password WHERE UserID = :userID;";
        $stmt = $pdo->prepare($querystring);
        $password = hash("sha256", $_POST["password"]);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":userID", $_SESSION["userID"]);
        $stmt->execute();
        $_SESSION["changePassword"] = false;
        header("Location: ../index.php");
        die();
    }
?>