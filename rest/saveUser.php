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
  $userName = $request->userName;
  $userRole = $request->userRole;
  $status = $request->status;

  User::save($userName, $userRole, $status, $session->user_name);

  JsonParser::sendJson("OK");

} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
