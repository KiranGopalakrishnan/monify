<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build

require 'classes/class.database.php';

$start = $_GET["start"];
$threshold=$_GET["threshold"];

//Database connection
$database = new database();
$dbConnection = $database->connect();

$response = array();

if($dbConnection){
  //Database connected
  //Getting the total number of results without limit clause
  $sql_total_results = "SELECT count(*) as total FROM user_data WHERE status=:status";
  $params_total_results=array();
  $params_total_results[count($params_total_results)] = $database->prepData(":status",0,PDO::PARAM_INT);
  $stmt = $database->runQuery($sql_total_results,$params_total_results);
  $stmt->execute();
  $total_num_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $response["total_num_results"]=isset($total_num_results[0]["total"])?$total_num_results[0]["total"]:0;
  //Get the actual results
  $sql="SELECT firstname,lastname,id FROM user_data WHERE status = :status  ORDER BY id DESC LIMIT :start,:threshold";

  $params=array();
  $params[count($params)]=$database->prepData(":threshold",(int)$threshold,PDO::PARAM_INT);
  $params[count($params)]=$database->prepData(":start",(int)$start,PDO::PARAM_INT);
  $params[count($params)]=$database->prepData(":status",0,PDO::PARAM_INT);
  $stmt = $database->runQuery($sql,$params);
  $result = $stmt->execute();
  $resultData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $response["result"]=$result;
  $response["data"] = $resultData;

  $database->closeConnection();
}else{
  $response["result"]=false;
  $response["error"] = "Database connection error";
}
echo json_encode($response);
?>
