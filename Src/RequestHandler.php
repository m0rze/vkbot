<?php

class RequestHandler
{
    public function recieveData($message)
    {
        $message = explode(":", $message);
        if ($message[0] == "nsc") {
            $message = intval($message[1]);

            $database = new Database();
            $database->updateDB(
                array(
                    array(
                        "key" => "smartphones",
                        "value" => $message
                    )
                ),
                array(
                    "key" => "ID",
                    "value" => "1"
                )
            );

            VKApi::sendAnswer("Успешно!");
        } else {
            VKApi::sendAnswer("Не успешно. Формат сообщения - nsc:123 , где 123 - это необходимое количество смартфонов");
        }
    }

}