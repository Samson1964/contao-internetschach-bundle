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
 * Table tl_internetschach_preise
 */
$GLOBALS['TL_DCA']['tl_internetschach_preise'] = array
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
			array('tl_internetschach_preise', 'getTurniereGruppen')
		),
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
			'fields'                  => array('turnier', 'gruppe'),
			'flag'                    => 11,
			'headerFields'            => array('titel'),
			'panelLayout'             => 'filter;sort;search,limit',
			'child_record_callback'   => array('tl_internetschach_preise', 'listPreise')
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'format'                  => '%s',
			'group_callback'          => array('tl_internetschach_preise', 'getGroup')
		),
		'global_operations' => array
		(
			'exportPreiseXLS' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['exportPreiseXLS'],
				'href'                => 'key=exportPreiseXLS',
				'icon'                => 'bundles/contaointernetschach/images/export.png',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['tl_internetschach_preise']['exportPreiseXLS_confirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
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
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				//'button_callback'     => array('tl_internetschach_preise', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				//'button_callback'     => array('tl_internetschach_preise', 'deleteArchive')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_internetschach_preise', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => '{name_legend},name;{preis_legend},platz,dwz_grenze;{turniere_legend},turnier,gruppe;{info_legend:hide},intern;{publish_legend},published'
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
		// Name des Preises
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['name'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 80,
				'tl_class'            => 'long'
			),
			'sql'                     => "varchar(80) NOT NULL default ''"
		),
		// Platz, der den Preis bekommt
		'platz' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['platz'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 11,
			'inputType'               => 'text',
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 4,
				'tl_class'            => 'w50',
				'rgxp'                => 'alnum'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		// Bedingung für Preisverleihung, DWZ unter xxxx
		'dwz_grenze' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['dwz_grenze'],
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
		// Turnier, für den der Preis gilt
		'turnier' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['turnier'],
			'exclude'                 => true,
			'options_callback'        => array('tl_internetschach_preise', 'getTurniere'),
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
		// Gruppe, für die der Preis gilt
		'gruppe' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['gruppe'],
			'exclude'                 => true,
			'options_callback'        => array('tl_internetschach_preise', 'getGruppen'),
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
		'intern' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['intern'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'textarea',
			'explanation'             => 'insertTags', 
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach_preise']['published'],
			'inputType'               => 'checkbox',
			'default'                 => 1,
			'filter'                  => true,
			'eval'                    => array('tl_class' => 'w50','isBoolean' => true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	)
);


/**
 * Class tl_internetschach_preise
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2014
 * @author     Leo Feyer <https://contao.org>
 * @package    News
 */
class tl_internetschach_preise extends Backend
{

	var $turniere = array();
	var $gruppen = array();

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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_preise::published', 'alexf'))
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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach_preise::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_internetschach_preise toggleVisibility', TL_ERROR);
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
		
		$this->createInitialVersion('tl_internetschach_preise', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_internetschach_preise']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_internetschach_preise']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_internetschach_preise SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_internetschach_preise', $intId);
	}

	/**
	 * Datensätze auflisten
	 * @param array
	 * @return string
	 */
	public function listPreise($arrRow)
	{
		$temp = $this->turniere[$arrRow['turnier']];
		$temp .= ', '.$this->gruppen[$arrRow['gruppe']];
		$temp .= ', '.($arrRow['dwz_grenze'] ? ' DWZ < '.$arrRow['dwz_grenze'] : 'alle Spieler');
		$temp .= ' | <i>'.$arrRow['platz'].'. Platz</i>';
		$temp .= ' | <b>'.$arrRow['name'].'</b>';
		return $temp;
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