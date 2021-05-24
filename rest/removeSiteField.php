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
  $session = Session::getSessionData($sessionId);

  ConfigMenu::removeMenu($siteId, $session->user_name);
  JsonParser::sendJson("OK");


} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
