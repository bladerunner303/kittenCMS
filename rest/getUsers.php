<?php

require_once '../dal/user.php';
require_once  '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }

  $session = Session::getSessionData($sessionId);
  if ($session->user_role != "ADMIN"){
    JsonParser::sendRoleError();
    return;
  }
  $ret =new stdClass();
  $ret->rows = User::getAll();
  $ret->userRole = $session->user_role;
  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
