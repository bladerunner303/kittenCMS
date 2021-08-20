<?php
// 2021.08.21
if (!isset($jsType)){
	$jsType = "admin";
}

$jquery = '';
$jqueryUi = '';
$jqueryUiCss = '';
$handlerbars= '';
$jqueryDatePickerLang = '';
$underscore = '';
$chartjs = '';
$editorjs = '';
$editorCss = '';

if ($jsType == "admin"){

	$jquery = "../js/jquery-3.5.1.min.js";
	$jqueryUi = "../js/jquery-ui-1.12.1.custom.min.js?x=20210419";
	$jqueryUiCss = "../js/jquery-ui-1.12.1.custom.min.css?x=20210419";
	$jqueryDatePickerLang = "../js/jquery.ui.datepicker-hu-1.12.1.js";
	$underscore = "../js/underscore-1.12.1.min.js";
	$chartjs = "../js/chart-2.9.4.min.js";
	$editorjs = "../js/suneditor-20210413.min.js";
	$editorCss = "../js/suneditor-20210413.min.css";
}
else if ($jsType == "sample"){
		$jquery = "../js/jquery-1.12.4.min.js";
}
else {
		$jquery = "js/jquery-1.12.4.min.js";
}

if ($jsType == "admin"){
	print "<script type=\"text/javascript\" src=\"$jquery\"></script>";
	print "<script type=\"text/javascript\" src=\"$jqueryUi\"></script>";
	print "<link rel=\"stylesheet\" href=\"$jqueryUiCss\"></script>";
	print "<script type=\"text/javascript\" src=\"$jqueryDatePickerLang\"></script>";
	print "<script type=\"text/javascript\" src=\"$underscore\"></script>";
	print "<script type=\"text/javascript\" src=\"$chartjs\"></script>";
	print "<link href=\"$editorCss\" rel=\"stylesheet\">";
	print "<script type=\"text/javascript\" src=\"$editorjs\"></script>";
}
else {
	print "<script type=\"text/javascript\" src=\"$jquery\"></script>";
}

?>

<script type="text/javascript">

	Util = (function(){
		return {
			 	pad(n, width, z) {
  				z = z || '0';
  				n = n + '';
  				return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
				},
			  getCurrentDate(){
					  var d = new Date();
						return d.getFullYear() +  "." +
									Util.pad(d.getMonth()+1, 2) + "." +
									Util.pad(d.getDate(), 2);
					//return new Date().toLocaleString().split(' ').join('').substr(0,10);
				},
				getCurrentTime(){
						var d = new Date();

						return 	 Util.pad(d.getHours(), 2) + ":" +
										 Util.pad(d.getMinutes(), 2) + ":" +
						 			 	 Util.pad(d.getSeconds(), 2);

				},
				getCurrentDateTime(){
					return Util.getCurrentDate() + ' ' + Util.getCurrentTime();
				},
				getDefaultDatePicker : function (yearRange){
					if (typeof yearRange == 'undefined'){
						yearRange = "-100:+0";
					}
					return {
						yearRange: yearRange,
						regional: "hu",
						changeYear: true ,
						changeMonth: true
					};
				},
				getQueryVariable(variable){
				       var query = window.location.search.substring(1);
				       var vars = query.split("&");
				       for (var i=0;i<vars.length;i++) {
				               var pair = vars[i].split("=");
				               if(pair[0] == variable){return pair[1];}
				       }
				       return("");
				},
				addUrlParams(paramName, paramValue){
					var url = new URL(window.location);
				  url.searchParams.set(paramName, paramValue);
				  window.history.pushState({}, '', url);
				},
				removeUrlParams(paramName){
					var url = new URL(window.location);
				  url.searchParams.delete(paramName);
				  window.history.pushState({}, '', url);
				},
				nvl : function (myObject, defaultIsNull) {
					if ((myObject === null) || (typeof myObject == 'undefined') || (myObject.toString() == '')){
						return defaultIsNull;
					}
					else {
						return myObject;
					}
				},
				isNullOrEmpty : function (myObject){
					if (Util.nvl(myObject, '') !== '') {
						return false;
					}
					else {
						return true;
					}
				},
			 copyStringToClipboard : function (str) {

   	 			var el = document.createElement('textarea');
   				el.value = str;
   				// Set non-editable to avoid focus and move outside of view
   				el.setAttribute('readonly', '');
   				el.style = {position: 'absolute', left: '-9999px'};
   				document.body.appendChild(el);

   				el.select();
      		document.execCommand('copy');
   				document.body.removeChild(el);
				},
				handleErrorResponse: function(response){
					if (response.status == 401){
						window.location.replace('index.php');
					}
					else if(response.status == 403){
						Util.showErrorNotificationBar('Nincs jogosultsága a műveletre');
					}
					else if(response.status == 500){
						Util.showErrorNotificationBar(response.responseText);
					}
				},
				showErrorNotificationBar(message){
					Util.showNotificationBar(message, 2000, "red", "white");
				},
				showNotificationBar(message, duration, bgColor, txtColor, height) {

					if ($('#notification-bar').length != 0){
							$('#notification-bar').remove();
					}

			     var HTMLmessage = "<div class='notification-message' style='text-align:center; line-length: 30px;padding:10px;'> " + message + " </div>";
		       $('body').prepend("<div id='notification-bar' style='display:none; width:100%; min-height:" + Util.nvl(height, 40) +
						 "px; background-color: " + Util.nvl(bgColor, "grey")  +
						 "; position: fixed; z-index: 100; color: " + Util.nvl(txtColor, "white") +
						  ";border-bottom: 1px solid " + Util.nvl(txtColor, "darkgrey") + ";'>" +
							  HTMLmessage + "</div>");

				    $('#notification-bar').slideDown(function() {
				        setTimeout(function() {
				            $('#notification-bar').slideUp(function() {});
				        }, duration);
				    });
				},
				showSimpleDialog(title, messageHtml, width, buttons){
				 	if (Util.isNullOrEmpty(buttons)){
						buttons = {
								Ok: function () {
										$(this).dialog("close");
								}
						};
					}

					if (Util.isNullOrEmpty(width)){
						width = "300px;";
					}

					//$('<div></div>').dialog({
					if ($('#my-simple-dialog').length != 0){
							$('#my-simple-dialog').remove();
					}
					$('body').append('<div id="my-simple-dialog" ></div>');
					$('#my-simple-dialog').dialog({
				      modal: true,
			        title: title,
							width: width,
							position: { my: 'top', at: 'top+150' },
			        open: function () {
			            $(this).html(messageHtml);
			        },
			        buttons: buttons
			    });
				},
				confirm(title, message, callbackIfYes, callbackParam){
					$('<div id="confirmContent"></div>').appendTo('body')
						.html('<div><h6>' + message + '</h6></div>')
						.dialog({
							modal: true,
							title: title,
							zIndex:10000,
							autopen: true,
							resizable: false,
							buttons : {
								Igen: function (){
									try {
										callbackIfYes(callbackParam);
										$(this).dialog('close');
									} catch (e) {
											Util.showErrorNotificationBar(e);
									}
								},
								 Nem: function (){
									 $(this).dialog('close');
								 }
							 },
							 close:function (event, ui) {
								 $(this).remove();
							 }
						})
				}
			}
		}());

</script>
