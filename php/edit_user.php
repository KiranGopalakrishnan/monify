<?php
ini_set("display_errors",1);  //TODO: To be removed from the production build
error_reporting(E_ALL);       //TODO: To be removed from the production build

require 'classes/class.database.php';
require 'classes/class.constraints.php';

$constraints = new constraints();
//Getting the POST variables
$id = $_POST["id"];
//Getting the POST database
$firstname = $constraints->escapeVariable($_POST["firstname"]);
$lastname = $constraints->escapeVariable($_POST["lastname"]);
$email = $constraints->escapeVariable($_POST["email"]);
$address = $constraints->escapeVariable($_POST["address"]);
$city = $constraints->escapeVariable($_POST["city"]);
$province = $constraints->escapeVariable($_POST["province"]);
$postalcode = $constraints->escapeVariable($_POST["postalcode"]);
$telephone = $constraints->escapeVariable($_POST["telephone"]);

$dob = date("Y-m-d",strtotime($_POST["dob"]));

$response = array();

$arguments=array();
$arguments[count($arguments)]=array("value"=>$firstname,"max_length"=>30,"min_length"=>1);
$arguments[count($arguments)]=array("value"=>$lastname,"max_length"=>30,"min_length"=>1);
$arguments[count($arguments)]=array("value"=>$email,"max_length"=>50,"min_length"=>1);
$arguments[count($arguments)]=array("value"=>$address,"max_length"=>100,"min_length"=>1);
$arguments[count($arguments)]=array("value"=>$city,"max_length"=>50,"min_length"=>1);
$arguments[count($arguments)]=array("value"=>$province,"max_length"=>2,"min_length"=>2);
$arguments[count($arguments)]=array("value"=>$postalcode,"max_length"=>7,"min_length"=>6);
$arguments[count($arguments)]=array("value"=>$telephone,"max_length"=>10,"min_length"=>10);


$postalcode = $constraints->format_postalcode($postalcode);
$telephone = $constraints->format_number($telephone);

$validated = $constraints->validateArguments($arguments);

if($validated){

  //Database connection
  $database = new database();
  $dbConnection = $database->connect();
  if($dbConnection){
    //Building the sql query
    $sql="UPDATE user_data SET lastname=:lastname, firstname=:firstname, email=:email, telephone=:telephone, dob=:dob,
    address=:address,city=:city, province=:province, postalcode=:postalcode, status=:status WHERE id = :id";

    //Setting the parameters
    $params=array();
    $params[count($params)]=array("name"=>":id","value"=>$id,"type"=>PDO::PARAM_INT);
    $params[count($params)]=array("name"=>":lastname","value"=>ucfirst($lastname),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":firstname","value"=>ucfirst($firstname),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":email","value"=>strtolower($email),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":telephone","value"=>$telephone,"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":dob","value"=>$dob,"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":address","value"=>ucfirst($address),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":city","value"=>ucfirst($city),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":province","value"=>strtoupper($province),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":postalcode","value"=>strtoupper($postalcode),"type"=>PDO::PARAM_STR);
    $params[count($params)]=array("name"=>":status","value"=>1,"type"=>PDO::PARAM_INT);

    $stmt = $database->runQuery($sql,$params);
    $result =$stmt->execute();  //Result of execution - true/false

    $response["result"]=$result;
  }else{
      $response["result"]=false;
      $response["error"]="Database connection error";
  }
}else{
  $response["result"]=false;
  $response["error"]="Arguments validation unsuccessful";
}

function escapeVariable($variable){
  return strip_tags($variable);
}

echo json_encode($response);

?>
