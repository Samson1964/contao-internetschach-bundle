<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2017 Leo Feyer
 *
 * @package   disam
 * @author    Frank Hoppe <webmaster@schachbund.de>
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @copyright Frank Hoppe 2014 - 2017
 */
/**
 * Table tl_internetschach
 */
$GLOBALS['TL_DCA']['tl_internetschach'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'                      => array('tl_internetschach_spieler'),
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id'             => 'primary'
			)
		)
	),
	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('jahr DESC', 'titel ASC'),
			'flag'                    => 12,
			'panelLayout'             => 'filter;search,sort,limit',
		),
		'label' => array
		(
			'fields'                  => array('jahr', 'titel'),
			'showColumns'             => true,
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif'
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_internetschach', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'reservation' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['reservation'],
				'href'                => 'table=tl_internetschach_anmeldungen',
				'icon'                => 'bundles/contaointernetschach/images/anmeldungen.png'
			),
			'players' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['players'],
				'href'                => 'table=tl_internetschach_spieler',
				'icon'                => 'bundles/contaointernetschach/images/players.png'
			),
			'import' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['import'],
				'href'                => 'key=importCSV',
				'icon'                => 'bundles/contaointernetschach/images/import.png',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_internetschach']['importConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			), 
			'export' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_internetschach']['export'],
				'href'                => 'key=exportXLS',
				'icon'                => 'bundles/contaointernetschach/images/export.png',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['tl_internetschach']['exportConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			), 

		)
	),
	// Palettes
	'palettes' => array
	(
		'__selector__'                => array(),
		'default'                     => '{internetschach_legend},titel,jahr;{config_legend},email;{gruppen_legend},gruppen;{turniere_legend},turniere;{publish_legend},published'
	),

	// Subpalettes
	'subpalettes' => array
	(
	), 

	// Base fields in table tl_internetschach
	'fields' => array
	(
		'id' => array
		(
			'search'                  => true,
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach']['tstamp'],
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		// Titel der Meisterschaft
		'titel' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach']['titel'],
			'inputType'               => 'text',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'filter'                  => false,
			'sql'                     => "varchar(255) NOT NULL default ''",
			'eval'                    => array
			(
				'mandatory'           => true,
				'maxlength'           => 255,
				'tl_class'            => 'w50'
			)
		),
		// Jahr
		'jahr' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach']['jahr'],
			'inputType'               => 'text',
			'default'                 => date('Y'),
			'exclude'                 => true,
			'sorting'                 => true,
			'eval'                    => array
			(
				'mandatory'           => true,
				'rgxp'                => 'digit', 
				'maxlength'           => 4,
				'tl_class'            => 'w50'
			),
			'sql'                     => "int(4) unsigned NOT NULL default '0'"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach']['email'],
			'inputType'               => 'textarea',
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => false,
			'filter'                  => false,
			'eval'                    => array
			(
				'cols'                => 80,
				'rows'                => 5,
				'style'               => 'height: 80px'
			),
			'sql'                     => "text NULL"
		),
		'gruppen' => array
		(
			'label'                   => $GLOBALS['TL_LANG']['tl_internetschach']['gruppen'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'columnFields'        => array
				(
					'name'            => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['gruppen_name'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array
						(
							'style'   => 'width:250px',
						),
					),
					'dwz_von'         => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['gruppen_dwz_von'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array
						(
							'style'     => 'width:180px',
							'maxlength' => 4
						),
					),
					'dwz_bis'         => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['gruppen_dwz_bis'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array
						(
							'style'     => 'width:180px',
							'maxlength' => 4
						),
					),
				),
			),
			'sql'                     => 'blob NULL',
		),
		'turniere' => array
		(
			'label'                   => $GLOBALS['TL_LANG']['tl_internetschach']['turniere'],
			'exclude'                 => true,
			'inputType'               => 'multiColumnWizard',
			'eval'                    => array
			(
				'columnFields'        => array
				(
					'name'            => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['turniere_name'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array
						(
							'style'   => 'width:250px',
						),
					),
					'feldname'        => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['turniere_feldname'],
						'exclude'     => true,
						'inputType'   => 'text',
						'eval'        => array
						(
							'style'   => 'width:180px'
						),
					),
					'finale'          => array
					(
						'label'       => $GLOBALS['TL_LANG']['tl_internetschach']['turniere_finale'],
						'exclude'     => true,
						'inputType'   => 'checkbox',
					),
				),
			),
			'sql'                     => 'blob NULL',
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_internetschach']['published'],
			'inputType'               => 'checkbox',
			'default'                 => true,
			'filter'                  => true,
			'exclude'                 => true,
			'eval'                    => array
			(
				'tl_class'            => 'w50',
				'isBoolean'           => true
			),
			'sql'                     => "char(1) NOT NULL default ''"
		),
	),
);

class tl_internetschach extends \Backend
{

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$this->import('BackendUser', 'User');

		if (strlen($this->Input->get('tid')))
		{
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 0));
			$this->redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach::published', 'alexf'))
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
		if (!$this->User->isAdmin && !$this->User->hasAccess('tl_internetschach::published', 'alexf'))
		{
			$this->log('Kein Zugriffsrecht für Aktivierung Datensatz ID "'.$intId.'"', 'tl_internetschach toggleVisibility', TL_ERROR);
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
		
		$this->createInitialVersion('tl_internetschach', $intId);
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_internetschach']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_internetschach']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnPublished = $this->$callback[0]->$callback[1]($blnPublished, $this);
			}
		}
		
		// Update the database
		$this->Database->prepare("UPDATE tl_internetschach SET tstamp=". time() .", published='" . ($blnPublished ? '' : '1') . "' WHERE id=?")
		               ->execute($intId);
		$this->createNewVersion('tl_internetschach', $intId);
	}

	/**
	 * Button zum Export der Anmeldungen
	 * @param DataContainer $dc
	 *
	 * @return string HTML-Code
	 */
	public function getExportAnmeldungen(DataContainer $dc)
	{

		// Zurücklink generieren, ab C4 ist das ein symbolischer Link zu "contao"
		if (version_compare(VERSION, '4.0', '>='))
		{
			$link = \System::getContainer()->get('router')->generate('contao_backend');
		}
		else
		{
			$link = 'contao/main.php';
		}
		$link .= '?do=disam&amp;key=exportXLS&amp;id=' . $dc->activeRecord->id . '&amp;rt=' . REQUEST_TOKEN;
		
		$string = '
<div class="w50 widget">
	<a href="'.$link.'">'.$GLOBALS['TL_LANG']['tl_internetschach']['export_anmeldungen'][0].'</a>
</div>'; 
		return $string;

	}

}