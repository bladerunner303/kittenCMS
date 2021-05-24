<?php
set_include_path(dirname(__FILE__)."/../");
require_once 'config/config.php';
class Data extends PDO
{

	private static $dataInstance = NULL;

	public static function getInstance (){
		if (self::$dataInstance == NULL){
      $dbServer = Config::$dbServer;
      $dbName = Config::$dbName;
      $dbUser = Config::$dbUser;
      $dbPwd = Config::$dbPwd;
			self::$dataInstance = new Data('mysql:host=' . $dbServer . ';dbname=' . $dbName , $dbUser, $dbPwd);
			self::$dataInstance->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
			self::$dataInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			self::$dataInstance->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
      self::$dataInstance->exec("set names utf8");
			self::$dataInstance->exec('SET SQL_SAFE_UPDATES=0');

		}
		return self::$dataInstance;
	}

	// Magic method clone is empty to prevent duplication of connection
	private function __clone() {
		throw new Exception("Nem clonozható a Singleton objektum!");
	}

}
