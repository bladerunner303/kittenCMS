<?php

require_once '../dal/configSetting.php';
require_once '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }

  $target_dir = "../uploads/";
  $target_file = $target_dir . basename($_FILES["file"]["name"]);
  $fileSize = $_FILES["file"]["size"];
  $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

  Logger::info($target_file);
  if (file_exists($target_file)) {
    throw new InvalidArgumentException("A fájl már létezik a szerveren!");
  }

  if ($fileSize > Config::$uploadFileByteLimit) {
    throw new InvalidArgumentException("A fájl mérete nagyobb mint a megengedett!");
  }

  if (!in_array(strtolower($imageFileType), explode(';', Config::$enabledFileTypes))){
    throw new InvalidArgumentException('Nem engedélyezett fájl típus!');
  }

  move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
  JsonParser::sendJson("OK");

}catch (InvalidArgumentException $e){
  JsonParser::sendError(500, $e->getMessage());
}catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}


?>
