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

  $request = json_decode ( file_get_contents ( 'php://input' ) );
  $fileName = $request->fileName;

  if (empty($fileName)){
    throw new InvalidArgumentException('Nem megfelelő bemeneti paraméter!');
  }
  unlink('../uploads/'.  $fileName);



  JsonParser::sendJson("OK");

} catch (InvalidArgumentException $e){
  JsonParser::sendError(500, $e->getMessage());
} catch (\Exception $e) {
  Logger::error($e->getMessage() . $e->getTraceAsString());
  JsonParser::sendError(500, $e->getMessage());
}

?>
