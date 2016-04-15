<?php

use \TYPO3\CMS\Backend\Utility\BackendUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006 ICSurselva AG (info@icsurselva.ch)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Module 'ICS Web AWStats' for the 'ics_web_awstats' extension.
 *
 * @author	Valentin Schmid <valli@icsurselva.ch>
 */

unset($MCONF);
require('conf.php');

$GLOBALS['LANG']->includeLLFile('EXT:ics_web_awstats/mod1/locallang.xml');
$GLOBALS['BE_USER']->modAccess($MCONF, 1);	// This checks permissions and exits if the users has no permission for entry.

class tx_icswebawstats_module1 extends \TYPO3\CMS\Backend\Module\BaseScriptClass {
	
	var $pageinfo;

	public function __construct() {
		parent::init();
		
		// Initialize document
		$this->doc = GeneralUtility::makeInstance('TYPO3\CMS\Backend\Template\DocumentTemplate');
		$this->doc->setModuleTemplate(
			ExtensionManagementUtility::extPath('ics_web_awstats') . 'mod1/mod_template.html'
		);
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->addStyleSheet(
			'tx_icswebawstats',
			'../' . ExtensionManagementUtility::siteRelPath('ics_web_awstats') . 'mod1/mod_styles.css'
		);
	}
	
	// If you chose 'web' as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	/**
	* Main function of the module. Write the content to $this->content
	*/
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = BackendUtility::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
	
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

			$this->doc->form='<form action="" method="post">';

			$this->content.= $this->doc->startPage($LANG->getLL('title'));
			$this->content.= $this->doc->header($LANG->getLL('title'));
			$this->content.= $this->doc->spacer(5);

			// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode = '
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = '.intval($this->id).';
				</script>
			';

