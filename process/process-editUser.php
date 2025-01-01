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

        if($_POST["userID"] != "")
        {
            if(isset($_POST["author"]))
            {
                $_POST["author"] = 1;
                $changes .= "Author = 1";
            }
            else
            {
                $_POST["author"] = 0;
                $changes .= "Author = 0";
            }

            if(isset($_POST["moderator"]))
            {
                $_POST["moderator"] = 1;
                $changes .= ", Moderator = 1";
            }
            else
            {
                $_POST["moderator"] = 0;
                $changes .= ", Moderator = 0";
            }

            if(isset($_POST["admin"]))
            {
                $_POST["admin"] = 1;
                $changes .= ", Admin = 1";
            }
            else
            {
                $_POST["admin"] = 0;
                $changes .= ", Admin = 0";
            }
            
            if(isset($_POST["username"]))
            {
                if($_POST["username"] != "")
                {
                    $changes .= ", Username = :username";
                }
            }

            if(isset($_POST["changePassword"]))
            {
                if($_POST["changePassword"] == "on")
                {
                    $changes .= ", Passwordz = :password";
                }
            }

            if(isset($_POST["name"]))
            {
                if($_POST["name"] != "")
                {
                    $changes .= ", Name = :name";
                }
            }

            if(isset($_POST["associationID"]))
            {
                $changes .= ", AssociationID = :associationID";
            }

            if(isset($_POST["associationRole"]))
            {
                if($_POST["associationRole"] != "")
                {
                    $changes .= ", AssociationRole = :associationRole";
                }
            }

            if($changes != "")
            {
                $querystring = "UPDATE Users SET " . $changes . " WHERE UserID = :userID;";
                $stmt = $pdo->prepare($querystring);
                $stmt->bindParam(":userID", $_POST["userID"]);
                
                if(isset($_POST["username"]))
                {
                    if($_POST["username"] != "")
                    {
                        $stmt->bindParam(":username", $_POST["username"]);
                    }
                }
                if(isset($_POST["changePassword"]))
                {
                    if($_POST["changePassword"] == "on")
                    {
                        $password = hash("sha256", "bytLösenord!");
                        $stmt->bindParam(":password", $password);
                    }
                }
                if(isset($_POST["name"]))
                {
                    if($_POST["name"] != "")
                    {
                        $stmt->bindParam(":name", $_POST["name"]);
                    }
                }
                if(isset($_POST["associationID"]))
                {
                    if($_POST["associationID"] != "")
                    {
                        $stmt->bindParam(":associationID", $_POST["associationID"]);
                    }
                }
                if(isset($_POST["associationRole"]))
                {
                    if($_POST["associationRole"] != "")
                    {
                        $stmt->bindParam(":associationRole", $_POST["associationRole"]);
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
            $errors = "Du måste välja en användare!";
        }
    }
    else
    {
        $errors = "Något gick fel...";
    }
    if($errors != "")
    {
        header("Location: ../editUser.php?success=" . $success . "&error=" . $errors . "&userID=" . $_POST["userID"]);
        die();
    }
    header("Location: ../users.php?success=" . $success . "&error=" . $errors);
?>