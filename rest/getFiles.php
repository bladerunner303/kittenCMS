<?php

require_once  '../dal/session.php';
require_once '../includes/jsonParser.php';
require_once '../includes/logger.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
    JsonParser::sendSessionExpired();
    return;
  }

  $ret = array();
  $fileNames = scandir ( '../uploads/' );
  foreach ($fileNames as $fileName) {
    if (($fileName != ".") && ($fileName != "..")){
      $file = new stdClass();
      $file->name = $fileName;

      $fileNameWithPath = '../uploads/' . $fileName;
      $size = fileSize($fileNameWithPath);
      if ($size<1024){
          $file->size = $size . 'byte';
      } else if (($size>=1024)&&($size<1024*1024)){
          $file->size = round(($size / 1024),3) . 'kb';
      } else {
        $file->size = round(($size / (1024 *1024)),3) . 'mb';
      }
      $file->modified = date("Y-m-d H:i:s",filectime($fileNameWithPath));
      array_push($ret, $file);
    }
  }
  JsonParser::sendJson($ret);
} catch (Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

 ?>
