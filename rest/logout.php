<?php

require_once '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try{
  $sessionId = $_COOKIE["sessionId"];
  if (Session::isValid($sessionId)){
    Session::kill($sessionId);
    setcookie("sessionId", "", time() - 3600, "/");
    $success = true;
    JsonParser::sendJson($success);
  }
  else {
    JsonParser::sendSessionExpired();
  }

} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
