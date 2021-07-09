<?php

require_once '../dal/stat.php';
require_once '../dal/configMenu.php';
require_once '../dal/news.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';
require_once '../includes/systemUtil.php';


$page = !empty($_GET['page']) ? $_GET['page'] : null ;
$isMobile = !empty($_GET['mobile']) ? $_GET['mobile'] : 0 ;
$geoInf = new stdClass();
$geoInf->city = !empty($_GET['city']) ? $_GET['city'] : null ;
$geoInf->countryName = !empty($_GET['country']) ? $_GET['country'] : null ;
$geoInf->regionName = !empty($_GET['region']) ? $_GET['region'] : null ;
try {
  if ($page == null){
    $page = ConfigMenu::getDefaultPage();
  }

  $ip = SystemUtil::getRequestIp();
  Stat::addStat(
    $ip,
    $page,
    empty($_SERVER['HTTP_USER_AGENT'])? 'N/A' : $_SERVER['HTTP_USER_AGENT'],
    $geoInf
   );
  $menu = ConfigMenu::getMenuItems($page, 1);
  if (($menu == null) || (count($menu) == 0)){
    throw new InvalidArgumentException("Nem található a keresett oldal!");
  }
  $ret = new stdClass();
  $ret->html = '';
  if (!empty($menu[0]->css)){
      $ret->html = '<style>' . $menu[0]->css . '</style>';
  }
  if (!empty($menu[0]->js)){
      $ret->html = '<script type="text/javascript">' . $menu[0]->js . '</script>';
  }
  $ret->html .= '<div>' . $menu[0]->content . '</div>';
  $ret->modified = $menu[0]->modified;

  if ($menu[0]->menu_type == 'NEWS'){
    $ret->html = News::getHtml($menu[0]->id, 1, 0, 9, $isMobile );
    $ret->html .= file_get_contents("../templates/moreNews.html");
  }

  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
