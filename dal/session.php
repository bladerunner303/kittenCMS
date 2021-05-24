<?php

require_once 'sqlConst.php';
require_once 'data.php';
require_once 'user.php';
require_once '../includes/systemUtil.php';

class Session{

  public static function open($userName, $userRole){
    $id = SystemUtil::getGuid();
    $usersData = User::get($userName);
    if ($usersData == null ){
      throw new Exception('Nem található a user');
    }
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SESSION_START);
    $params = array(
      ':id' => $id,
       ':ip' => SystemUtil::getRequestIp(),
       ':browser_hash' => SystemUtil::getRequestBrowserHash(),
       ':user_name' => $userName,
       ':user_role' => $userRole,
        ':logout_time' => null,
        ':session_data' => null
      );
      $pre->execute($params);
      return $id;
  }

  public static function getSessionData($id){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SESSION_SELECT_BY_ID);
    $params = array (
      ':id' => $id,
      ':ip' => SystemUtil::getRequestIp(),
      ':browser_hash' => SystemUtil::getRequestBrowserHash(),
    );
    $pre->execute($params);
    $currentSession =  $pre->fetchAll(PDO::FETCH_OBJ);
    if (count($currentSession) != 1 ){
      return null;
    }
    else {
      return $currentSession[0];
    }
  }

  public static function isValid($sessionId){
    try {
      $db = Data::getInstance();
      $pre = $db->prepare(SqlConst::SESSION_VALID_COUNT);
      $params = array (
        ':id' => $sessionId,
        ':ip' => SystemUtil::getRequestIp(),
        ':browser_hash' => SystemUtil::getRequestBrowserHash()
      );
      $pre->execute($params);
      $sessionCount = (int)$pre->fetch(PDO::FETCH_OBJ)->cnt;
      if ($sessionCount == 1){
        $pre = $db->prepare(SqlConst::SESSION_UPDATE_LAST_ACTIVITY);
        $params = array (':id' => $sessionId);
        $pre->execute($params);
        return true;
      }
      else {
        return false;
      }
    } catch (Exception $e) {
      error_log($e->getMessage() . $e->getTraceAsString());
      return false;
    }


  }

  public static function kill($sessionId){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SESSION_KILL);
    $params = array (
      ':id' => $sessionId,
      ':ip' => SystemUtil::getRequestIp(),
      ':browser_hash' => SystemUtil::getRequestBrowserHash()
    );
    $pre->execute($params);
  }

  public static function killUserSessions($userName){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::SESSION_KILL_BY_NAME);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->execute($params);
  }

  public static function clearOldSession(){

  }

}

?>