			// Render content:
			$this->renderModuleContent();
			$this->doc->form='</form>';
		} else {
			// If no access or if ID == zero
			$this->content.=$this->doc->section('','<br />'.$LANG->getLL('click_page'),0,1);
		}
		
		$markers = array();
		$markers['CONTENT'] = $this->content;
		
		$buttons = $this->getButtons();
		
		// Build the <body> for the module
		$this->content = $this->doc->moduleBody($this->pageinfo, $buttons, $markers);
		// Renders the module page
		$this->content = $this->doc->render(
			$GLOBALS['LANG']->getLL('title'),
			$this->content
		);
	}

	/**
	 * Prints out the module HTML
	 */
	public function printContent()	{
		echo $this->content;
	}

	/**
	 * Evaluates the logfilename from the pageId
	 */
	protected function getLogfileNames($pageId) {
		$logfilenames_arr = array();

		// initialize tsparser for extensions
		$template = GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\ExtendedTemplateService');
		$template->tt_track = 0;
		$template->init();

		// get the rootline (perhaps not neccessary)
		$page = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		$rootline = $page->getRootLine($pageId);

		// we need all templates from rootline (do we?)
		$template->runThroughTemplates($rootline);

		// parse the template
		$template->generateConfig();

		// get the logfile entry
		$logfilename = $template->setup['config.']['stat_apache_logfile'];
		if ($logfilename) {
			array_push($logfilenames_arr,
			array('logfilename' => $logfilename, 'type' => '', 'page' => '')
			);
		}

		// search in each pagetype for additional logfile-entries
		if (array_key_exists('types.', $template->setup)) {
			foreach ($template->setup['types.'] as $type => $page ) {
				$logfilename = $template->setup[$page.'.']['config.']['stat_apache_logfile'];
				if ($logfilename) {
					array_push($logfilenames_arr,
					array('logfilename' => $logfilename, 'type' => $type, 'page' => $page)
					);
				}
			}
		}

		return $logfilenames_arr;
	}

	protected function is_member_of_logfilenames_arr($logfilenames_arr, $logfilename) {
		foreach ($logfilenames_arr as $logfiledata) {
			if ($logfiledata['logfilename'] == $logfilename) return true;
		}
		return false;
	}

	protected function getNoteTr($msg, $msg_colspan=1) {
		global $BACK_PATH;
		$content = '<tr><td width="18">';
		$content.= '<img src="'.$BACK_PATH.'gfx/icon_note.gif" width="18" height="16" border="0" alt="" />';
		$content.= '</td><td colspan="'.$msg_colspan.'">';
		$content.= $msg;
		$content.= '</td></tr>'."\n";
		return $content;
	}

	protected function getWarningTr($msg, $msg_colspan=1) {
		global $BACK_PATH;
		$content = '<tr><td width="18">';
		$content.= '<img src="'.$BACK_PATH.'gfx/icon_warning.gif" width="18" height="16" border="0" alt="" />';
		$content.= '</td><td colspan="'.$msg_colspan.'">';
		$content.= '<strong>'.$msg.'</strong>';
		$content.= '</td></tr>'."\n";
		return $content;
	}

	/**
	 * Generates the module content
	 */
	function renderModuleContent() {
		
		global $LANG;

		$awstats = GeneralUtility::makeInstance('tx_icsawstats_awstats');
		$logfilenames_arr = $this->getLogfileNames($this->id);

		$content = '<table border="0" cellspacing="0" cellpadding="1">'."\n";

		if ((count($logfilenames_arr) == 1) && (GeneralUtility::_GP('showaws') != 1)) {
			// TODO: Cleanup Code
			$t3log = $logfilenames_arr[0]['logfilename'];
			$content.= $this->displayLogfile($awstats, $t3log);
		}
		else if (GeneralUtility::_GP('showaws')) {
			$t3log = GeneralUtility::_GP('t3log');
			if ($t3log && $this->is_member_of_logfilenames_arr($logfilenames_arr, $t3log)) {
				$content.= $this->displayLogfile($awstats, $t3log);
			} else {
				$content.= $this->getNoteTr($LANG->getLL('no_logfile_selected_text'));
			}
		} else {
			$inst_content = $LANG->getLL('instruction_text');
			$this->content.= $this->doc->section($LANG->getLL('instruction_title').':', $inst_content, 0, 1);
			$this->content.= $this->doc->spacer(5);

			if (count($logfilenames_arr) > 0) {
				foreach ($logfilenames_arr as $logfiledata) {
					$t3log = $logfiledata['logfilename'];
					$logconfig = $awstats->get_single_logconfig($t3log);
					// delete update lock files
					if (GeneralUtility::_GP('rmlock') && (GeneralUtility::_GP('rmlock') == $t3log)) {
						if ($logconfig['browser_update']) {
							$awstats->unlink_update_lockfile($t3log);
						}
					}
					// output logfile
					if ($logconfig['type'] == tx_icsawstats_awstats::$LOGF_REGISTERED) {
						$url = $SERVER['PHP_SELF'].'?M=web_txicswebawstatsM1&id='.urlencode($this->id).'&showaws=1&t3log='.urlencode($t3log);
						$content .= '<tr><td width="18">';
						$content.= '<a href="'.htmlspecialchars($url).'" target="_blank">';
						$content.= '<img src="logfileicon.gif" width="18" height="16" alt="'.htmlspecialchars($LANG->getLL('open_stats_in_new_window')).'" title="'.htmlspecialchars($LANG->getLL('open_stats_in_new_window')).'" />';
						$content.= '</a>';
						$content.= '</td><td>';
						$content.= '<a href="'.htmlspecialchars($url).'">'.$t3log.'</a>';
						$content.= '</td><td style="width: 5px"></td><td>';
						if ($logfiledata['type'] && $logfiledata['type'] != '') {
							$content.= 'Type: '.$logfiledata['type'];
						}
						$content.= '</td><td style="width: 5px"></td><td>';
						if ($awstats->is_set_update_lockfile($t3log)) {
							$content.= $LANG->getLL('update_in_progress_text');
							if ($logconfig['browser_update']) {
								$rmlockurl = 'index.php?id='.urlencode($this->id).'&rmlock='.urlencode($t3log);
								$content.= ' (<a href="'.htmlspecialchars($rmlockurl).'">'.$LANG->getLL('delete_update_lockfile').'</a>)';
							}
						}
						$content.= '</td></tr>'."\n";
					} else {
						// Only prompt the warning if the logfile belongs not to a page type.
						if (! ($logfiledata['type'] && $logfiledata['type'] != '') ) {
							$content.= $this->getWarningTr(sprintf($LANG->getLL('no_config_text'), $t3log), 5);
						}
					}
				}
			} else {
				$content .= $this->getNoteTr($LANG->getLL('no_logfile_text'));
			}
		}
		$content.= '</table>';
		
		$this->content.= $content;
	}

	/**
	 * Displays the statistics for the given configuration.
	 *
	 * Notice: The code after the function call will be executed only if an error occured while
	 * executing awstats.
	 */
	protected function displayLogfile(tx_icsawstats_awstats $awstats, $logfilename) {

		global $LANG;

		$logconfig = $awstats->get_single_logconfig($logfilename);

		// Is it a registered logfile?
		if ($logconfig['type'] == tx_icsawstats_awstats::$LOGF_REGISTERED) {
			$aws_wrapper = BackendUtility::getModuleUrl('web_txicswebawstatsM1', array('showaws' => '1', 'id' => $this->id, 't3log' => $logfilename));
			$result = $awstats->call_awstats($logfilename, $aws_wrapper, 0);
			if (!is_numeric($result)) {
				return $result;
			}
			else {
				// the following will only be executed if an error occured in call_awstats
				// otherwise the script will die in call_awstats
				switch ($result)	{
					case tx_icsawstats_awstats::$ERR_LOGFILE_NOT_CONFIGURED:
						$content = $this->getWarningTr(sprintf($LANG->getLL('no_config_text'), $logfilename));
						break;
					case tx_icsawstats_awstats::$ERR_AWSTATS_CALL_FAILED:
						$content = $this->getWarningTr($LANG->getLL('awstats_failed_text'));
						break;
					case tx_icsawstats_awstats::$ERR_UPDATE_IS_LOCKED:
						$content = $this->getWarningTr($LANG->getLL('update_locked_text'));
						break;
					default:
						$content = $this->getWarningTr($LANG->getLL('unknown_error'));
				}
			}
		} else {
			$content = $this->getWarningTr(sprintf($LANG->getLL('no_config_text'), $logfilename));
		}

		return $content;

	} // End of Method: displayLogfile()

	/**
	 * Create the panel of buttons for submitting the form or otherwise
	 * perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	protected function getButtons() {
		$buttons = array(
			'csh' => '',
			'shortcut' => '',
			'save' => ''
		);
			// CSH
		$buttons['csh'] = BackendUtility::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']);

			// Shortcut
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$buttons['shortcut'] = $this->doc->makeShortcutIcon('id, showaws, t3log', 'function', $this->MCONF['name']);
		}

		return $buttons;
	}
	
} // End of class: tx_icswebawstats_module1



if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ics_web_awstats/mod1/index.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS']['XCLASS']['ext/ics_web_awstats/mod1/index.php']);
}




// Make instance:
$SOBE = GeneralUtility::makeInstance('tx_icswebawstats_module1');
$SOBE->main();
$SOBE->printContent();

?>