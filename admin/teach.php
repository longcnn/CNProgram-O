<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.1
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// teach.php
$upperScripts = <<<endScript

    <script type="text/javascript">
<!--
      function showMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('teachForm');
        sh.style.display = 'block';
        tf.style.display = 'none';
      }
      function hideMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('teachForm');
        sh.style.display = 'none';
        tf.style.display = 'block';
      }
      function showHide() {
        var display = document.getElementById('showHelp').style.display;
        switch (display) {
          case '':
          case 'none':
            return showMe();
            break;
          case 'block':
            return hideMe();
            break;
          default:
            alert('display = ' + display);
        }
      }
//-->
    </script>
endScript;

  $msg = '';
  if((isset($_POST['action']))&&($_POST['action']=="teach")) {
    $msg = insertAIML();
  }
  $teachContent = $template->getSection('TeachBotForm');
  $showHelp = $template->getSection('TeachShowHelp');
  #$teachContent = '';
  #$showHelp = '';

    $topNav        = $template->getSection('TopNav');
    $leftNav       = $template->getSection('LeftNav');
    $main          = $template->getSection('Main');
    $topNavLinks   = makeLinks('top', $topLinks, 12);
    $navHeader     = $template->getSection('NavHeader');
    $leftNavLinks  = makeLinks('left', $leftLinks, 12);
    $FooterInfo    = getFooter();
    $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
    $errMsgStyle   = $template->getSection($errMsgClass);
    $noLeftNav     = '';
    $noTopNav      = '';
    $noRightNav    = $template->getSection('NoRightNav');
    $headerTitle   = 'Actions:';
    $pageTitle     = 'My-Program O - Teaching Interface';
    $mainContent   = $template->getSection('TeachMain');
    #die ("main content = <pre>|$mainContent|</pre><br />\n");
    #$mainContent   = 'Hello!';
    $mainTitle     = "Chatbot Teaching Interface for $bot_name [helpLink]";

    $mainContent   = str_replace('[bot_name]', $bot_name, $mainContent);
    $mainContent   = str_replace('[teach_content]', $teachContent, $mainContent);
    $mainContent   = str_replace('[showHelp]', $showHelp, $mainContent);
    $mainTitle   = str_replace('[helpLink]', $template->getSection('HelpLink'), $mainTitle);

function formatchinese($str){
	if(preg_match_all("/[\x{4e00}-\x{9fa5}]{1}/u",$str,$out)){
		foreach($out[0] as $value){
			$str = preg_replace("/".$value."/",$value." ",$str);
		}
	}
	return trim(preg_replace("/\s+/"," ",$str));
}
function insertAIML() {
  //db globals
  global $template, $msg;
  $dbconn = db_open();
  $aiml = "<category><pattern>[pattern]</pattern>[thatpattern]<template>[template]</template></category>";
  $aimltemplate = formatchinese(trim($_POST['template']));
  $pattern = formatchinese((trim($_POST['pattern'])));
  $thatpattern = formatchinese((trim($_POST['thatpattern'])));
  $aiml = str_replace('[pattern]', $pattern, $aiml);
  $aiml = (empty($thatpattern)) ? str_replace('[thatpattern]', "<that>$thatpattern</that>", $aiml) : $aiml;
  $aiml = formatchinese(str_replace('[template]', $aimltemplate, $aiml));
  $topic = formatchinese((trim($_POST['topic'])));
  $bot_id = (isset($_SESSION['poadmin']['bot_id'])) ? $_SESSION['poadmin']['bot_id'] : 1;
  if(($pattern=="") || ($template=="")) {
    $msg = 'You must enter a user input and bot response.';
  }
  else {
    $sql = "INSERT INTO `aiml` (`id`,`bot_id`, `aiml`, `pattern`,`thatpattern`,`template`,`topic`,`filename`, `php_code`) VALUES (NULL,'$bot_id', '$aiml','$pattern','$thatpattern','$aimltemplate','$topic','admin_added.aiml', '')";
    //$sql = mysql_real_escape_string($sql,$dbconn);
	$result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");

    if($result) {
      $msg = "AIML added.";
    }
    else {
      $msg = "There was a problem adding the AIML - no changes made.";
    }
  }
  mysql_close($dbconn);

  return $msg;
}

?>