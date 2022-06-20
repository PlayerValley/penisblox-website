<?php
if(!empty($_GET['id']))
{
    $id = $_GET['id'];
    if (file_exists("s/" . $id)) { // regular asset
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"". hash('sha1', $id) ."\"");
        readfile("s/" . $id);
    } else { // regular asset that isn't in penisblox's asset folder, also necessary magic is used here
        // put the content of the file in a variable
        $data = file_get_contents("https://assetdelivery.roblox.com/v1/assetId/" . $id); 
        // JSON decode
        $obj = json_decode($data); 
        // redirect to the location of the file
        header('Location: ' . $obj->location);
    }
}
elseif(!empty($_GET['id']) && !empty($_GET['version']))
{ // version asset that certainly isn't in penisblox's asset folder, necessary magic is still used here
    // put the content of the file in a variable
    $data = file_get_contents("https://assetdelivery.roblox.com/v1/assetId/" . $_GET['id'] . "/version/" . $_GET['version']);
    // JSON decode
    $obj = json_decode($data); 
    // redirect to the location of the file
    header('Location: ' . $obj->location);
}
exit();