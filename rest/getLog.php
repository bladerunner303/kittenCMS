<?php

require_once '../config/config.php';
require_once  '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }
  $ret =new stdClass();
  $ret->log = file_get_contents(Logger::getLogFileFullPath());
  $ret->userRole = Session::getSessionData($sessionId)->user_role;
  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
