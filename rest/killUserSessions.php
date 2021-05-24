<?php


require_once '../dal/configSetting.php';
require_once '../dal/session.php';
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
  $request = json_decode ( file_get_contents ( 'php://input' ) );
  $userName =  $request->userName;
  Session::killUserSessions($userName);
  JsonParser::sendJson($userName);

} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
