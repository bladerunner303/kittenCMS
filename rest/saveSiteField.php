<?php


require_once '../dal/configMenu.php';
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
  $siteId = $request->siteId;
  $field = $request->field;
  $fieldValue = $request->fieldValue;
  $session = Session::getSessionData($sessionId);

  ConfigMenu::saveField($siteId, $field, $fieldValue, $session->user_name);
  if ($siteId == null){
    JsonParser::sendJson("INSERT");
  }
  else {
    JsonParser::sendJson("UPDATE");
  }


} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
