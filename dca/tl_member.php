<?php if (!defined('TL_ROOT')) {
	die('You cannot access this file directly!');
}

/**
 *
 *
 * @copyright  Michael Fleischmann 2016
 * @author     Michael Fleischmann <info@michael-fleischmann.com>
 * @package    login_link
 * @license    LGPL
 * @filesource
 */


/**
 * Table tl_member
 */

$GLOBALS['TL_DCA']['tl_member']['palettes'] = str_replace('{account_legend}', '{loginLink_legend},loginLink,loginLinkExpire,loginLinkGen,jumpTo;{account_legend}', $GLOBALS['TL_DCA']['tl_member']['palettes']);

array_insert($GLOBALS['TL_DCA']['tl_member']['fields'],count($GLOBALS['TL_DCA']['tl_member']['fields']),array
(
	'loginLink'         => array
	(
		'label'     	=>  &$GLOBALS['TL_LANG']['tl_member']['loginLink'],
		'inputType' => 'text',
		'eval'      	=> array('minlength'=> 5, 'unique' => true, 'tl_class'=>'w50'),
		'sql'		=> "varchar(255) NOT NULL default ''"
	),
	'loginLinkExpire'   => array
	(
		'label'     	=> &$GLOBALS['TL_LANG']['tl_member']['loginLinkExpire'],
		'inputType' => 'text',
		'eval'      	=> array('rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'),
		'sql'		=> "varchar(10) NOT NULL default ''"
	),
	'loginLinkGen'   => array
	(
		'label'     	=> &$GLOBALS['TL_LANG']['tl_member']['loginLinkGen'],
		'inputType' => 'checkbox',
		'eval'      	=> array('tl_class'=>'clr', 'submitOnChange'=>true),
		'save_callback' => array
			(
				array('tl_loginLink','loginLinkGen')
			),
		'sql' 	=> "int(1) unsigned NOT NULL default '0'"
	),
	'jumpTo'    => array
	(
		'label'			=> &$GLOBALS['TL_LANG']['tl_member']['jumpTo'],
		'inputType'		=> 'pageTree',
		'eval'			=> array('fieldType'=>'radio', 'tl_class'=>'long'),
		'sql'			=>	"int(10) unsigned NOT NULL default '0'"
	),
));

class tl_loginLink extends Backend
{
	protected $authKey = '';

	public function loginLinkGen($varValue, DataContainer $dc)
	{
		if($varValue)
		{
			\Database::getInstance()->prepare('UPDATE tl_member SET loginLink = ? WHERE id = ?')->execute(LoginLink::generateLoginKey(), $dc->id);
		}
		return 0; // reset checkbox
	}
}