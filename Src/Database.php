<?php

class Database
{
    public $pdo;
    private $tableName;

    public function __construct($install = "")
    {
        try{
            $options = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            );
            $this->pdo = new PDO("mysql:host=".Config::$dbServer.";dbname=".Config::$dbName, Config::$dbUsername, Config::$dbPassword, $options);
        }catch(PDOException $e){
            if(empty($install)) {
                Errors::sendError("Can't connect to MySQL");
            } else {
                Errors::echoError("Can't connect to MySQL");
            }
        }
        $this->tableName = Config::$table["name"];
        if(!empty($install)) {
            $this->configDB();
            if($this->checkTables(Config::$table)){
                $this->insertNewLines(array(
                    "comments" => "",
                    "smartphones" => ""
                ));
                echo "Таблицы созданы успешно, установка завершена";
                die();
            }
        }
    }
    private function configDB(){
            if(!$this->checkTables(Config::$table)){
                $this->createTable(Config::$table);
            } else {
                echo "Таблицы уже были созданы ранее. Установка не требуется.";
                die();
            }
    }

    public function checkTables($table){
        if (!$this->tableExists($this->pdo, $table["name"])) {
            return false;
        } else {
            return true;
        }
    }

    public function createTable($table){
        $sql = "";
        $fields = $table["fields"];
        foreach($fields as $k=>$oneField){
            $sql .= $k . " " . $oneField . " NULL DEFAULT NULL,";
        }
        $sql = trim($sql, ",");

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "CREATE table `" . $table["name"] . "`(ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY," . $sql . ");";
        $this->pdo->exec($sql);
    }

    private function tableExists($pdo, $table)
    {

        try {
            $result = $pdo->query("SELECT 1 FROM `" . $table . "` LIMIT 1");
        } catch (Exception $e) {
            return FALSE;
        }
        return $result !== FALSE;
    }

    public function updateDB($needData, $condition = "")
    {
        $setLine = "";
        $setExecArr = array();
        foreach ($needData as $k => $oneData) {
            $setLine .= "`" . $oneData["key"] . "` = ?, ";
            $setExecArr[] = $oneData["value"];
        }
        $setLine = trim($setLine, ", ");
        if (empty($condition)) {
            $sql = "UPDATE `" . $this->tableName . "` 
                SET " . $setLine;
            $result = $this->pdo->query($sql);
        } else {
            $sql = "UPDATE `" . $this->tableName . "` 
                SET " . $setLine . " WHERE `" . $condition["key"] . "` = ?";
            $setExecArr[] = $condition["value"];
            $result = $this->pdo->prepare($sql);
            $result->execute($setExecArr);
        }

        return true;
    }

    public function insertNewLines($data)
    {
        $keys = "";
        $preparedValues = "";
        $values = array();
        foreach ($data as $key => $value) {
            $randValue = $this->randString(5, "no");
            $keys .= "`".$key."`, ";
            $preparedValues .= ":".$randValue.", ";
            $values[$randValue] = $value;
        }
        $keys = trim($keys, ", ");
        $preparedValues = trim($preparedValues, ", ");
        $sql = "INSERT INTO `".$this->tableName."` (".$keys.") VALUES (".$preparedValues.")";
        $statement = $this->pdo->prepare($sql);
        foreach ($values as $k => $value) {
            $statement->bindValue(":" . $k, $value);
        }
        $inserted = $statement->execute();
        if (!$inserted) {
            Errors::sendError("Не смог вставить новые данные в таблицу");
        }
    }

    public function readDB($tableName, $needKeys, $conditions = "", $condString = "", $limit = "")
    {

        foreach ($needKeys as $k => $oneKey) {
            $needKeys[$k] = "`" . $oneKey . "`";
        }
        $keys = implode(", ", $needKeys);
        if (empty($conditions)) {
            $sql = "SELECT " . $keys . " 
                FROM `" . $tableName . "`";
            if (!empty($condString)) {
                $sql .= " WHERE " . $condString;
            }
            if (!empty($limit)) {
                $sql .= " LIMIT " . $limit;
            }
            $result = $this->pdo->query($sql);
        } else {
            $bindValues = array();
            $where = " WHERE ";
            foreach ($conditions as $k => $oneCondition) {
                if (stripos("qqq" . $oneCondition, "NULL")) {
                    $where .= "`" . $k . "` " . $oneCondition . " AND ";
                } else {
                    $randValue = $this->randString(5, "no");
                    $bindValues[$randValue] = $oneCondition;
                    $where .= "`" . $k . "` = :" . $randValue . " AND ";
                }
            }
            $where = trim($where, " AND ");
            $sql = "SELECT " . $keys . " 
                FROM `" . $tableName . "`" . $where;
            if (!empty($limit)) {
                $sql .= " LIMIT " . $limit;
            }
            $result = $this->pdo->prepare($sql);
            foreach ($bindValues as $k => $oneBindValue) {
                $result->bindValue(":" . $k, $oneBindValue);
            }
            $result->execute();
        }

        $returnResult = array();
        if ($result != false) {
            $rows = $result->fetchAll();
            foreach ($rows as $k => $oneRow) {
                foreach ($needKeys as $oneKey) {
                    $oneKey = str_ireplace("`", "", $oneKey);
                    $returnResult[$k][$oneKey] = $oneRow[$oneKey];
                }
            }
        }
        return $returnResult;
    }

    private function randString($length, $upperCase = "yes")
    {
        $str = "";
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPRSTUVWXYZ";
        if ($upperCase == "no") {
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        }

        $size = strlen($chars);
        $numbers = range(0, $size - 1);
        for ($i = 0; $i < $length; $i++) {
            $randNum = $this->shuffleArr($numbers);
            $randNum = $randNum[0];
            $str .= $chars[$randNum];
        }
        return $str;
    }

    private function shuffleArr($arr)
    {
        srand((float)microtime() * 1000000);
        shuffle($arr);
        return $arr;
    }
}