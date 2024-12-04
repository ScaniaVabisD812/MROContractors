<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bild</title>
    <style>
        body{
            margin: 0;
            padding: 0;
        }
        img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php
        $image = $_GET['image'];
        echo("<img src='process/process-fetchImage.php?image=$image' alt='Bild'>");
    ?>
</body>
</html>