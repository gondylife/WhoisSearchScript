<?php
/*
	WHOIS DOmain Search Script::A Whois lookup script written in PHP, i wrote this in 2011 for my domain name registration website http://isoftnigeria.com from an example by Marek Rozanski.
	You can add any other extension you desire and modify to suit your needs.
	Copyright (C) 2011-2016 Godswill Okwara
*/


$product= $_SESSION['product'];
define('FILE_NAME',					"register.php?p=$product&s=do");

// Language settings

define('PAGE_TITLE_META', 			'iSoft Nigeria | domain search');			// Title to be used in META tag within HEADER
define('POWERED_BY',				'iSoft Nigeria');			// Text to display in a footer, please do not change if you don't have to

define('MAIN_COMMAND', 				'Enter your desired domain name to search');	// Command in a main window
define('CHECK_BUTTON', 				'Check');									// Check button text
define('META_CHARSET',				'iso-8859-1');								// Charset to be used in META tags
define('META_LANGUAGE',				'en');										// Language to be used in META tags

define('FOOTER_TEXT', 				'Please wait for the answer - 
									 due to isoft servers overload it may take a while to lookup all names.
									 Sometimes you may need to reload the page and repeat lookup.');				// text in footer
define('FOOTER_RELOAD_TEXT',		'Reload');					// text of the link to reload the page

// This is an extra layer displayed during domain search. It is helpfull when someone is
// searching for all domains at once.
// If you are not confident with layers at all
// just disable it - set the first value below to false.

define('WAIT_LAYER_ENABLED',		true);
define('WAIT_TITLE',				'Please wait...');
define('WAIT_MESSAGE',				'If the lookup takes too long , click here to reload the page and try again.');

define('LINK_REGISTER_TEXT',		'Register');						// Register link text
define('STATUS_BAR_REGISTER_TEXT',	'Register');						// Status bar register message - when hovered over the "Register" link
define('STATUS_BAR_DETAILS',		'Details of');						// Status bar "Details of" - when hovered over "Details" link
define('LINK_TAKEN_DETAILS',		'Details');							// "Details" text
define('LINK_TAKEN_GOTO',			'Goto');						// "Goto" text

define('ALL_TEXT',					'all');						// Text to display for all domains checking
define('CLOSE_BUTTON_TEXT',			'Close window');					// "Close" button text in a details window
define('AVAILABLE_TEXT',			'Available');						// Text displayed if the domain is available
define('NOT_AVAILABLE_TEXT',		'Not Available');						// Text displayed if the domain is not available

define('ERROR_TOO_SHORT',			'The domain name you typed is too short - it must contain minimum 3 characters'); 			// Error message if the domain name is too short
define('ERROR_TOO_LONG',			'The domain name you typed is too long - it may contain maximum 63 characters');		// Error message if the domain name is too long
define('ERROR_HYPHEN',				'Domain names cannot begin or end with a hyphen or contain double hyphens');			// Error message if the domain starts with hyphen or contains double hyphen
define('ERROR_CHARACTERS',			'Domain names can only contain alphanumerical characters and hyphens');								// Error message if the domain contains other characters than letters, digits or hyphens

// DESIGN PARAMETERS

		// change it to whatever you like

// Define lookup variables

// .com domains
define('COM_SERVER', 	"whois.verisign-grs.com");			// server to lookup for domain name
define('COM_NOMATCH',	"No match");							// string returned by server if the domain is not found
define('COM_INCLUDE',	true);									// include this domain in lookup

// .net domains
define('NET_SERVER', 	"whois.verisign-grs.com");			// server to lookup for domain name
define('NET_NOMATCH',	"No match");							// string returned by server if the domain is not found
define('NET_INCLUDE',	true);									// include this domain in lookup

// .org domains
define('ORG_SERVER',	'whois.publicinterestregistry.net');	// server to lookup for domain name
define('ORG_NOMATCH',	'NOT FOUND');								// string returned by server if the domain is not found
define('ORG_INCLUDE',	true);									// include this domain in lookup

// .info domains
define('INFO_SERVER',	'whois.afilias.net');					// server to lookup for domain name
define('INFO_NOMATCH',	'Not found');							// string returned by server if the domain is not found
define('INFO_INCLUDE',	true);									// include this domain in lookup

// .biz domains
define('BIZ_SERVER',	'whois.nic.biz');						// server to lookup for domain name
define('BIZ_NOMATCH',	'Not found');							// string returned by server if the domain is not found
define('BIZ_INCLUDE',	true);									// include this domain in lookup

// Shall we use register link? (true/false)
define('REG_LINK',	false);
// If yes, give the url, it can be your affiliate link
define('REG_URL',	'http://www.123-reg.co.uk/affiliate.cgi?id=AF8763');

// Do you want a log file? (true/false)
define('WANTLOG',	false);
// If yes, give the log file name here
// remember to chmod the file to 777 (change permition to writable for everyone)
define('LOGFILE',	'mrwhois.log');


/* 
#################################################################################################################
End of variables, you do not need to change anythin below this line.
#################################################################################################################
*/ 

if(isset($_POST['ddomain']))
{
if ($_POST['type']!="") define('TYPE', $_POST['type']);
if ($_POST['ddomain']!="") define('DDOMAIN', $_POST['ddomain']);
}else{
 define('TYPE', $type);
 define('DDOMAIN', $ddomain);
}
// This function displays an available domain
function dispav($what)
{
	echo '<tr><td nowrap align="center">';
	if (REG_LINK)
	{
		echo '<a href="'.REG_URL.'" target="_blank" onMouseOver="window.status=\''.STATUS_BAR_REGISTER_TEXT.' '.$what.'\';return true" onMouseOut="window.status=\'\';return true">'.LINK_REGISTER_TEXT.'</a>';
	}
	else
		echo '&nbsp;';
	echo '</td>
	<td nowrap align="center" class="available"><b>'.$what.'</b></td><td colspan=3>&nbsp;</td></tr>';
}

// Function to display an unavailable domain with additional links
function dispun($what,$where)
{
  echo '<tr>
  			<td colspan="2">&nbsp;</td>
            <td align="center" nowrap class="notavailable"><b>'.$what.'</b></td>
        <td nowrap align="center">
		&nbsp;</td>
        <td nowrap align="center"><a href="http://www.'.$what.'" target="_blank">'.LINK_TAKEN_GOTO.'</a></td>
        </tr>';
}

function startborder()
{
  echo '<div style= "margin-left:92px; margin-top:30px;">
  		<table align="center" width="590" border="0" cellspacing="0" cellpadding="0">
        <tr><td width="100%" class="windowborder">
        <table width="590" border="0" cellspacing="1" cellpadding="2">
        <tr><td class="windowinside">';
}


function endborder()
{
  echo '</td></tr></table></td></tr></table>
  </div>';
}

function disperror($text)
{
  startborder();
  echo '<center><b class="errors">'.$text.'</b></center>';
  endborder();
}

function main()
{
  echo '<br>';
  startborder();
  echo '
  <table width="100%" align="center" cellspacing="0" cellpadding="1">
  <tr>
  <td colspan="2" align="center" width="100%"><b>'.MAIN_COMMAND.'</b></td>
  </tr>
  <tr>
  <td align="center">
     <form method="POST" action="'.FILE_NAME.'">
     <input type="hidden" name="action" value="checkdom">
	 <input type="hidden" name="doption" value="'.$_SESSION['doption'].'">
     <input type="hidden" name="type" value="'.TYPE.'">
     <input type="text" name="ddomain" size="30" maxlength="63" value="'.DDOMAIN.'">&nbsp;
	 <select name="type">';
	 
	  echo '<option'; if(TYPE=='com' or TYPE == '') { echo ' SELECTED '; } echo ' VALUE="com"> .com</option>'; 
	  echo '<OPTION'; if(TYPE=='net')  { echo ' SELECTED '; } echo ' VALUE="net"> .net</option>';
	  echo '<OPTION'; if(TYPE=='org')  { echo ' SELECTED '; } echo ' VALUE="org"> .org</OPTION>';	
	  echo '<OPTION'; if(TYPE=='info') { echo ' SELECTED '; } echo ' VALUE="info"> .info</OPTION>'; 
	  echo '<OPTION'; if(TYPE=='biz')  { echo ' SELECTED '; } echo ' VALUE="biz"> .biz</OPTION>'; 
	 
	 echo '</select>';

	echo '<input type="submit" name="button" value="'.CHECK_BUTTON.'">
  </td>
  <td align="left">';


echo '</form>
  </td>
  </tr>
  <tr><td colspan="2" align="center" class="footer">'.FOOTER_TEXT.'<br><br>
  <a class="footerreload" href="'.FILE_NAME.'" target="_self"><b>'.FOOTER_RELOAD_TEXT.'</b></a>
  </td></tr>
  </table>';
  
}

$available= true;	

if (isset($_GET['action']) && $_GET['action'] == "details")
{
$server = $_GET['server'];
$ddomain = $_GET['ddomain'];

echo '<pre>';
$fp = fsockopen($server,43);
fputs($fp, "$ddomain\r\n");
while(!feof($fp))
{
	echo fgets($fp,128);
}
fclose($fp);
echo '</pre>';
echo '<p align="center"><form><input type="button" value="'.CLOSE_BUTTON_TEXT.'" onclick="window.close()"></form>';
pagefooter();
exit;
}
else
{
if($_SESSION['doption'] != 'host')
{
if (WAIT_LAYER_ENABLED)
{
	echo '
	<script language=javascript>
	var ie4 = (document.all) ? true : false;
	var ns4 = (document.layers) ? true : false;
	var ns6 = (document.getElementById && !document.all) ? true : false;
	
	function hidelayer(lay) {
		if (ie4) {document.all[lay].style.visibility = "hidden";}
		if (ns4) {document.layers[lay].visibility = "hide";}
		if (ns6) {document.getElementById([lay]).style.display = "none";}
	}

	function showlayer(lay) {
		if (ie4) {document.all[lay].style.visibility = "visible";}
		if (ns4) {document.layers[lay].visibility = "show";}
		if (ns6) {document.getElementById([lay]).style.display = "block";}
	}
	</script>';

	echo '
	<script language="javascript">
	var laywidth  = screen.width/2;
	var layheight = screen.height/2;
	var layl   = (screen.width-laywidth)/2;
  	var layt   = (screen.height-layheight)/2;
	document.write("<div id=\'waitlayer\' align=\'center\' style=\'position:absolute; width:"+laywidth+"px; height:"+layheight+"px; z-index:-1; left:"+layl+"px; top:"+layt+"px; visibility: visible;\'>");
	</script>';

		echo '<center><b>'.WAIT_TITLE.'</b><br><br>
	<a href="'.FILE_NAME.'" target="_self">'.WAIT_MESSAGE.'</a>
	</div>';
}

// Check the name for bad characters
if(strlen(DDOMAIN) < 3)
{
	
	disperror(ERROR_TOO_SHORT);
	main();
	exit;
}
if(strlen(DDOMAIN) > 63)
{
	
	disperror(ERROR_TOO_LONG);
	main();
	
	exit;
}
if(ereg("^-|-$",DDOMAIN))
{
	
	disperror(ERROR_HYPHEN);
	main();
	
	exit;
}
if(!ereg("([a-z]|[A-Z]|[0-9]|-){".strlen(DDOMAIN)."}",DDOMAIN))
{
	
	disperror(ERROR_CHARACTERS);
	main();
	
	exit;
}

startborder();


echo '
  <table width="100%" align="center" cellspacing="0" cellpadding="1">
     <tr>
        <td nowrap align="center" class="separator"><b>&nbsp;</b></td>
        <td nowrap align="center" class="separator"><b>'.AVAILABLE_TEXT.'</b></td>
        <td nowrap align="center" class="separator"><b>'.NOT_AVAILABLE_TEXT.'</b></td>
        <td nowrap align="center" class="separator"><b>&nbsp;</b></td>
        <td nowrap align="center" class="separator"><b>&nbsp;</b></td>
     </tr>';


if ( (TYPE == "all" or TYPE == "com") and COM_INCLUDE )
{
	$domname = DDOMAIN.".com";
	$ns = fsockopen(COM_SERVER,43); fputs($ns,"$domname\r\n");
	$result = '';
	while(!feof($ns)) $result .= fgets($ns,128); fclose($ns);
	if (eregi(COM_NOMATCH,$result)) { dispav($domname); } else {$available= false; dispun($domname,COM_SERVER); }
	echo '<tr><td colspan="5" class="separator">&nbsp;</td></tr>';
}
if ( (TYPE == "all" or TYPE == "net") and NET_INCLUDE )
{
	$domname = DDOMAIN.".net";
	$ns = fsockopen(NET_SERVER,43); fputs($ns,"$domname\r\n");
	$result = '';
	while(!feof($ns)) $result .= fgets($ns,128); fclose($ns);
	if (eregi(NET_NOMATCH,$result)) { dispav($domname); } else {$available= false; dispun($domname,NET_SERVER); }
	echo '<tr><td colspan="5" class="separator">&nbsp;</td></tr>';
}

if ( (TYPE == "all" or TYPE == "org") and ORG_INCLUDE )
{
	$domname = DDOMAIN.".org";
	$ns = fsockopen(ORG_SERVER,43); fputs($ns,"$domname\r\n");
	$result = '';
	while(!feof($ns)) $result .= fgets($ns,128); fclose($ns);
	if (eregi(ORG_NOMATCH,$result)) { dispav($domname); } else {$available= false; dispun($domname,ORG_SERVER); }
	echo '<tr><td colspan="5" class="separator">&nbsp;</td></tr>';
}

if ( (TYPE == "all" or TYPE == "info") and INFO_INCLUDE )
{
	$domname = DDOMAIN.".info";

	$ns = fsockopen(INFO_SERVER,43); fputs($ns,"$domname\r\n");
	$result = '';
	while(!feof($ns)) $result .= fgets($ns,128); fclose($ns);
	if (eregi(INFO_NOMATCH,$result)) { dispav($domname); } else {$available= false; dispun($domname,INFO_SERVER); }
	echo '<tr><td colspan="5" class="separator">&nbsp;</td></tr>';
}

if ( (TYPE == "all" or TYPE == "biz") and BIZ_INCLUDE )
{
	$domname = DDOMAIN.".biz";
	$ns = fsockopen(BIZ_SERVER,43); fputs($ns,"$domname\r\n");
	$result = '';
	while(!feof($ns)) $result .= fgets($ns,128); fclose($ns);
	if (eregi(BIZ_NOMATCH,$result)) { dispav($domname); } else {$available= false; dispun($domname,BIZ_SERVER); }
	echo '<tr><td colspan="5" class="separator">&nbsp;</td></tr>';
}

echo '</table>';
endborder();
if (WAIT_LAYER_ENABLED)
{
	echo '<script language="javascript">
	hidelayer("waitlayer");
	</script>';
}

// if logging enabled write info to the file
if(WANTLOG)
{
	$remote_addr = $REMOTE_ADDR;
	$today = date("d-m-y H:i", time());
	if (file_exists(LOGFILE) and is_writeable(LOGFILE))
	{
		$fp = fopen(LOGFILE,"a+");
		$infolog = "Date: $today | IP: $remote_addr | ".DDOMAIN."\n";
		fputs($fp, $infolog);
		fclose($fp);
	}
}
main();

}

else
{
echo "<script>
	self.location= 'register.php?st=step2'
	</script>";
}
}

?>