<?php
  ini_set("display_errors",1);
  error_reporting(E_ALL);
  require 'class.xmlParser.php';
  define("XML_FILE_PATH","../xml/members.xml");
  $xmlParser = new xmlParser();
  $xmlParser->loadXml(XML_FILE_PATH);
  $singleUser = $xmlParser->getSingleUser(0);
  echo $singleUser->lastname;
?>
