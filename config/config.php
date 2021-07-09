<?php

class Config{

  public static $dbServer = '127.0.0.1';
  public static $dbName = 'sashalom';
  public static $dbUser = 'em';
  public static $dbPwd = 'em';

  public static $baseUrl = 'http://myapps/sashalom';

  public static $loginTrial = 5; // in count
  public static $forrbidenTime = 30; // in minute
  public static $passwordSalt = "c868bdf5-9ae1-4ef0-a685-dcfee2aca4c0";

  public static $logFileName = "error"; // without extension
  public static $logFilePath = "../log";

  public static $forrbiddenPasswords =
  "password;123456;1234;12345;iloveyou;jelszó;password123;pass123;abc123;qwerty;qwertz;"; //weak passwords separated by semicolons

  public static $uploadFileByteLimit = 1024*1024*5; //byte-ban, a példa 5mb
  public static $enabledFileTypes = "jpg;jpeg;png;gif;pdf;doc;docx;xls;xlsx;cvs;js;zip;rar;7zip;ppt;pptx;ods;rtf;css;txt;";

  public static $geoLocationtype = "kwelo"; // possible value: none, kwelo, geoplugin-http, geoplugin-https
//  public static $geoLocationtype = "geoplugin-http";

  public static $urlKwelo = "https://api.kwelo.com/v1/network/ip-address/location/{{ip}}?format=json";
  public static $urlGeoPluginHttp = "http://geoplugin.net/json.gp";

}
?>
