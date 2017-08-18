<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build
require 'classes/class.constraints.php';
require 'classes/class.database.php';
$response =array();
$result = false;
//Getting the GET variables
$id = trim($_GET["id"]);

$constraints=new constraints();
if($constraints->IntParameterCheck($id)){

  //Database connection
  $database = new database();
  $dbConnection = $database->connect();
  if($dbConnection){
    //Database Connected
    $sql="UPDATE user_data SET status=:status WHERE id = :id";
    $params=array();
    $params[count($params)]=$database->prepData(":id",$id,PDO::PARAM_STR);
    $params[count($params)]=$database->prepData(":status",0,PDO::PARAM_INT);
    $stmt = $database->runQuery($sql,$params);
    $result = $stmt->execute();

    $response["result"]=$result;
    $response["id"]=$id;

  }else{

    $response["result"]=false;
    $response["error"] = "Database connection error";
  }
}else{

  $response["result"]=false;
  $response["error"] = "ID was not provided";
}


echo json_encode($response);

?>
