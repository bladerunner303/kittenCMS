<?php
set_include_path(dirname(__FILE__)."/../");
require_once 'sqlConst.php';
require_once 'data.php';
require_once 'configSetting.php';

class News{

  public static function get($menuId, $visible, $first, $last){
    $rowCount = $last - $first;
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::NEWS_SELECT);
    $pre->bindParam(':menu_id', $menuId, PDO::PARAM_STR);
    $pre->bindParam(':visible', $visible, PDO::PARAM_INT);
    $pre->execute();
    $result = $pre->fetchAll(PDO::FETCH_OBJ);
    $ret = array();
    foreach ($result as $key => $row) {
      //because mysql limit has a bug
      if (($key >= $first) && ($key <= $last)){
        array_push($ret, $row);
      }
    }
    return $ret;
  }

  public static function getById($newsId){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::NEWS_SELECT_BY_ID);
    $pre->bindParam(':id', $newsId, PDO::PARAM_STR);
    $pre->execute();
    return $pre->fetch(PDO::FETCH_OBJ);
  }

  public static function getHtml($menuId, $visible, $first, $last, $isMobile){
    $newsTemplate = "";
    if ($isMobile){
      $newsTemplate = ConfigSetting::getSettingValue("news-mobile-template");
    }
    if ($newsTemplate == ""){
      $newsTemplate = ConfigSetting::getSettingValue("news-template");
    }

    $html = "";
    $news = News::get($menuId, $visible, $first, $last);
    foreach ($news as $key => $item) {
      if (( $key >= $first) && ($key <= $last)){
        $newsHtml = $newsTemplate;
        $newsHtml = str_replace('<%%title%%>', $item->title, $newsHtml);
        $newsHtml = str_replace('<%%content%%>', $item->content, $newsHtml);
        $html = $html . $newsHtml;
      }
    }
    return $html;
  }

  public static function add($newsObj, $user){

    $id = SystemUtil::getGuid();
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::NEWS_ADD);
    $pre->bindParam(':id', $id, PDO::PARAM_STR);
    $pre->bindParam(':menu_id', $newsObj->siteId, PDO::PARAM_STR);
    $pre->bindParam(':title', $newsObj->title, PDO::PARAM_STR);
    $pre->bindParam(':content', $newsObj->content, PDO::PARAM_STR);
    $pre->bindParam(':visible', $newsObj->visible, PDO::PARAM_INT);
    $pre->bindParam(':highlight', $newsObj->highlight, PDO::PARAM_INT);
    $pre->bindParam(':user', $user, PDO::PARAM_STR);
    $pre->execute();
    }

  public static function modify($newsObj, $user){
    $db = Data::getInstance();

    if (!empty($newsObj->title)){
      $pre = $db->prepare(SqlConst::NEWS_MODIFY_TITLE);
      $pre->bindParam(':id', $newsObj->id, PDO::PARAM_STR);
      $pre->bindParam(':title', $newsObj->title, PDO::PARAM_STR);
      $pre->bindParam(':user', $user, PDO::PARAM_STR);
      $pre->execute();
    }

    if (!empty($newsObj->content)){
      $pre = $db->prepare(SqlConst::NEWS_MODIFY_CONTENT);
      $pre->bindParam(':id', $newsObj->id, PDO::PARAM_STR);
      $pre->bindParam(':content', $newsObj->content, PDO::PARAM_STR);
      $pre->bindParam(':user', $user, PDO::PARAM_STR);
      $pre->execute();
    }

    if (($newsObj->visible === 0) || ($newsObj->visible === 1)) {
      $pre = $db->prepare(SqlConst::NEWS_MODIFY_VISIBLE);
      $pre->bindParam(':id', $newsObj->id, PDO::PARAM_STR);
      $pre->bindParam(':visible', $newsObj->visible, PDO::PARAM_INT);
      $pre->bindParam(':user', $user, PDO::PARAM_STR);
      $pre->execute();
    }

    if (($newsObj->highlight === 0) || ($newsObj->highlight === 1)){
      $pre = $db->prepare(SqlConst::NEWS_MODIFY_HIGHLIGHT);
      $pre->bindParam(':id', $newsObj->id, PDO::PARAM_STR);
      $pre->bindParam(':highlight', $newsObj->highlight, PDO::PARAM_INT);
      $pre->bindParam(':user', $user, PDO::PARAM_STR);
      $pre->execute();
    }

  }

  public static function remove($menuId, $user){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::NEWS_REMOVE);
    $pre->bindParam(':id', $menuId, PDO::PARAM_STR);
    $pre->bindParam(':user_name', $user, PDO::PARAM_STR);
    $pre->execute();
  }
}
?>
