<?php
set_include_path(dirname(__FILE__)."/../");
require_once 'includes/systemUtil.php';
require_once 'sqlConst.php';
require_once 'data.php';

class ConfigMenu{


  public static function getDefaultPage(){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_SELECT_DEFAULT);
    $pre->execute();
    return $pre->fetch(PDO::FETCH_OBJ)->page;
  }

  public static function getAll(){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_SELECT_ALL);
    $pre->execute();
    return $pre->fetchAll(PDO::FETCH_OBJ);
  }

  public static function getMenuList(){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_SELECT_VISIBLE);
    $pre->execute();
        return $pre->fetchAll(PDO::FETCH_OBJ);
  }

  public static function getMenuItems($id, $visible){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_SELECT_BY_ID);
    $pre->bindParam(':id', $id, PDO::PARAM_STR);
    $pre->bindParam(':visible', $visible, PDO::PARAM_INT);
    $pre->execute();
    return $pre->fetchAll(PDO::FETCH_OBJ);
  }

  public static function saveField($siteId, $field, $fieldValue, $user){
    $db = Data::getInstance();


    $sql = SqlConst::MENU_UPDATE_TEMPLATE;
    if ($siteId == null){
      $sql = SqlConst::MENU_INSERT;
      $siteId = self::generateId($fieldValue);
    }
    switch ($field) {
      case 'name':
        $sql = str_replace('<%%field_name%%>', 'name', $sql);
        break;
      case 'menu_type':
        $sql = str_replace('<%%field_name%%>', 'menu_type', $sql);
        break;
      case 'tooltip':
        $sql = str_replace('<%%field_name%%>', 'tooltip', $sql);
        break;
      case 'content':
        $sql = str_replace('<%%field_name%%>', 'enabled_editor=1, content', $sql);
        break;
      case 'content-no-editor':
        $sql = str_replace('<%%field_name%%>', 'enabled_editor=0, content', $sql);
        break;
      case 'visible':
        $sql = str_replace('<%%field_name%%>', 'visible', $sql);
        break;
      case 'order_field':
        $sql = str_replace('<%%field_name%%>', 'order_field', $sql);
        break;
      case 'default_page':
        $pre = $db->prepare(SqlConst::MENU_REMOVE_DEFAULT);
        $pre->execute();
        $sql = str_replace('<%%field_name%%>', 'default_page', $sql);
        break;
      case 'css':
        $sql = str_replace('<%%field_name%%>', 'css', $sql);
        break;
      case 'js':
        $sql = str_replace('<%%field_name%%>', 'js', $sql);
        break;
      default:
        throw new InvalidArgumentException('Nem létező mező');
    }
    $pre = $db->prepare($sql);
    $pre->bindParam(':field_value', $fieldValue, PDO::PARAM_STR);
    $pre->bindParam(':modifier', $user, PDO::PARAM_STR);
    $pre->bindParam(':id', $siteId, PDO::PARAM_STR);
    $pre->execute();

  }

  private static function generateId($menuName){

    $cleanMenuName = strtolower(SystemUtil::clearString($menuName));
    $originalCleanMenuName = $cleanMenuName;

    $db = Data::getInstance();
    $cnt = 1;
    $i = 0;

    do {
      if ($i > 0){
        $cleanMenuName = $originalCleanMenuName . '_' . $i;
      }
      $pre = $db->prepare(SqlConst::MENU_COUNT);
      $pre->bindParam(':id', $cleanMenuName, PDO::PARAM_STR);
      $pre->execute();
      $cnt = (int)$pre->fetch(PDO::FETCH_OBJ)->cnt;
      $i++;
    } while ($cnt > 0);

    return $cleanMenuName;
  }

  public static function updateMenu($menuObj){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_UPDATE);
    $pre->bindParam(':name', $settingKey, PDO::PARAM_STR);
    $pre->bindParam(':tooltip', $settingValue, PDO::PARAM_STR);
    $pre->bindParam(':content', $userName, PDO::PARAM_STR);
    $pre->bindParam(':visible', $userName, PDO::PARAM_INT);
    $pre->bindParam(':order_field', $userName, PDO::PARAM_INT);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->bindParam(':id', $userName, PDO::PARAM_STR);
    $pre->execute();
  }

  public static function removeMenu($siteId, $modifier){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::MENU_REMOVE);
    $pre->bindParam(':id', $siteId, PDO::PARAM_STR);
    $pre->bindParam(':deleted_by', $modifier, PDO::PARAM_STR);
    $pre->execute();
  }
}
?>
