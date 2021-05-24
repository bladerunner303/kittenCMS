<?php

class Config{

  public static $dbServer = '127.0.0.1'; //database server ip address
  public static $dbName = 'my-mariadb-schema'; //mariadb or mysql database schema name
  public static $dbUser = 'my-mariadb-user'; //mariadb or mysql database user name
  public static $dbPwd = 'my-mariadb-cleantext-password'; //mariadb or mysql database cleantext password

  public static $baseUrl = 'http://mySampleDomain.com'; //my site domain

  public static $loginTrial = 5; // in count
  public static $forrbidenTime = 30; // in minute
  public static $passwordSalt = "c868bdf5-9ae1-4ef0-a685-dcfee2aca4c0"; //password salt 

  public static $logFileName = "error"; // without extension
  public static $logFilePath = "../log"; // log file path

  public static $forrbiddenPasswords =
  "password;123456;1234;12345;iloveyou;jelszó;password123;pass123;abc123;qwerty;qwertz;"; //weak passwords separated by semicolons 

  public static $uploadFileByteLimit = 1024*1024*5; // in byte. this sample equal 5mb
  public static $enabledFileTypes = "jpg;jpeg;png;gif;pdf;doc;docx;xls;xlsx;cvs;js;zip;rar;7zip;ppt;pptx;ods;rtf;css;txt;"; //file extensions separated by semicolons

}
?>
