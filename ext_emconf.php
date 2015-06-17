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
	'version' => '0.4.4-dev',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 0,
	'author' => 'Valentin Schmid',
	'author_email' => 'valentin.schmid@newmedia.ch',
	'author_company' => 'Suedostschweiz Newmedia AG',
	'constraints' =>
	array (
		'depends' =>
		array (
			'cms' => '',
			'typo3' => '6.2.0-6.2.99',
			'ics_awstats' => '0.4.0-0.6.99',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
);

