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

<?php
if ($isMobile){
  require 'templates/mobileIndex.php';
}
else {
  require 'templates/normalIndex.php';
}
?>

  <script type="text/javascript">
    var baseUrl = "<?php print Config::$baseUrl; ?>";

    $( document ).ready(function(){
      //$( document ).tooltip();
      var page = Util.getQueryVariable('page');
      showPage(page);
    });
    function showPage(id){
      Util.removeUrlParams(Util.getQueryVariable('page'));
      if (!Util.isNullOrEmpty(id)){
        Util.addUrlParams('page', id);
      }
      var pageContentUrl = new URL(baseUrl + '/rest/getMenuContent.php');
      pageContentUrl.searchParams.set('page', id);
      pageContentUrl.searchParams.set('mobile', $('#hd-is-mobile').val());
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
