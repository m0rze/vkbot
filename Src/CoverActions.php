<?php

use Dompdf\Dompdf;

class CoverActions
{
    private $html;
    private $headScrPath;
    private $getCommentsCount;
    private $smartphonesCount;
    private $database;

    public function __construct($headScrPath)
    {
        if(file_exists($this->headScrPath."/Images/cover.png")){
            @unlink($this->headScrPath."/Images/cover.png");
        }
        $this->headScrPath = $headScrPath;
        $this->database = new Database();
        // Получение цданных по количествам каментов и смартфонов
        $this->getData();
        // Получение HTML кода обложки и замена макросов на значения
        $this->getHTML();
        // Преобразование HTML в PDF
        $pdf = $this->convertToPDF();
        // Преобразование PDF в картинку
        $this->convertToImg($pdf);
    }

    private function getData()
    {
        $this->getCommentsCount = VKApi::getCommentsCount();
        if($this->getCommentsCount === false){
            Errors::echoError("Не смог считать количество комментариев");
        }
        $this->database->updateDB(
            array(
                array(
                    "key" => "comments",
                    "value" => $this->getCommentsCount
                )
            ),
            array(
                "key" => "ID",
                "value" => "1"
            )
        );
        $this->smartphonesCount = $this->database->readDB(Config::$table["name"], array("comments", "smartphones"), array("ID" => 1));
        if(!empty($this->smartphonesCount[0]) && !empty($this->smartphonesCount[0]["smartphones"])){
            $this->smartphonesCount = $this->smartphonesCount[0]["smartphones"];
        } else {
            Errors::echoError("Нет значения количества смартфонов");
        }
    }

    private function getHTML()
    {
        $this->html = file_get_contents($this->headScrPath."/HTML/index.html");
        $this->html = str_ireplace("[FULLPATH]", $this->headScrPath."/HTML/", $this->html);
        $this->html = str_ireplace("[COMMENTSCOUNT]", $this->getCommentsCount, $this->html);
        $this->html = str_ireplace("[SMARTPHONESCOUNT]", $this->smartphonesCount, $this->html);
    }

    private function convertToPDF()
    {
        $dompdf = new Dompdf(array('enable_remote' => true));
        $dompdf->getOptions()->setChroot($this->headScrPath."/HTML");
        $dompdf->loadHtml($this->html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $output = $dompdf->output();
        if(!empty($output)) {
            return $output;
        } else {
            Errors::echoError("Неудачная конвертация в PDF");
            return false;
        }
    }

    private function convertToImg($pdf)
    {
        $imagick = new Imagick();
        $imagick->readImageBlob($pdf);
        $imagick->writeImages($this->headScrPath."/Images/cover.png", false);
    }

    public function checkImage()
    {
        if(file_exists($this->headScrPath."/Images/cover.png") && filesize($this->headScrPath."/Images/cover.png") > 200000 ){
            return true;
        }
        return false;
    }

}