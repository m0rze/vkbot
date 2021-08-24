<?php

class Config
{
    // =========================================================================================================================
    // VK Settings
    static public $apiToken = "";                 // API токен сообщества
    static public $confirmationToken = "";        // токен для подтверждения Callback
    static public $secretKey = "";                // secret, заданный в Callback
    static public $apiVer = "5.131";              // версия API
    static public $allowedUserIds = array(        // список ID юзеров, которые могут отправлять информацию о количестве смартфонов
        "123222212",
        "313523423"
    );
    static public $groupID = "";                  // ID сообщества
    static public $postID = "1";                  // ID записи в сообществе, у которой отслеживается количество каментов
    static public $serviceKey = "";               // Сервисный ключ, берется из настроек приложения, которое надо создать


    // Local MySQL settings
    static public $dbServer = "localhost";        // Сервер MySQL
    static public $dbName = "";                   // Имя базы
    static public $dbUsername = "";               // юзер MySQL
    static public $dbPassword = "";               // пароль юзера MySQL

    // =========================================================================================================================




    // Do not touch
    static public $table = array(
        "name" =>"vkbot",
        "fields" => array(
            "comments" => "VARCHAR( 20 )",
            "smartphones" => "VARCHAR( 50 )"
        )
    );
    static public $getPass = "dfghrea5rt";

}