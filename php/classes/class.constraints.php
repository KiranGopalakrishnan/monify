<?php
/*
*Contains validation methodes
*/
class constraints{
  function __construct(){

  }
  function validateArguments($arguments){
    $response = false;
    foreach ($arguments as $singlePair) {
      $value = $singlePair["value"];
      $max_length = $singlePair["max_length"];
      $min_length = $singlePair["min_length"];
      if(!empty($value)&&strlen($value)<=$max_length&&strlen($value)>=$min_length){
        $response = true;
      }else{
        break;
      }
    }
    return $response;
  }
  function format_number($phone) {
    if(strlen($phone)==10){
      $numbers_only = preg_replace("/[^\d]/", "", $phone);
      return preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $numbers_only);
    }else{
      return $phone;
    }
  }
  function format_postalcode($postalcode){
    return strlen($postalcode)==6?chunk_split($postalcode, 3, ' '):$postalcode;
  }
  function escapeVariable($variable){
    return strip_tags($variable);
  }

  //function to check the parameter meets constraints or not
  function IntParameterCheck($value){
    return ($value!=""||$value!=null?true:false)&&(is_numeric($value)&&$value>=0);
  }
}
?>
