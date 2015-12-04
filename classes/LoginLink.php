<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * @copyright  Michael Fleischmann 2015
 * @author     Michael Fleischmann <info@michael-fleischmann.com>
 * @package    login_link
 * @license    LGPL
 * @filesource
 */

class LoginLink extends \Frontend
{
	protected $authKey = null;
	protected $strHash = '';

	public function __construct()
	{
		$this->authKey = \Input::get('key');
		if(!$this->authKey || FE_USER_LOGGED_IN) return;
	}


	/**
	 * @param Database_Result $objPage
	 * @param Database_Result $objLayout
	 * @param PageRegular $objPageRegular
	 */
	public function login($objPage, $objLayout, $objPageRegular)
	{

		$time = time();
		$objMember = \Database::getInstance()->prepare("SELECT * FROM tl_member WHERE loginLink = ? AND (loginLinkExpire > ? OR loginLinkExpire = '')")->execute($this->authKey,$time);


		if($objMember->numRows != 1 || $objMember->loginLink != $this->authKey) return;

		// Generate the cookie hash
		$this->strHash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? \Environment::get('ip') : '') . 'FE_USER_AUTH');

		// Clean up old sessions
		\Database::getInstance()->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")->execute(($time - $GLOBALS['TL_CONFIG']['sessionTimeout']), $this->strHash);

		// Save the session in the database
		\Database::getInstance()->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
			->execute($objMember->id, $time, 'FE_USER_AUTH', session_id(), \Environment::get('ip'), $this->strHash);

		// Set the authentication cookie
		$this->setCookie('FE_USER_AUTH', $this->strHash, ($time + $GLOBALS['TL_CONFIG']['sessionTimeout']), $GLOBALS['TL_CONFIG']['websitePath']);

		// Save the login status
		$_SESSION['TL_USER_LOGGED_IN'] = true;

		\System::log('User "' . $objMember->username . '" was logged by authKey', 'LoginLink()', TL_ACCESS);



		if($objMember->jumpTo)
			$objPage = \PageModel::findByPk($objMember->jumpTo);


		$strUrl = \Controller::generateFrontendUrl($objPage->row());

		$strParam = '';
		foreach($_GET as $index => $value)
		{
			if($index == 'key')
				continue;

			if(!$strParam):
				$strParam .= '?' . $index . '=' . \Input::get($index);
			else:
				$strParam .= '&' . $index . '=' . \Input::get($index);
			endif;
		}

		\Controller::redirect($strUrl . $strParam);
	}


	/**
	 * @param Database_Result $objUser
	 */
	public function activateAccount($objUser)
	{
		// Default keylength: 10
		$intKeyLength = $GLOBALS['TL_CONFIG']['login_link_defaultKeyLength'];

		// Auto AutoKey
		if($GLOBALS['TL_CONFIG']['login_link_autoKey'])
		{
			$strKey = substr(base64_encode(uniqid(mt_rand()).uniqid(mt_rand())),0,$intKeyLength);
			\Database::getInstance()->prepare('UPDATE tl_member SET loginLink = ? WHERE id = ?')->execute($strKey, $objUser->id);
		}

		// Auto ExpireTime
		if($GLOBALS['TL_CONFIG']['login_link_useDefaultExpireTime'] && $GLOBALS['TL_CONFIG']['login_link_defaultExpireTime'])
		{
			$intExpireTime = time()+$GLOBALS['TL_CONFIG']['login_link_defaultExpireTime'];
			\Database::getInstance()->prepare('UPDATE tl_member SET loginLinkExpire = ? WHERE id = ?')->execute($intExpireTime, $objUser->id);
		}
	}


	/**
	 * @param $strTag
	 */
	public function replaceInsertTagsLoginLink($strTag)
	{
		switch($strTag)
		{
			case 'loginKey':
				return \FrontendUser::getInstance()->loginLink;
			break;
		}

		return false;
	}
}