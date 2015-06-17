<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
// Switch TYPO3_MOD_PATH for global or local installed extension
/*if(strstr($_SERVER['SCRIPT_FILENAME'], 'typo3/ext')) {
	define('TYPO3_MOD_PATH', 'ext/ics_web_awstats/mod1/');
} else {
	define('TYPO3_MOD_PATH', '../typo3conf/ext/ics_web_awstats/mod1/');
}*/

$MCONF['name']='web_txicswebawstatsM1';
$MCONF['script']='_DISPATCH';

$MCONF['access']='user,group';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:ics_web_awstats/mod1/locallang_mod.xml';
?>
