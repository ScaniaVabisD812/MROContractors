<?php
// Anslut till databasen (ersätt med dina egna uppgifter)
require_once '../../httpd.private/config.php';
$errors = "";
$success = "";
$uploadedFiles = 0;

$articleSaved = false;

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) 
{
    $errors .= "Databasfel<br/>";
}
else
{
    $querystring = "INSERT INTO Articles (Title, FirmName, FirmAddress, Content) VALUES (:title, :firmName, :firmAddress, :content);";
    $stmt = $conn->prepare($querystring);
    $stmt->bind_param(":title", "STRING");
    $stmt->bind_param(":FirmName", "AAA");
    $stmt->bind_param(":FirmAddress", "BBB");
    $stmt->bind_param(":Content", "CCC");

    echo("<pre>" . print_r($stmt) . "</pre>");
    $stmt->execute();
}
$conn->close();
?>