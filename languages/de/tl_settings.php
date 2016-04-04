<?php

// LEGENDS
$GLOBALS['TL_LANG']['tl_settings']['login_link_legend']	= 'Login_Link Einstellungen';


// FIELDS
$GLOBALS['TL_LANG']['tl_settings']['intervalReference']						= array('m' => 'Minuten','h' => 'Stunden', 'd' => 'Tage', 'w' => 'Wochen', 'M' => 'Monate');
$GLOBALS['TL_LANG']['tl_settings']['login_link_defaultKeyLength']				= array('Standard Key-Zeichenlänge','Gilt nur durch das System erstellte Keys');
$GLOBALS['TL_LANG']['tl_settings']['login_link_autoKey']						= array('Aktiv bei Mitgliederaktivierung','Jedes Mitglied erhält automatisch einen generierten Key zugewiesen');
$GLOBALS['TL_LANG']['tl_settings']['login_link_useDefaultExpireTime']		= array('Aktiviere Gültigkeitsdauer je Key','');
$GLOBALS['TL_LANG']['tl_settings']['login_link_defaultExpireTime']			= array('Standardgültigkeit je Key','Wird bei Aktivierung dem Mitglied hinterlegt');
$GLOBALS['TL_LANG']['tl_settings']['login_link_generateKeysForAllMembers']	= array('Generiere Key für alle Mitglieder (die noch keinen Key haben)','');


// Reference

$GLOBALS['TL_LANG']['tl_settings']['login_link_autoKey_ref']	= array
(
		''						=> 'Keine Aktion ausführen',
	  	'onActivateAccount'	=> 'Bei Aktivierung (per Link)',
	  	'onCreateNewUser'		=> 'Immer bei neuem User'
);