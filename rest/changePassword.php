<?php

require_once '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }

  $request = json_decode ( file_get_contents ( 'php://input' ) );
  $oldPassword = isset($request->oldPassword)?$request->oldPassword:null;
  $newPassword = isset($request->newPassword)?$request->newPassword:null;
  $userName = isset($request->userName)?$request->userName:null;
  $session = Session::getSessionData($sessionId);

  $isAdminChange = false;
  if ($userName != null) {
    if ($session->user_role != "ADMIN"){
      JsonParser::sendRoleError();
      return;
    }
    $isAdminChange = true;
  }
  else {
    $userName = $session->user_name;
  }

  User::changePassword($oldPassword, $newPassword,$userName, $isAdminChange, $session->user_name );
  JsonParser::sendJson("OK");

} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
