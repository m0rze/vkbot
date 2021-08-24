<?php

class Errors
{

    static public function sendError($errText){
        VKApi::sendAnswer($errText);
        $fd = fopen("./Logs/errors.txt", "a+");
        fwrite($fd, date("H:i:s d.m.Y").": ".$errText."\n");
        fclose($fd);
        die();
    }

    static public function echoError($errText){
        echo $errText;
        $fd = fopen("./Logs/errors.txt", "a+");
        fwrite($fd, date("H:i:s d.m.Y").": ".$errText."\n");
        fclose($fd);
        die();
    }

}