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
		'onload_callback'             => array
		(
			array('tl_internetschach_tabellen', 'getTurniereGruppen')
		),
		'sql' => array
		(
			'keys' => array
			(
				'id'            => 'primary',
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 4,
			'fields'                  => array('turnier'),
			'flag'                    => 3,
			'headerFields'            => array('titel'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_internetschach_tabellen', 'listTabellen')
		),
		'label' => array
		(
			'fields'                  => array('turnier'),
			'format'                  => '%s',
			'group_callback'          => array('tl_internetschach_preise', 'getGroup')
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
		'default'                     => '{turniere_legend},turnier,gruppe;{daten_legend},csv,disqualifikation,ungewertet;{leitung_legend:hide},turnierleiter;{info_legend:hide},intern;{publish_legend},published'
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
		'turnier' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['turnier'],
			'exclude'                 => true,
			'options_callback'        => array('tl_internetschach_tabellen', 'getTurniere'),
			'inputType'               => 'radio',
			'flag'                    => 11,
			'eval'                    => array
			(
				'mandatory'           => true,
				'multiple'            => false,
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
			'flag'                    => 11,
			'eval'                    => array
			(
				'mandatory'           => true,
				'multiple'            => false,
				'tl_class'            => 'w50'
			),
			'sql'                     => "blob NULL"
		),
		'csv' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['csv'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'eval'                    => array
			(
				'allowHtml'           => false,
				'class'               => 'monospace',
				'rows'                => 30,
				'rte'                 => 'ace',
				'helpwizard'          => true
			),
			'explanation'             => 'csv',
			'sql'                     => "mediumtext NULL",
		),
		'disqualifikation' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['disqualifikation'],
			'inputType'               => 'text',
			'eval'                    => array
			(
				'tl_class'            => 'long',
				'maxlength'           => 255,
				'helpwizard'          => true,
			),
			'explanation'             => 'disqualifikation',
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'ungewertet' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['ungewertet'],
			'inputType'               => 'text',
			'eval'                    => array
			(
				'tl_class'            => 'long',
				'maxlength'           => 255,
				'helpwizard'          => true,
			),
			'explanation'             => 'ungewertet',
			'sql'                     => "varchar(255) NOT NULL default ''",
		),
		'turnierleiter' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_tabellen']['turnierleiter'],
			'inputType'               => 'text',
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'maxlength'           => 40,
			),
			'sql'                     => "varchar(40) NOT NULL default ''",
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
	public function listTabellen($arrRow)
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

	function getTurniereGruppen(DataContainer $dc)
	{
		// Turniere und Gruppen zuordnen
		$objSerie = \Database::getInstance()->prepare("SELECT * FROM tl_internetschach WHERE id = ?")
		                                    ->execute(\Input::get('id'));

		if($objSerie->numRows)
		{
			$turniere = unserialize($objSerie->turniere);
			if($turniere)
			{
				foreach($turniere as $item)
				{
					$this->turniere[$item['feldname']] = $item['name'];
				}
			}
			$gruppen = unserialize($objSerie->gruppen);
			if($gruppen)
			{
				foreach($gruppen as $item)
				{
					$this->gruppen[$item['feldname']] = $item['name'].' (DWZ '.$item['dwz_von'].' - '.$item['dwz_bis'].')';
				}
			}
		}
	}

	public function getGroup($group, $mode, $field, $row)
	{
		// Do something
		//$newLabel = $this->turniere[$row['turnier']];
		//echo "Feld: $field / ";
		return $group;
	}
}