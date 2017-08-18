<?php

//Database wrapper class

class database{

  private $dbConnection;
  private $dbHostname;
  private $dbUsername;
  private $dbPassword;
  private $dbName;
  const DB_CONFIG_FILE = "db_config.ini"; //Path of the db config file
  public function __construct(){
    $dbCredentials = $this->readConfigFile(self::DB_CONFIG_FILE);
    $this->dbHostname=$dbCredentials["hostname"];
    $this->dbName=$dbCredentials["dbName"];
    $this->dbUsername=$dbCredentials["dbUsername"];
    $this->dbPassword=$dbCredentials["dbPassword"];;
  }
  public function connect(){
    try{
      $this->dbConnection = new PDO('mysql:host='.$this->dbHostname.';dbname='.$this->dbName.';charset=utf8', ''.$this->dbUsername.'',''.$this->dbPassword.'', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ));
      return $this->dbConnection;
    }
    catch(PDOException $e) {
      return false;
      //echo $e->getMessage();  //TODO: Remove on production code
    }
  }
  //reads the ini file from the path
  private function readConfigFile($path){
    return parse_ini_file($path);
  }
  public function runQuery($sql=null,$params=null){
    if($sql!=null){
      $stmt = $this->dbConnection->prepare($sql);
      foreach ($params as $singlePair) {
        if(!empty($singlePair["name"])&&!empty($singlePair["type"])){
          $stmt->bindValue($singlePair["name"],$singlePair["value"],$singlePair["type"]);
        }else{
          echo "Parameter Names cannot be empty";
        }
      }
      return $stmt; //return the prepared statement;
    }else{
      return "Error:SQL query parameter cannot be empty";
    }
  }
  public function prepData($name,$value,$type){
    return array("name"=>$name,"value"=>$value,"type"=>$type);
  }
  public function closeConnection(){
    $this->dbConnection=null;
  }
}
?>
