<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build

require 'classes/class.constraints.php';
require 'classes/class.database.php';

//Getting the GET variables
$id = $_GET["id"];

$response = array();

$constraints = new constraints();

if($constraints->IntParameterCheck($id)){
  //Database connection
  $database = new database();
  $dbConnection = $database->connect();

  if($dbConnection){
    //Database Connected

    $sql="SELECT * FROM user_data WHERE id = :id";

    $params=array();
    $params[count($params)]=$database->prepData(":id",$id,PDO::PARAM_STR);
    $stmt = $database->runQuery($sql,$params);
    $result = $stmt->execute();

    $resultData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response["result"]=$result;
    $response["data"]=$resultData;

  }else{
    $response["result"]=false;
    $response["error"]="Database connection error";
  }
}else{
  $response["result"]=false;
  $response["error"]="Invalid arguments";
}



echo json_encode($response);

?>
