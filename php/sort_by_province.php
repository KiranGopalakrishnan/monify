<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build

require 'classes/class.constraints.php';
require 'classes/class.database.php';
//Getting the GET variables

$province = $_GET["province"];
$start_limit = $_GET["start"];
$response = array();
$threshold = $_GET["threshold"];

$constraints = new constraints();
$parameterConstraintChecks = $constraints->IntParameterCheck($start_limit)&&$constraints->IntParameterCheck($threshold)&&strlen($province)>0;
if($parameterConstraintChecks){

  //Database connection
  $database = new database();
  $dbConnection = $database->connect();
  if($dbConnection){
    //Database Connected
    //Getting the total number of results without limit clause
    $sql_total_results = "SELECT count(*) as total FROM user_data WHERE province = :province AND status=:status";
    $params_total_results=array();
    $params_total_results[count($params_total_results)] = $database->prepData(":province",$province,PDO::PARAM_INT);
    $params_total_results[count($params_total_results)] = $database->prepData(":status",1,PDO::PARAM_INT);
    $stmt = $database->runQuery($sql_total_results,$params_total_results);
    $stmt->execute();
    $total_num_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response["total_num_results"]=isset($total_num_results[0]["total"])?$total_num_results[0]["total"]:0;

    //Get the actual results
    $sql="SELECT firstname,lastname,id,province FROM `user_data` WHERE province = :province AND status = :status ORDER BY id DESC LIMIT  :start_limit,:threshold";

    $params=array();
    $params[count($params)]=$database->prepData(":status",1,PDO::PARAM_INT);
    $params[count($params)]=$database->prepData(":province",$province,PDO::PARAM_STR);
    $params[count($params)] = $database->prepData(":start_limit",(int)$start_limit,PDO::PARAM_INT);
    $params[count($params)] = $database->prepData(":threshold",(int)$threshold,PDO::PARAM_INT);

    $stmt = $database->runQuery($sql,$params);
    $result=$stmt->execute();
    $resultData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response["result"]=$result;
    $response["data"] = $resultData;
  }else{
    $response["result"]=false;
    $response["error"] = "Database connection error";
  }
}else{
  $response["result"]=false;
  $response["error"] = "Invalid arguments";
}

echo json_encode($response);
?>
