<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build

require 'classes/class.database.php';
require 'classes/class.constraints.php';

$start_limit = $_GET["start"];
$response = array();
$threshold = $_GET["threshold"];

$constraints = new constraints();
$parameterConstraintChecks = $constraints->IntParameterCheck($start_limit)&&$constraints->IntParameterCheck($threshold);

if($parameterConstraintChecks){
  //Database connection
  $database = new database();
  $dbConnection = $database->connect();
  if($dbConnection){

    //Database Connected
    //Getting the total number of results without limit clause
    $sql_total_results = "SELECT count(*) as total FROM user_data WHERE status=:status";
    $params_total_results=array();
    $params_total_results[count($params_total_results)] = $database->prepData(":status",1,PDO::PARAM_INT);
    $stmt = $database->runQuery($sql_total_results,$params_total_results);
    $stmt->execute();
    $total_num_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response["total_num_results"]=isset($total_num_results[0]["total"])?$total_num_results[0]["total"]:0;
    //Get the actual results
    $sql="SELECT id,firstname,lastname FROM user_data WHERE status = :status ORDER BY id DESC LIMIT :start_limit,:threshold"; //Assuming all members include deleted members as well

    $params=array();
    $params[count($params)] = $database->prepData(":start_limit",(int)$start_limit,PDO::PARAM_INT);
    $params[count($params)] = $database->prepData(":threshold",(int)$threshold,PDO::PARAM_INT);
    $params[count($params)] = $database->prepData(":status",1,PDO::PARAM_INT);

    $stmt = $database->runQuery($sql,$params);
    $stmt->execute();
    $resultData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response["result"]=true;
    $response["data"] = $resultData;

    $database->closeConnection();
  }else{
    $response["result"]=false;
    $response["message"]="Database connection error";
  }
}else{
  $response["result"]=false;
  $response["message"]="Invalid arguments";
}

echo json_encode($response);
?>
