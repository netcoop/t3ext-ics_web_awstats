<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ics_web_awstats".
 *
 * Auto generated 18-06-2015 00:46
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'ICS Web AWStats',
	'description' => 'Provide AWStats Statistics in the Web-Section.',
	'category' => 'module',
	'version' => '0.7.0',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Loek Hilgersom',
	'author_email' => 'typo3extensions@netcoop.nl',
	'author_company' => 'Netcoop.nl',
	'constraints' =>
	array (
		'depends' =>
		array (
			'cms' => '',
			'typo3' => '6.2.0-7.9.99',
			'ics_awstats' => '0.6.4-0.7.0',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
);

