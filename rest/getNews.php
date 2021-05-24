<?php

require_once '../dal/configMenu.php';
require_once  '../dal/session.php';
require_once '../dal/news.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $page = !empty($_GET['page']) ? $_GET['page'] : null ;
  $first = !empty($_GET['first']) ? $_GET['first'] : 0 ;
  $last = !empty($_GET['last']) ? $_GET['last'] : 90000000 ;
  $html =  !empty($_GET['html']) ? $_GET['html'] : 0 ;
  $isMobile = !empty($_GET['mobile']) ? $_GET['mobile'] : 0 ;
  if ($html == '1'){
      JsonParser::sendJson(News::getHtml($page, 1, $first, $last, $isMobile));
  }
  else {
    $sessionId = $_COOKIE["sessionId"];
    if (!Session::isValid($sessionId)){
      JsonParser::sendSessionExpired();
      return;
    }
    JsonParser::sendJson(News::get($page, 0, $first, $last));
  }

} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
