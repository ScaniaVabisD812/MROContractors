<?php
    $requestedImage = $_GET['image'];

    require_once '../../../httpd.private/config.php';

    $path = "../../../httpd.private/uploads/$requestedImage";

    // Get the file extension
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    // Set the appropriate content type
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $contentType = 'image/jpeg';
            break;
        case 'png':
            $contentType = 'image/png';
            break;
        case 'gif':
            $contentType = 'image/gif';
            break;
        default:
            $contentType = 'application/octet-stream';
            break;
    }

    if(file_exists($path))
    {
        header('Content-Type: ' . $contentType);
		readfile($path);
    }
    else
    {
        header('Content-Type: image/png');
        readfile("../../httpd.private/uploads/BildSaknas.png");
    }
?>