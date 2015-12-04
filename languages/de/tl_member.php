<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

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
 * @copyright  Michael Fleischmann 2012
 * @author     Michael Fleischmann <info@michael-fleischmann.com>
 * @package    login_link
 * @license    LGPL
 * @filesource
 */

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_member']['loginLink_legend'] 	= 'Login-Link Einstellungen';


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_member']['loginLink'] 				= array('Auth-Key (GET Parameter .html?key=xxx)','Anhängen an jede URL möglich! z.B. /index.html?key=L3Tm3lN<br>Key kann auch als InsertTag verwendet werden <br><b>z.B. ?key={{user::loginLink}} oder ?key={{loginKey}}</b>');
$GLOBALS['TL_LANG']['tl_member']['loginLinkExpire']	= array('Login per Link erlauben bis:','Bis zum angegebenen Zeitpunkt den Login per Link erlauben. Normaler Login ist danach auch weiterhin möglich!');
$GLOBALS['TL_LANG']['tl_member']['loginLinkGen']		= array('Auth-Key generieren?','Achtung: Wird der Haken gesetzt, wird der Key überschrieben, auch wenn bereits einer drin steht!');
$GLOBALS['TL_LANG']['tl_member']['jumpTo']	        		= array('Weiterleitungsseite', 'Bitte wählen Sie die Seite aus, zu der Besucher nach dem LinkLogin weitergeleitet werden.');
