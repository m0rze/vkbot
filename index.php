<?php

require_once "Config.php";
require_once "Src/Database.php";
require_once "Src/VKApi.php";
require_once "Src/Errors.php";
require_once "Src/RequestHandler.php";

ini_set('log_errors', 'On');
ini_set('error_log', './php_errors.log');
ini_set("display_errors", "Off");

if(!empty($_GET["install"]) && $_GET["install"] == Config::$getPass){
    new Database("yes");
    die();
}

$peerID = "";
$request = json_decode(file_get_contents('php://input'));
if(!empty($request->type)){
    if($request->secret !== Config::$secretKey){
        die();
    }
    switch ($request->type) {
        case 'confirmation':
            echo Config::$confirmationToken;
            break;
        case "message_new":
            echo 'ok';
            if(!empty($request->object->message->text) && !empty($request->object->message->text)) {
                $peerID = $request->object->message->peer_id;
                if(in_array($peerID, Config::$allowedUserIds)) {
                    $result = new RequestHandler();
                    $result->recieveData($request->object->message->text);
                }
            }
            break;
    }

}