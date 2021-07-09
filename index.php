<?php
require_once 'includes/systemUtil.php';
require_once 'dal/configSetting.php';
require_once 'dal/configMenu.php';
require_once 'config/config.php';

$mainSwitch = ConfigSetting::getSettingValue('main-switch');
if ((int)$mainSwitch == 0){
  require 'templates/underconstruction.html';
  return;
}

$headerHtml = "";
$footerHtml = "";
$contentLength = "";
$menuLength = "";
$isMobile = false;
$enabledMobileView = ConfigSetting::getSettingValue('enabled-mobile-view');
if (((int)$enabledMobileView == 1) && (SystemUtil::isMobile())){
  //csak mobile ág
//  $headerHtml = ConfigSetting::getSettingValue('header-mobile-html');
//  $footerHtml = ConfigSetting::getSettingValue('footer-mobile-html');
  $contentLength = ConfigSetting::getSettingValue('content-mobile-length');
  $isMobile = true;
}

if ($headerHtml == ""){
  $headerHtml = ConfigSetting::getSettingValue('header-html');
}
if ($footerHtml == ""){
  $footerHtml= ConfigSetting::getSettingValue('footer-html');
}
if ($contentLength == ""){
  $contentLength = ConfigSetting::getSettingValue('content-length');
}
$menuLength = ConfigSetting::getSettingValue('menu-length');

$title = ConfigSetting::getSettingValue('title');
$metaDescription = ConfigSetting::getSettingValue('meta-description');
$metaKeywords = ConfigSetting::getSettingValue('meta-keywords');
$bgColor = ConfigSetting::getSettingValue('background-color');
$menuItems = ConfigMenu::getMenuList();

$geoLocationtype = Config::$geoLocationtype;
$geoUrl = '';  // kwelo, geoplugin-http, geoplugin-https
switch ($geoLocationtype) {
  case 'kwelo':
    $geoUrl = Config::$urlKwelo;
    break;
  case 'geoplugin-http':
    $geoUrl = Config::$urlGeoPluginHttp;
    break;
  case 'geoplugin-https':
    $geoUrl = Config::$urlGeoPluginHttps;
    break;
}

$menuHtml = '';

if ($isMobile){

  $menuHtml = '<div class="dropdown">
  <span style="color:white;padding-left:10px;" onclick="showMenuItems()" class="dropbtn">&#9776;&nbsp;</span>
  <div id="dropdown-menu" class="dropdown-content">';
  foreach ($menuItems as $item) {
    $menuHtml .= '<a onClick="showPage(\'' . $item->id . '\')" title="'. $item->tooltip .'">' . $item->name . "</a>";
  }
  $menuHtml .= "</div></div>";
  $menuHtml .= '<script type="text/javascript">
  function showAlert() {
    alert("valami");
  }

  function showMenuItems() {
    document.getElementById("dropdown-menu").classList.toggle("show");
  }

  window.onclick = function(event) {
  if (!event.target.matches(".dropbtn")) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains("show")) {
        openDropdown.classList.remove("show");
      }
    }
  }
}
  </script>';
}
else {
  $menuHtml = '<ul class="menu-ul">';
  foreach ($menuItems as $item) {
    $menuHtml .= '<li class="menu-li"><a onClick="showPage(\'' . $item->id . '\')" title="'. $item->tooltip .'">' . $item->name . "</a></li>";
  }
  $menuHtml .= '</ul>';
}

 ?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="description" content="<?php print $metaDescription; ?>">
<meta name="keywords" content="<?php print $metaKeywords; ?>">
<title><?php print $title; ?></title>

<?php
if ($isMobile){
  print '<link href="css/mobile.css?x=20210507" rel="stylesheet" type="text/css">';
}else {
  print '<link href="css/normal.css?x=20210507" rel="stylesheet" type="text/css">';
}
?>

<style>
</style>
<?php
$jsType = "main";
include("includes/jslibs.php");
?>
</head>
<body style="background-color:<?php print $bgColor; ?>">
<input type="hidden" id="hd-is-mobile" value="<?php print $isMobile ? 1:0 ?>"/>
<input type="hidden" id="hd-ip" value="" />
<script id="geo-plugin-script" type="text/javascript" src="http://www.geoplugin.net/javascript.gp?x=<?php print rand(); ?>"></script>
<?php
if ($isMobile){
  require 'templates/mobileIndex.php';
}
else {
  require 'templates/normalIndex.php';
}
?>

  <script type="text/javascript">
    var requestIp = "<?php print SystemUtil::getRequestIp(); ?>";
    var baseUrl = "<?php print Config::$baseUrl; ?>";
    var geoType = "<?php print $geoLocationtype; ?>";
    var geoUrl = "<?php print $geoUrl; ?>";

    var geoObj = {};
    geoObj.city = null;
    geoObj.country = null;
    geoObj.region = null;

    $( document ).ready(function(){
      //$( document ).tooltip();
      var page = Util.getQueryVariable('page');

      if (geoType != 'none'){
        geoUrl = geoUrl.split('{{ip}}').join(requestIp);
        geoUrl = new URL(geoUrl);
        geoUrl.searchParams.set('x', Date.now());
      }

      if (geoType == 'kwelo'){

        $.getJSON(geoUrl,
            function (data) {
              try {
                geoObj.city = data.data.geolocation.city.names.en;
                geoObj.country = data.data.geolocation.country.names.en;
                geoObj.region = data.data.geolocation.subdivisions[0].names.en;
              } catch (e) {
                console.log(e)
              }


            }
        ).always(function() { showPage(page); });

      }
      else if (geoType == 'geoplugin-http'){
        $.getJSON(geoUrl,
          function (data) {

            try {
              geoObj.city = data.geoplugin_city;
              geoObj.country = data.geoplugin_country;
              geoObj.region = data.geoplugin_region;
            } catch (e) {
              console.log(e)
            }
          }
        ).always(function() { showPage(page); });
      }
      else {
        showPage(page);
      }

    });

    function showPage(id){

      Util.removeUrlParams(Util.getQueryVariable('page'));
      if (!Util.isNullOrEmpty(id)){
        Util.addUrlParams('page', id);
      }
      var pageContentUrl = new URL(baseUrl + '/rest/getMenuContent.php');
      pageContentUrl.searchParams.set('page', id);
      pageContentUrl.searchParams.set('mobile', $('#hd-is-mobile').val());
      if (!Util.isNullOrEmpty(geoObj.city)){
          pageContentUrl.searchParams.set('city', geoObj.city);
      }
      if (!Util.isNullOrEmpty(geoObj.region)){
          pageContentUrl.searchParams.set('region', geoObj.region);
      }
      if (!Util.isNullOrEmpty(geoObj.country)){
          pageContentUrl.searchParams.set('country', geoObj.country);
      }
      pageContentUrl.searchParams.set('x', Date.now());

      $.ajax({
            url: pageContentUrl.href,
            type: 'GET',
            dataType: 'json',
            success: function(data){
              $('#div-content').html(data.html);
              $('#div-last-modified').html('Utoljára módosítva:' + data.modified);
            },
            error:function(response) {
              $('#div-content').html(response.responseText);
              $('#div-last-modified').html('');
            }
        });
    }


  </script>
</body>
