<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package News
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Table tl_internetschach_spieler
 */
$GLOBALS['TL_DCA']['tl_internetschach_spieler'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ptable'                      => 'tl_internetschach',
		'switchToEdit'                => true, 
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'            => 'primary',
				'name'          => 'index'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('name'),
			'flag'                    => 3,
			'headerFields'            => array('titel'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_internetschach_spieler', 'listSpieler')
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s'
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				//'button_callback'     => array('tl_internetschach_spieler', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_internetschach_spieler', 'deleteArchive')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_internetschach_spieler', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'copyToAnmeldung' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['copyToAnmeldung'],
				'href'                => 'key=copyToAnmeldung',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_internetschach_spieler']['copyToAnmeldung_confirm'] . '\'))return false;Backend.getScrollOffset()"',
				'icon'                => 'bundles/contaointernetschach/images/copy.png'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{name_legend},name,geschlecht,geburtsjahr;{verein_legend:hide},zps,verein,mglnr,status,spielberechtigung;{dwz_legend:hide},dwz;{fide_legend:hide},fideElo,fideTitel,fideID,fideNation;{publish_legend},published'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'search'                  => true,
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_internetschach.titel',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type'=>'belongsTo', 'load'=>'eager')
		),
		'tstamp' => array
		(
			'sorting'                 => true,
			'flag'                    => 1,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'zps' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['zps'],
			'exclude'                 => true,
			'filter'                  => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 5,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(5) NOT NULL default ''"
		),
		'mglnr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['mglnr'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 4,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'verein' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['verein'],
			'exclude'                 => true,
			'search'                  => true,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 80,
				'tl_class'            => 'w50'
			),
			'sql'                     => "varchar(80) NOT NULL default ''"
		),
		'status' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['status'],
			'exclude'                 => true,
			'filter'                  => true,
			'search'                  => false,
			'sorting'                 => false,
			'flag'                    => 1,
			'default'                 => 'A',
			'inputType'               => 'select',
			'options'                 => array
			(
				'A'                   => 'Aktiv',
				'P'                   => 'Passiv'
			),
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 1, 
				'tl_class'            => 'w50',
				'includeBlankOption'  => true,
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 80,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(80) NOT NULL default ''"
		),
		'geschlecht' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['geschlecht'],
			'exclude'                 => true,
			'search'                  => false,
			'filter'                  => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'inputType'               => 'select',
			'options'                 => array
			(
				'M'                   => 'Männlich',
				'W'                   => 'Weiblich',
			),
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 1, 
				'tl_class'            => 'w50',
				'includeBlankOption'  => true,
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'spielberechtigung' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['spielberechtigung'],
			'exclude'                 => true,
			'search'                  => false,
			'filter'                  => true,
			'sorting'                 => false,
			'flag'                    => 1,
			'default'                 => 'D',
			'inputType'               => 'select',
			'options'                 => array
			(
				'D'                   => 'Deutsche/r',
				'E'                   => 'EU-Ausländer/in',
				'A'                   => 'Ausländer/in',
				'G'                   => 'Gleichgestellte/r',
			),
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 1, 
				'tl_class'            => 'w50',
				'includeBlankOption'  => true,
			),
			'sql'                     => "varchar(1) NOT NULL default ''"
		),
		'geburtsjahr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['geburtsjahr'],
			'exclude'                 => true,
			'search'                  => false,
			'filter'                  => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 4,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'dwz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['dwz'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 4,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'fideElo' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['fideElo'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'maxlength'           => 4,
				'tl_class'            => 'w50 clr',
				'rgxp'                => 'alnum'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'fideTitel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['fideTitel'],
			'exclude'                 => true,
			'search'                  => false,
			'filter'                  => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 3, 
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(3) NOT NULL default ''"
		),
		'fideID' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['fideID'],
			'exclude'                 => true,
			'search'                  => false,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 12, 
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(12) NOT NULL default ''"
		),
		'fideNation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['fideNation'],
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'search'                  => false,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false, 
				'maxlength'           => 3, 
				'tl_class'            => 'w50',
			),
			'sql'                     => "varchar(3) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_spieler']['published'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);


/**
 * Class tl_internetschach_spieler
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_internetschach_spieler extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_spieler::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;id='.$this->Input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ';
	}

	public function toggleVisibility($intId, $blnPublished)
	{
		// Check permissions to publish
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_spieler::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_internetschach_spieler toggleVisibility', TL_ERROR);
			// Zurücklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
			if (version_compare(VERSION, '4.0', '>='))
			{
				$backlink = \System::getContainer()->get('router')->generate('contao_backend');
			}
			else
			{
				$backlink = 'contao/main.php';
			}
			$this->redirect($backlink.'?act=error');
		}
		
		$this->createInitialVersion('tl_internetschach_spieler', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_internetschach_spieler']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_internetschach_spieler']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_internetschach_spieler SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_internetschach_spieler', $intId);
	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listSpieler($arrRow)
	{ 
		$temp = $arrRow['name'];
		if($arrRow['geburtsjahr']) $temp .= ' (*'.$arrRow['geburtsjahr'].')';
		if($arrRow['dwz']) $temp .= ' DWZ '.$arrRow['dwz'];
		if($arrRow['status']) $temp .= ' - '.$arrRow['status'];
		if($arrRow['verein']) $temp .= ' | '.$arrRow['verein'];
		return $temp;
	}

}