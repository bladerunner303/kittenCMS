<?php

require_once 'sqlConst.php';
require_once 'data.php';

class Stat{


  public static function addStat($ip, $pageId, $userAgent, $geoInf){
    try {
      $db = Data::getInstance();
      $pre = $db->prepare(SqlConst::STAT_ADD);

      $ip = md5($ip . 'd4ecd1d0-c8e0-435f-9ebd-7b6bb2f73725');
      if (empty($geoInf->countryName)){
        $geoInf->countryName = 'N/A';
      }
      if (empty($geoInf->city)){
        $geoInf->city = 'N/A';
      }
      if (empty($geoInf->regionName)){
        $geoInf->regionName = 'N/A';
      }

      $pre->bindParam(':ip_hash', $ip, PDO::PARAM_STR);
      $pre->bindParam(':page_id', $pageId, PDO::PARAM_STR);
      $pre->bindParam(':user_agent', $userAgent, PDO::PARAM_STR);
      $pre->bindParam(':country', $geoInf->countryName, PDO::PARAM_STR);
      $pre->bindParam(':city', $geoInf->city, PDO::PARAM_STR);
      $pre->bindParam(':region', $geoInf->regionName, PDO::PARAM_STR);

      $pre->execute();
    } catch (\Exception $e) {
      error_log('Hiba a stat mentésekor', $e->excepiton);
    }


  }


  public static function getStat($statType, $dateFrom, $dateTo){
    $db = Data::getInstance();
    $sql = '';
    switch ($statType) {
      case 'visitorsCount':
        $sql = SqlConst::STAT_VISITORS_COUNT;
        break;
      case 'browserStats':
        $sql = SqlConst::STAT_BROWSERS;
        break;
      case 'osStats':
        $sql = SqlConst::STAT_OS;
        break;
      case 'cityStat':
        $sql = SqlConst::STAT_CITY;
        break;
      case 'regionStat':
        $sql = SqlConst::STAT_REGION;
        break;
      case 'countryStat':
        $sql = SqlConst::STAT_COUNTRY;
        break;
      default:
        // code...
        break;
    }
    $pre = $db->prepare($sql);
    $pre->bindParam(':date_from', $dateFrom, PDO::PARAM_STR);
    $pre->bindParam(':date_to', $dateTo, PDO::PARAM_STR);
    $pre->execute();
    return $pre->fetchAll(PDO::FETCH_OBJ);
  }

}
?>
