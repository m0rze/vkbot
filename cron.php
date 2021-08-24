<?php

ini_set("display_errors", "On");

require_once "Config.php";
require_once "Src/Database.php";
require_once "Src/VKApi.php";
require_once "Src/Errors.php";
require_once "Src/CoverActions.php";
require_once 'Libs/dompdf/autoload.inc.php';


$headScrPath = dirname(__FILE__)."/HeadSrc";
$coverActions = new CoverActions($headScrPath);
if($coverActions->checkImage()){
    $uploadURL = VKApi::getUploadUrl();
    if(!empty($uploadURL)){
        $uploadResult = VKApi::sendImage($uploadURL, $headScrPath);
        if(!empty($uploadResult)){
            VKApi::saveCover($uploadResult->hash, $uploadResult->photo);
        }
    }
}