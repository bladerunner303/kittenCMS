<?php

require_once '../dal/stat.php';
require_once '../dal/configMenu.php';
require_once '../dal/news.php';
require_once '../includes/jsonParser.php';
require_once '../includes/systemUtil.php';
require_once '../includes/logger.php';

$page = !empty($_GET['page']) ? $_GET['page'] : null ;
$isMobile = !empty($_GET['mobile']) ? $_GET['mobile'] : 0 ;
try {
  if ($page == null){
    $page = ConfigMenu::getDefaultPage();
  }

  $menu = ConfigMenu::getMenuItems($page, 0);
  if (($menu == null) || (count($menu) == 0)){
    throw new InvalidArgumentException("Nem található a keresett oldal!");
  }
  $ret = new stdClass();
  $ret->html = $menu[0]->content;
  $ret->modified = $menu[0]->modified;
  if ($menu[0]->menu_type == 'NEWS'){
    $ret->html = News::getHtml($menu[0]->id, 0, 0, 9, $isMobile );
    $ret->html .= file_get_contents("../templates/moreNews.html");
  }
  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
