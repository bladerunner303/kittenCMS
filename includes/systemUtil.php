<?php

class SystemUtil {

	public static function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

	public static function getGuid(){
		mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		//$uuid = chr(123)// "{"
		$guid = ''
				.substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12)
				//.chr(125)// "}"
		;
		return $guid;
	}

	public static function getRequestIp(){
		return
		!empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] :
		!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] :$_SERVER['REMOTE_ADDR'];
	}

	public static function getGeoInfoFromIp($ip){
		$xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".$ip);
		$ret = new stdClass();
		foreach ($xml as $key => $value)
		{
			$key = str_replace("geoplugin_", "", $key);
			$ret->{$key} = $value;
		}
		return $ret;
	}

	public static function getRequestBrowserHash(){
		return hash('md5', EMPTY($_SERVER['HTTP_USER_AGENT'])? 'N/A' : $_SERVER['HTTP_USER_AGENT']);
	}

	public static function clearString($dirtyString){
	  $enabledChars = "abcdefghijklmnopqrstzvwyxABCDEFGHIJKLMNOPQRSTZVWYX0123456789";

	  $dirtyString = str_replace('á', 'a',$dirtyString);
	  $dirtyString = str_replace('Á', 'A',$dirtyString);
	  $dirtyString = str_replace('ä', 'a',$dirtyString);
	  $dirtyString = str_replace('Ä', 'A',$dirtyString);
	  $dirtyString = str_replace('é', 'e',$dirtyString);
	  $dirtyString = str_replace('É', 'e',$dirtyString);
	  $dirtyString = str_replace('í', 'i',$dirtyString);
	  $dirtyString = str_replace('Í', 'I',$dirtyString);
	  $dirtyString = str_replace('ó', 'o',$dirtyString);
	  $dirtyString = str_replace('ö', 'o',$dirtyString);
	  $dirtyString = str_replace('ő', 'o',$dirtyString);
	  $dirtyString = str_replace('Ó', 'O',$dirtyString);
	  $dirtyString = str_replace('Ö', 'O',$dirtyString);
	  $dirtyString = str_replace('Ő', 'O',$dirtyString);
	  $dirtyString = str_replace('ú', 'u',$dirtyString);
	  $dirtyString = str_replace('ü', 'u',$dirtyString);
	  $dirtyString = str_replace('ű', 'u',$dirtyString);
	  $dirtyString = str_replace('Ú', 'U',$dirtyString);
	  $dirtyString = str_replace('Ü', 'U',$dirtyString);
	  $dirtyString = str_replace('Ű', 'U',$dirtyString);

	  $ret = "";
	  for ($i=0; $i < strlen($dirtyString); $i++) {
	    if (strpos($enabledChars, $dirtyString[$i]) > -1) {
	        $ret .= $dirtyString[$i];
	    }

	  }
	  return $ret;
	}

}
?>
