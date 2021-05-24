<?php


require_once '../dal/news.php';
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
  $session = Session::getSessionData($sessionId);
  if ($request->id == null){
    News::add($request, $session->user_name);
    JsonParser::sendJson("OK");
  }
  else {
    News::modify($request, $session->user_name);
    $news = News::getById($request->id);
    JsonParser::sendJson($news->modified . ' (' . $news->modifier . ')');
  }


} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
