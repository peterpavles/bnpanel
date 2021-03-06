<?php
/* For licensing terms, see /license.txt */
define("PAGE", "Support Area");

require '../includes/compiler.php';


if(!isset($main->getvar['page'])) { 
	$main->getvar['page'] = 'kb';
}

$support_navigation = $main->getSupportNavigation();

$support_item = false;
if (isset($support_navigation[$main->getvar['page']]) && !empty($support_navigation[$main->getvar['page']])) {
	$support_item = $support_navigation[$main->getvar['page']];
}
$header = 'Home';
$link = 'pages/home.php';	

if (isset($support_item) && !empty($support_item)) {		
	$header = $support_item['visual'];		
	$link = "pages/". $support_item['link'] .".php";
	$header = $support_item['visual'];
}	

if($db->config("senabled") == 0) {
	$html = $db->config("smessage");
} else {
	if(!file_exists($link)) {
		$html = "Seems like the .php is non existant. Is it deleted?";	
	} else {
		//If deleting something
		if (preg_match("/[\.*]/", $main->getvar['page']) == 0) {
			require $link;
			$content = new page();
			if(isset($main->getvar['sub'])) {
				ob_start();
				$content->content();
				$html = ob_get_contents(); # Retrieve the HTML
				ob_clean(); # Flush the HTML
			} elseif(isset($content->navlist)) {
				$html = $content->description();
			} else {
				ob_start();
				$content->content();
				$html = ob_get_contents(); # Retrieve the HTML
				ob_clean(); # Flush the HTML	
			}
		} else {
			$html = "";
			$email->staff("Possible Hacking Attempt", "A user has been logged trying to hack your copy of BNPanel, their IP is: ". $main->removeXSS($_SERVER['REMOTE_ADDR']));
		}
	}
}


$data = $main->table($header, $html);


echo $style->replaceVar("layout/one-col/index.tpl", array('content' => $data)); 
