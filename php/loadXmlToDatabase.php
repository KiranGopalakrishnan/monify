<?php
  ini_set("display_errors",1);  //TODO: To be removed from the production build
  error_reporting(E_ALL);       //TODO: To be removed from the production build

  require 'classes/class.xmlParser.php';
  require 'classes/class.database.php';

  define("XML_FILE_PATH","../xml/members.xml");

  //Database connection
  $database = new database();
  $dbConnection = $database->connect();
  $response =array();
  $successCounter=0;
  $failCounter=0;
if($dbConnection){

  //Database Connected

  $xmlObject=null;  //Will contain the returned data from loadXml
  $xmlParser = new xmlParser();
  $xmlObject = $xmlParser->loadXml(XML_FILE_PATH);

  $sql="INSERT INTO user_data(id, lastname, firstname, email,
    telephone, dob,address,city, province, postalcode, status)
        VALUES (:id,:lastname,:firstname,:email,:telephone,
          :dob,:address,:city,:province,:postalcode,:status)";

  //Iterating through the object
  foreach ($xmlObject->member as $singleUser){
      $params=array();
      $params[count($params)]=array("name"=>":id","value"=>$singleUser["id"],"type"=>PDO::PARAM_INT);
      $params[count($params)]=array("name"=>":lastname","value"=>ucfirst($singleUser->lastname),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":firstname","value"=>ucfirst($singleUser->firstname),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":email","value"=>strtolower($singleUser->email),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":telephone","value"=>$singleUser->telephone,"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":dob","value"=>$singleUser->dob,"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":address","value"=>ucfirst($singleUser->address),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":city","value"=>ucfirst($singleUser->city),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":province","value"=>strtoupper($singleUser->province),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":postalcode","value"=>strtoupper($singleUser->{"postal-code"}),"type"=>PDO::PARAM_STR);
      $params[count($params)]=array("name"=>":status","value"=>1,"type"=>PDO::PARAM_INT);
      $stmt = $database->runQuery($sql,$params);
      $result = $stmt->execute();
      if($result){
        $successCounter+=1;
      }
      else{
        $failCounter+=1;
      }
  }
  $response["result"]=true;
  $response["successful"]=$successCounter;
  $response["failed"]=$failCounter;
}else{
$response["result"]=false;
$response["error"]="Database Connection Error";
}
echo json_encode($response);
?>
