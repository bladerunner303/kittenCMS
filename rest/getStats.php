<?php

require_once '../dal/stat.php';
require_once  '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }
  $ret;
  $statType = !empty($_GET['statType']) ? $_GET['statType'] : null ;
  $dateFrom = !empty($_GET['dateFrom']) ? $_GET['dateFrom'] : null ;
  $dateTo = !empty($_GET['dateTo']) ? $_GET['dateTo'] : null ;

  $ret = Stat::getStat($statType, $dateFrom, $dateTo);
  
  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
