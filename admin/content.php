<!DOCTYPE html>

<?php

require_once '../includes/logger.php';
require_once '../dal/session.php';
require_once '../config/config.php';

try {
  $sessionId = $_COOKIE["sessionId"];
  if (!Session::isValid($sessionId)){
      header('Location: index.php?x=expired');
      return;
  }

  $sessionData = Session::getSessionData($sessionId);
  $userRole = $sessionData->user_role;
} catch (\Exception $e) {
    error_log($e->getMessage() . $e->getTraceAsString());
    header('Location: index.php?x=error&msg=' . $e->getMessage());
    return;
}

$maxFileSizeByte = Config::$uploadFileByteLimit;

?>

<html>
<head>
<meta charset="UTF-8">
<title>Admin oldal</title>
<style>
 .menu-ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

.menu-ul li {
  float: left;
  border-right:1px solid #bbb;
}

.menu-ul li:last-child {
  border-right: none;
}

.menu-ul a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  cursor: pointer;
}

.menu-ul li a:hover:not(.active) {
  background-color: #111;
}

.active {
  background-color: #4CAF50;
}
body{
  margin: 0px;
}
.div-footer {
    min-height: 2%;
    color: white;
    font-size: small;
    background: black;
    position:fixed;
    bottom:0;
    width:100%;
}
</style>
<?php include("includes/jslibs.php"); ?>
</head>
<body>
  <input type="hidden" id="hd-max-file-size" value="<?php print($maxFileSizeByte); ?>"></input>
  <input type="hidden" id="hd-user-role" value="<?php print($userRole); ?>"></input>
  <ul class="menu-ul">
    <li ><a id="menu-sites"  onclick="showMenuSites();">Menü oldalak</a></li>
    <li ><a id="menu-files" onclick="showFiles();">Fájlok</a></li>
    <li ><a id="menu-settings" onclick="showSettings();">Beállítások</a></li>
    <li ><a id="menu-stats" onclick="showStats();">Statisztika</a></li>
    <li ><a id="menu-log" onclick="showLog();">Hiba napló</a></li>
    <?php if ($userRole=='ADMIN'){
      print '<li><a id="menu-users" onclick="showUsers();">Felhasználó kezelés</a></li>';
    }
    ?>
    <li><a id="menu-pwd-change" onclick="showPasswordChange();">Jelszó váltás</a></li>
    <li style="float:right"><a onclick="logout();">Kijelentkezés</a></li>
  </ul>
  <div id="main-content" style="margin-top:10px;"></div>
  <div class="div-footer">
    <div style="width: 50%;display: inline-block;padding-left: 3px">Licenc: GPLv3</div>
    <div style="width: 49%;display: inline-block;text-align: right;">Verzió: 1.0.1</div>
  </div>
<script type="text/javascript">
  var baseUrl = "<?php print Config::$baseUrl; ?>";
  $( document ).ready(function(){
    //$( document ).tooltip();
    var menu = Util.getQueryVariable('menu');
    switch (menu) {
      case 'pwdChange':
        showPasswordChange();
        var needNewPassword = Util.getQueryVariable('needNewPassword');
        Util.removeUrlParams('needNewPassword');
        if (Util.nvl(needNewPassword, 'false') == 'true'){
          Util.showSimpleDialog("Jelszó változtatás szükséges!", "A fiókjának biztonsága érdekében kérjük azonnal változtasd meg a jelszavad!");
        }

        break;
      case 'log':
        showLog();
        break;
      case 'settings':
        showSettings();
        break;
      case 'sites':
        showMenuSites();
        break;
      case 'users':
        showUsers();
        break;
      case 'stats':
        showStats();
        break;
      case 'files':
        showFiles();
        break;
      default:
        showMenuSites();
    }

  });

function logout(){
  var loginUrl = new URL(baseUrl + '/rest/logout.php');
  loginUrl.searchParams.set('x', Date.now());

  $.ajax({
        url: loginUrl.href,
        type: 'GET',
        success: function(data){
          if (data){
            window.location.href = baseUrl + '/admin/index.php?x=logout';
          }
        },
        error:function(response) {
          if (response.status == 401){
			        	window.location.href =  baseUrl + '/admin/index.php?x=expired';
			    }
          else {
            Util.showErrorNotificationBar(response.responseText);
          }

        }
    });
}

function showPasswordChange(){
  $('.active').removeClass('active');
  $('#menu-pwd-change').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'pwdChange');
  $.get("../templates/passwordChange.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
    $('#main-content').html(text);
  });
}

function showLog(){
  $('.active').removeClass('active');
  $('#menu-log').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'log');
  $.get("../templates/log.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
    $('#main-content').html(text);
  });
}

function showStats(){
  $('.active').removeClass('active');
  $('#menu-stats').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'stats');
  refreshStats();
}

function showUsers(){
  $('.active').removeClass('active');
  $('#menu-users').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'users');
  refreshUsers();
}

function showSettings(){
  $('.active').removeClass('active');
  $('#menu-settings').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'settings');
  refreshSettings();
}

