<?php

require_once '../includes/systemUtil.php';
require_once '../config/config.php';
require_once 'sqlConst.php';
require_once 'data.php';

class User{

  private static function encodePassword($password){
		return hash('sha256', $password . '#' . Config::$passwordSalt);
	}

  public static function get($userName){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::USER_BY_NAME);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->execute();
    try {
      return $pre->fetch(PDO::FETCH_OBJ);
    } catch (\Exception $e) {
      return null;
    }
  }

  public static function getAll(){
    $db = Data::getInstance();
    $pre = $db->prepare(SqlConst::USERS);
    $pre->execute();
    return $pre->fetchAll(PDO::FETCH_OBJ);

  }

  private static function isForbidden($userName){

    $loginTrial = Config::$loginTrial;
    if ($loginTrial<1){
      return false;
    }

		$db = Data::getInstance();
		$pre = $db->prepare(Sqlconst::USER_BAD_LOGIN_COUNT);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->bindParam(':min', Config::$forrbidenTime, PDO::PARAM_INT);

		$pre->execute();
		$currentAttemptCount = (int)$pre->fetch(PDO::FETCH_OBJ)->cnt;


		return ($loginTrial <= $currentAttemptCount);
	}

  public static function login($userName, $userPassword){

  $ret = new stdClass();
  $user = self::get($userName);

  if ($user == null){
    $ret->isGood = false;
    $ret->error = 'Hibás felhasználó vagy jelszó!';
    $ret->userName = $userName;
    $ret->userRole = null;
  }
  elseif (self::isForbidden($user->user_name)){
    $ret->isGood = false;
    $ret->error = 'A felhasználó átmenetileg kitiltott! Próbálkozz később';
    $ret->userName = $userName;
    $ret->userRole = null;
  }
  elseif ((mb_strlen($userPassword) > 3)
      && ($user->user_pwd == self::encodePassword( $userPassword))
      && ($user->status == 'AKTIV'))  {

    $ret->isGood = true;
    $ret->error = '';
    $ret->userName = $userName;
    $ret->userRole = $user->role;
    self::setLastLogin($userName);
    self::clearBadLogins($userName);
  }
  else {
    $ret->isGood = false;
    $ret->error = 'Hibás felhasználó vagy jelszó!';
  //  $ret->error = self::encodePassword( $userPassword);
    $ret->userName = $userName;
    $ret->userRole = null;
    self::addBadLogin($userName);
  }

  return $ret;

}

private static function setLastLogin($userName){
  $db = Data::getInstance();
  $pre = $db->prepare(Sqlconst::USER_UPDATE_LAST_LOGIN);
  $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);

  $pre->execute();

}

private static function clearBadLogins($userName){
  $db = Data::getInstance();
  $pre = $db->prepare(Sqlconst::USER_CLEAR_BAD_LOGINS);
  $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);

  $pre->execute();

}


private static function addBadLogin($userName){
  $db = Data::getInstance();
  $pre = $db->prepare(Sqlconst::USER_ADD_BAD_LOGIN);
  $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);

  $pre->execute();

}

public static function logout($userName){

  $user = self::get($userName);
  self::set($user, 'LOGOUT');

}

public static function changePassword($oldPassword, $newPassword, $userName, $isAdminChange, $modifier){

  $newPassword = trim($newPassword);
  $minPasswordLength = 4;
  if (mb_strlen($newPassword) < $minPasswordLength) {
    throw new InvalidArgumentException("Az új jelszónak minimum $minPasswordLength karakter hosszúnak kell lennie!");
  }

  if (strtolower($newPassword) == strtolower($userName)){
    throw new InvalidArgumentException('Az új jelszó nem egyezhet meg a felhasználó névvel');
  }

  if (in_array(strtolower($newPassword), explode(';', Config::$forrbiddenPasswords))){
    throw new InvalidArgumentException('Az új jelszó túl gyakran használt, emiatt könnyen törhető. Kérlek válassz másikat!');
  }

  if (!$isAdminChange){
    $user = self::get($userName);
    if  ($user->user_pwd != self::encodePassword( $oldPassword)){
      throw new InvalidArgumentException('Nem megfelelő a régi jelszó!');
    }
  }
  else {
    if ($userName == $modifier){
      throw new InvalidArgumentException('Saját jelszót ezen a felületen nem módosíthat');
    }
  }

  $db = Data::getInstance();
  $pre = $db->prepare(Sqlconst::USER_SET_PWD);
  $newPassword = self::encodePassword($newPassword);
  $pre->bindParam(':password', $newPassword, PDO::PARAM_STR);
  $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);

  $pre->execute();

}

public static function save($userName, $userRole, $status, $modifier){

  $enabledRoles = ['ADMIN', 'EDITOR'];
  $enabledStatuses = ['AKTIV', 'INAKTIV'];

  if (!in_array($userRole, $enabledRoles)){
    throw new UnexpectedValueException('Nem engedélyezett jogosultság csoport!');
  }

  if (!in_array($status, $enabledStatuses)){
    throw new UnexpectedValueException('Nem engedélyezett státusz!');
  }

  $db = Data::getInstance();

  if (self::get($userName) == null){
  //  $cleanUserName = self::clearUserName($userName);
    $cleanUserName = SystemUtil::clearString($userName);

    $pre = $db->prepare(Sqlconst::USER_ADD);
    $pre->bindParam(':user_name', $cleanUserName, PDO::PARAM_STR);
    $pre->bindParam(':user_pwd', self::encodePassword($userName), PDO::PARAM_STR);
    $pre->bindParam(':role', $userRole, PDO::PARAM_STR);
    $pre->bindParam(':modifier', $modifier, PDO::PARAM_STR);
    $pre->execute();

  }
  else {
    if (($userRole == 'EDITOR')
    || (($userRole == 'ADMIN') && ($status == 'INAKTIV'))){
      $pre = $db->prepare(Sqlconst::USER_ADMIN_COUNT);
      $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
  		$pre->execute();
  		$adminUserCnt = (int)$pre->fetch(PDO::FETCH_OBJ)->cnt;

      if ($adminUserCnt < 1){
        throw new UnexpectedValueException('Nem módosítható a user, mert nem marad Admin felhasználó!');
      }
    }

    $pre = $db->prepare(Sqlconst::USER_UPDATE);
    $pre->bindParam(':user_name', $userName, PDO::PARAM_STR);
    $pre->bindParam(':role', $userRole, PDO::PARAM_STR);
    $pre->bindParam(':status', $status, PDO::PARAM_STR);
    $pre->bindParam(':modifier', $modifier, PDO::PARAM_STR);
    $pre->execute();

  }

}

}
?>
