<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 *
 * @copyright  Michael Fleischmann 2012
 * @author     Michael Fleischmann <info@michael-fleischmann.com>
 * @package    login_link
 * @license    LGPL
 * @filesource
 */


/**
 * Add palette
 */

/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{login_link_legend},login_link_autoKey,login_link_defaultKeyLength,login_link_useDefaultExpireTime,login_link_generateKeysForAllMembers';


array_insert($GLOBALS['TL_DCA']['tl_settings'],count($GLOBALS['TL_DCA']['tl_settings']),array
(
	// PALETTES
	'palettes'	=> array
	(
		'__selector__'      => array
		(
			'login_link_useDefaultExpireTime',
		),
	),

	// SUBPALETTES
	'subpalettes'   => array
	(
		'login_link_useDefaultExpireTime'       			=> 'login_link_defaultExpireTime',
	),

	// FIELDS
	'fields'	=> array
	(
		'login_link_autoKey' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['login_link_autoKey'],
			'inputType'        => 'select',
			'options'		=> array('','onActivateAccount','onCreateNewUser'),
			'reference'		=> $GLOBALS['TL_LANG']['tl_settings']['login_link_autoKey_ref'],
			'eval'                    => array('tl_class'=>'clr')
		),
		'login_link_defaultKeyLength' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['login_link_defaultKeyLength'],
			'default'		=> '15',
			'inputType'        => 'select',
			'options'		=> range(10,50),
			'eval'                    => array('tl_class'=>'clr')
		),
		'login_link_useDefaultExpireTime' => array
		(
			'label'                   		=> &$GLOBALS['TL_LANG']['tl_settings']['login_link_useDefaultExpireTime'],
			'inputType'			=> 'checkbox',
			'eval'				=> array('submitOnChange'=>true, 'tl_class'=>'clr')
		),
		'login_link_defaultExpireTime' => array
		(
			'label'                   		=> &$GLOBALS['TL_LANG']['tl_settings']['login_link_defaultExpireTime'],
			'inputType'			=> 'timePeriod',
			'options'			=> array('m','h','d','w','M'),
			'reference'			=> &$GLOBALS['TL_LANG']['tl_settings']['intervalReference'],
			'save_callback'		=> array(array('tl_login_link_settings','saveInterval')),
			'load_callback'		=> array(array('tl_login_link_settings','loadInterval')),
			'eval'				=> array('rgxp'=>'digit')
		),
		'login_link_generateKeysForAllMembers' => array
		(
			  'label'                   	=> &$GLOBALS['TL_LANG']['tl_settings']['login_link_generateKeysForAllMembers'],
			  'inputType'		=> 'checkbox',
			  'eval'				=> array('submitOnChange'=>true, 'tl_class'=>'clr'),
			  'save_callback' 	=> array
			  (
				    array('tl_login_link_settings','generateKeysForAllMembers')
			  ),
		),
	)
));


class tl_login_link_settings extends System
{

	public function generateKeysForAllMembers($intField)
	{
		if(!$intField)
			return '';

		$objMembers = \Database::getInstance()->execute("SELECT * FROM tl_member WHERE loginLink = ''");

		while($objMembers->next())
		{
			\Database::getInstance()->prepare("UPDATE tl_member %s WHERE id = ?")->set
			(
				  array
				(
					'loginLink'	=> \LoginLink::generateLoginKey(),
				)
			)->execute($objMembers->id);
		}

		$_SESSION['TL_INFO'][] = sprintf('LoginKeys für %s Mitglieder generiert und gespeichert!',$objMembers->numRows);
		\Config::persist('login_link_generateKeysForAllMembers','');
	}

	public function saveInterval($val,$dc)
	{
		$val = unserialize($val);
		switch($val['unit'])
		{
		case 'm':
			return $val['value'] * 60;
			break;

		case 'h':
			return $val['value'] * 60 * 60;
			break;

		case 'd':
			return $val['value'] * 60 * 60 * 24;
			break;

		case 'w':
			return $val['value'] * 60 * 60 * 24 * 7;
			break;

		case 'M':
			return $val['value'] * 60 * 60 * 24 * 30;
			break;

		default:
			throw new Exception('Please type a integer-number.');
			break;
		}
	}

	public function loadInterval($val,$dc)
	{
		if(fmod($val,(60 * 60 * 24 * 30)) == 0)
		{
			$unit = 'M';
			$value = $val / (60 * 60 * 24 * 30);
		}
		elseif(fmod($val,(60 * 60 * 24 * 7)) == 0)
		{
			$unit = 'w';
			$value = $val / (60 * 60 * 24 * 7);
		}
		elseif(fmod($val,(60 * 60 * 24)) == 0)
		{
			$unit = 'd';
			$value = $val / (60 * 60 * 24);
		}
		elseif(fmod($val,(60 * 60)) == 0)
		{
			$unit = 'h';
			$value = $val / (60 * 60);
		}
		else
		{
			$unit = 'm';
			$value = $val / 60;
		}

		return serialize(array('value'=>$value,'unit'=>$unit));

	}
}