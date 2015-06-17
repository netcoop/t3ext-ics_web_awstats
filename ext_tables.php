<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

if (TYPO3_MODE=='BE') {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModulePath('web_txicswebawstatsM1', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'mod1/');
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule('web','txicswebawstatsM1','',\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'mod1/');
}
?>
