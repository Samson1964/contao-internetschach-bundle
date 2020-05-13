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
 * Table tl_internetschach_tabellen
 */
$GLOBALS['TL_DCA']['tl_internetschach_tabellen'] = array
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
			'child_record_callback'   => array('tl_internetschach_tabellen', 'listSpieler')
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s',
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
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				//'button_callback'     => array('tl_internetschach_tabellen', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_internetschach_tabellen', 'deleteArchive')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_internetschach_tabellen', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{name_legend},name,geschlecht,geburtsjahr;{contact_legend:hide},email;{turniere_legend:hide},turniere,gruppe;{chessbase_legend:hide},chessbase;{verein_legend:hide},verein;{dwz_legend:hide},dwz;{fide_legend:hide},fideElo,fideTitel,fideID,fideNation;{info_legend:hide},bemerkungen,intern,checked;{publish_legend},published'
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
		// Registrierungsdatum
		'registerDate' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['registerDate'],
			'flag'                    => 8,
			'sorting'                 => true,
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Verknüpfung zu tl_internetschach_spieler
		'playerId' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['playerId'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'verein' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['verein'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 80,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(80) NOT NULL default ''"
		),
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['name'],
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['geschlecht'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
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
		'geburtsjahr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['geburtsjahr'],
			'exclude'                 => true,
			'search'                  => true,
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
		'turniere' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['turniere'],
			'exclude'                 => true,
			'options_callback'        => array('tl_internetschach_tabellen', 'getTurniere'),
			'inputType'               => 'checkboxWizard',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => true,
				'tl_class'            => 'w50'
			),
			'sql'                     => "blob NULL"
		),
		'gruppe' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['gruppe'],
			'exclude'                 => true,
			'options_callback'        => array('tl_internetschach_tabellen', 'getGruppen'),
			'inputType'               => 'radio',
			'eval'                    => array
			(
				'mandatory'           => false,
				'multiple'            => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "blob NULL"
		),
		'chessbase' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['chessbase'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['email'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => false,
				'maxlength'           => 255,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'dwz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['dwz'],
			'exclude'                 => true,
			'search'                  => true,
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['fideElo'],
			'exclude'                 => true,
			'search'                  => true,
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
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['fideTitel'],
			'exclude'                 => true,
			'search'                  => false,
			'sorting'                 => true,
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
		'bemerkungen' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['bemerkungen'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'explanation'             => 'insertTags', 
			'sql'                     => "text NULL"
		),
		'intern' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['intern'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'explanation'             => 'insertTags', 
			'sql'                     => "text NULL"
		),
		'checked' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['checked'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['published'],
			'inputType'               => 'checkbox',
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);


/**
 * Class tl_internetschach_tabellen
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_internetschach_tabellen extends Backend
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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_tabellen::published', 'alexf'))
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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_tabellen::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_internetschach_tabellen toggleVisibility', TL_ERROR);
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
		
		$this->createInitialVersion('tl_internetschach_tabellen', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_internetschach_tabellen']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_internetschach_tabellen']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_internetschach_tabellen SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_internetschach_tabellen', $intId);
	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listSpieler($arrRow)
	{
		if($arrRow['checked']) $container = '<span style="color:#00791F">';
		else $container = '<span style="color:#970000">';

		$temp = $container;
		$temp .= $arrRow['name'] ? $arrRow['name'] : '- ohne Name -';
		if($arrRow['geburtsjahr']) $temp .= ' (*'.$arrRow['geburtsjahr'].')';
		if($arrRow['dwz']) $temp .= ' DWZ '.$arrRow['dwz'];
		if($arrRow['verein']) $temp .= ' | '.$arrRow['verein'];
		return $temp.'</span>';
	}

	public function getTurniere(DataContainer $dc)
	{
		$array = array();
		$objTurniere = $this->Database->prepare("SELECT turniere FROM tl_internetschach WHERE id = ?")->execute($dc->activeRecord->pid);
		if($objTurniere->numRows)
		{
			$temp = unserialize($objTurniere->turniere);
			if($temp)
			{
				foreach($temp as $item)
				{
					$array[$item['feldname']] = $item['name'];
				}
			}
		}
		return $array;
	}

	public function getGruppen(DataContainer $dc)
	{
		$array = array();
		$objGruppen = $this->Database->prepare("SELECT gruppen FROM tl_internetschach WHERE id = ?")->execute($dc->activeRecord->pid);
		if($objGruppen->numRows)
		{
			$temp = unserialize($objGruppen->gruppen);
			if($temp)
			{
				foreach($temp as $item)
				{
					$array[$item['feldname']] = $item['name'].' (DWZ '.$item['dwz_von'].' - '.$item['dwz_bis'].')';
				}
			}
		}
		return $array;
	}
}