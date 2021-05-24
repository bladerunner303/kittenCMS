<?php

require_once '../config/config.php';

class Logger {
	public static function error($message){
		self::writeLog("[ERROR] " . $message);
	}
	public static function info($message){
		self::writeLog("[INFO] " . $message);
	}
	public static function warning($message){
		self::writeLog("[WARNING] " . $message);
	}

	private static function writeLog($message){

		$fp = fopen ( self::getLogFileFullPath(), "a" );
		fputs ( $fp, date ( 'Y-m-d H:i:s' ) . $message . "\r\n" );
		fclose ( $fp );
	}

  public static function getLogFileFullPath(){
    return Config::$logFilePath . "/" . Config::$logFileName . ".log";
  }

}
?>