function showFiles(){
  $('.active').removeClass('active');
  $('#menu-files').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'files');
  refreshFiles();
}


function showMenuSites(){
  $('.active').removeClass('active');
  $('#menu-sites').addClass('active');
  Util.removeUrlParams('menu');
  Util.addUrlParams('menu', 'sites');
  refreshMenu();
}

function refreshSettings(){
  var settingUrl = new URL(baseUrl + '/rest/getSettings.php');
  settingUrl.searchParams.set('x', Date.now());

  $.ajax({
        url: settingUrl.href,
        type: 'GET',
        success: function(data){
          if (data){
            $.get("../templates/settings.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
              var template = _.template(text);
			    	  $('#main-content').html(template({rows: data, lastRefresh: Util.getCurrentDateTime()}));
            });

          }
        },
        error:function(response) {
          Util.handleErrorResponse(response);
        }
    });
}

function refreshUsers(){
  var usersUrl = new URL(baseUrl + '/rest/getUsers.php');
  usersUrl.searchParams.set('x', Date.now());

  $.ajax({
        url: usersUrl.href,
        type: 'GET',
        success: function(data){
          if (data){
            $.get("../templates/users.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
              var template = _.template(text);
              $('#main-content').html(template({
                rows: data.rows,
                lastRefresh: Util.getCurrentDateTime(),
                currentUserRole: data.userRole,
                roles:[
                  {key: 'ADMIN', name: 'Admin'},
                  {key: 'EDITOR', name: 'Szerkesztő'}
                ],
                statuses:[
                  {key: 'AKTIV', name: 'Aktív'},
                  {key: 'INAKTIV', name: 'Inaktív'}
                ]
              }));

            });

          }
      },
      error:function(response) {
        Util.handleErrorResponse(response);
      }
    });
}

function refreshFiles(){
  var filesUrl = new URL(baseUrl + '/rest/getFiles.php');
  filesUrl.searchParams.set('x', Date.now());

  $.ajax({
        url: filesUrl.href,
        type: 'GET',
        success: function(data){
          if (data){
            $.get("../templates/files.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
              var fileSizeByte = parseInt($('#hd-max-file-size').val());
              var fileSizeMB = fileSizeByte / 1024 /1024;
              var template = _.template(text);
              $('#main-content').html(template({
                rows: data,
                lastRefresh: Util.getCurrentDateTime(),
                maxFileSizeByte: fileSizeByte,
                maxFileSizeMB:fileSizeMB
              }));
            });
          }
      },
      error:function(response) {
        Util.handleErrorResponse(response);
      }
    });
}

function refreshStats(){
  $.get("../templates/stats.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
    $('#main-content').html(text);
  });

}

function refreshMenu(){
  var menuUrl = new URL(baseUrl + '/rest/getMenus.php');
  menuUrl.searchParams.set('x', Date.now());

  $.ajax({
        url: menuUrl.href,
        type: 'GET',
        success: function(data){
          if (data){
            $.get("../templates/sites.html?x=" + Util.getCurrentDate()).then(function(text, status, xhr){
              var template = _.template(text);
              $('#main-content').html(template({
                rows: data,
                lastRefresh: Util.getCurrentDateTime(),
                types:[
                  {key: 'ALAP', name: 'Alap'},
                  {key: 'NEWS', name: 'Hírfolyam'}
                ],
              }));

              editors = [];
              for (var i=0; i < data.length; i++) {
                editors.push(
                  {id: data[i].id,
                   editor: SUNEDITOR.create((document.getElementById('tb-content-' + data[i].id) || 'tb-content-' + data[i].id), {
                  font : [
                          'Arial',
                          'tohoma',
                          'Courier New,Courier'
                      ],
                   fontSize : [
                          8, 10, 12, 14, 18, 24, 36
                      ],
                      colorList : [
                          ['#ccc', '#dedede', 'OrangeRed', 'Orange', 'RoyalBlue', 'SaddleBrown'],
                          ['SlateGray', 'BurlyWood', 'DeepPink', 'FireBrick', 'Gold', 'SeaGreen']
                      ],
                      paragraphStyles : [
                          'spaced',
                          'neon'
                      ],
                      textStyles : [
                          'translucent'
                      ],
                      width : '100%',
                      maxWidth : '1600px',
                      minWidth : '400px',
                      height : '400px',
                      videoWidth : '80%',
                      youtubeQuery : 'autoplay=1&mute=1&enablejsapi=1',
                  buttonList : [
                  	    ['undo', 'redo', 'font', 'fontSize', 'formatBlock'],
                        ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript', 'removeFormat'],
                        ['fontColor', 'hiliteColor', 'outdent', 'indent', 'align', 'horizontalRule', 'list', 'table'],
                        ['link', 'image', 'video', 'showBlocks', 'codeView', 'save']
                    ],
                    callBackSave: function (contents, isChanged) {
                        alert(contents + isChanged);
                    }
                  })
                });
                //editors[i].editor.setContents(data[i].content);
              }

            });
          }
      },
      error:function(response) {
        Util.handleErrorResponse(response);
      }
    });
}


</script>

</body>
</html>
