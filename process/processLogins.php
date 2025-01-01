<?php
require_once '../../../httpd.private/config.php';

$DBServer = DB_SERVER;
$DBUsername = DB_USERNAME;
$DBPassword = DB_PASSWORD;
$DBName = DB_NAME;

$pdo = new PDO("mysql:host=$DBServer;dbname=$DBName", $DBUsername, $DBPassword);

session_start();

if(isset($_POST["username"]) && isset($_POST["password"]))
{
    //$password = hash("sha256", $_POST["password"]);

    $username = $_POST["username"];
    $password = hash("sha256", $_POST["password"]);

    $querystring = "SELECT * FROM Users WHERE Username = :username AND Passwordz = :password;";
    $stmt = $pdo->prepare($querystring);
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->execute();
    $queryResult = $stmt->fetchAll();
    $passwordDB = $queryResult[0]["Passwordz"];

    if(count($queryResult) > 0)
    {
        $_SESSION["username"] = $queryResult[0]["Username"];
        $_SESSION["userID"] = $queryResult[0]["UserID"];
        $_SESSION["name"] = $queryResult[0]["Name"];
        $_SESSION["author"] = $queryResult[0]["Author"];
        $_SESSION["moderator"] = $queryResult[0]["Moderator"];
        $_SESSION["admin"] = $queryResult[0]["Admin"];
        
        $associationID = $queryResult[0]["AssociationID"];

        $querystring = "SELECT * FROM Associations WHERE AssociationID = :associationID;";
        $stmt = $pdo->prepare($querystring);
        $stmt->bindParam(":associationID", $associationID);
        $stmt->execute();
        $queryResult = $stmt->fetchAll();
        
        $_SESSION["associationName"] = $queryResult[0]["Name"];

        if($passwordDB == hash("sha256", "bytLösenord!"))
        {
            $_SESSION["changePassword"] = true;
            header("Location: ../changePassword.php");
            die();
        }
        $_SESSION["changePassword"] = false;
        header("Location: ../index.php");
        die();
    }
    else
    {
        header("Location: ../login.php?error=Inloggning misslyckades");
        die();
    }
}
else if(isset($_GET["logoutReq"]))
{
    session_destroy();
    header("Location: ../login.php");
    die();
}
else
{
    header("Location: ../login.php");
    die();
}
?>