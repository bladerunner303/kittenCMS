<?php

require_once 'sqlConst.php';
require_once 'data.php';

class ConfigSetting{

  public static function getSettingValue($settingKey){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SETTING_SELECT_BY_KEY);
    $pre->bindParam(':key', $settingKey, PDO::PARAM_STR);
    $pre->execute();
    try {
      return $pre->fetch()[0];
    } catch (\Exception $e) {
      return "";
    }
  }

  public static function getAllSettings(){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SETTING_SELECT_ALL);
    $pre->execute();
    return $pre->fetchAll(PDO::FETCH_OBJ);
  }

  public static function updateSetting($settingKey, $settingValue, $userName){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SETTING_UPDATE);
    $pre->bindParam(':setting_key', $settingKey, PDO::PARAM_STR);
    $pre->bindParam(':setting_value', $settingValue, PDO::PARAM_STR);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->execute();
  }

}
?>
