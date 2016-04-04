<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * @copyright 	Michael Fleischmann 2016
 * @author 		Michael Fleischmann <info@michael-fleischmann.com>
 * @package 	login_link
 * @license 		LGPL
 * @filesource
 */

class LoginLink extends \Frontend
{
	protected $authKey = null;
	protected $strHash = '';

	public function __construct()
	{
		$this->authKey = \Input::get('key');
		if(!$this->authKey)
			return;
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

		$objMemberModel = \MemberModel::findById($objMember->id);


		if($objMember->numRows != 1 || $objMember->loginLink != $this->authKey)
			return;


		if(!FE_USER_LOGGED_IN)
		{
			// Generate the cookie hash
			$this->strHash = sha1(session_id() . (!\Config::get('disableIpCheck') ? \Environment::get('ip') : '') . 'FE_USER_AUTH');
			// Clean up old sessions
			\Database::getInstance()->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")->execute(($time - \Config::get('sessionTimeout')), $this->strHash);
			// Save the session in the database
			\Database::getInstance()->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
				 ->execute($objMember->id, $time, 'FE_USER_AUTH', session_id(), \Environment::get('ip'), $this->strHash);
			// Set the authentication cookie
			\System::setCookie('FE_USER_AUTH', $this->strHash, ($time + \Config::get('sessionTimeout')), null, null, false, true);

			// Set the login status (backwards compatibility)
			$_SESSION['TL_USER_LOGGED_IN'] = true;

			// Save the login status
			$_SESSION['TL_USER_LOGGED_IN'] = true;

			\System::log('User "' . $objMember->username . '" logged in by authKey', 'LoginLink()', TL_ACCESS);
			\Controller::reload();
		}


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


	public function createNewUser($intId, $arrData)
	{
		if(\Config::get('login_link_autoKey') == 'onCreateNewUser')
		{
			$memberModel = \MemberModel::findById($intId);
			$memberModel->loginLink = self::generateLoginKey();

				// Auto ExpireTime
			if(\Config::get('login_link_useDefaultExpireTime') && \Config::get('login_link_defaultExpireTime'))
			{
				$intExpireTime = time()+\Config::get('login_link_defaultExpireTime');
				$memberModel->loginLinkExpire = $intExpireTime;
			}

			$memberModel->save();
		}
	}



	/**
	 * @param Database_Result $objUser
	 */
	public function activateAccount($memberModel, $registrationModule)
	{

		// Auto AutoKey
		if(\Config::get('login_link_autoKey') == 'onActivateAccount')
		{
			$memberModel = \MemberModel::findById($memberModel->id);
			if(!$memberModel->loginLink)
				$memberModel->loginLink = self::generateLoginKey();

			// Auto ExpireTime
			if(\Config::get('login_link_useDefaultExpireTime') && \Config::get('login_link_defaultExpireTime'))
			{
				$intExpireTime = time()+\Config::get('login_link_defaultExpireTime');
				$memberModel->loginLinkExpire = $intExpireTime;
			}

			$memberModel->save();
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


	public static function generateLoginKey($intKeyLength=false)
	{
		if(!$intKeyLength || !is_int($intKeyLength))
			$intKeyLength = \Config::get('login_link_defaultKeyLength') ? \Config::get('login_link_defaultKeyLength') : 10;

		$strKey = substr(sha1(uniqid(mt_rand()).uniqid(mt_rand())),0,$intKeyLength);
		$objMember = \Database::getInstance()->prepare("SELECT id FROM tl_member WHERE loginLink = ?")->execute($strKey);

		if($objMember->numRows)
			LoginLink::generateLoginKey();

		return $strKey;
	}

}