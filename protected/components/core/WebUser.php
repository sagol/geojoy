<?php

namespace app\components\core;

/**
 * Добавлено роль пользователя, название роли пользователя, id мульти акка, логирование выхода
 */
class WebUser extends \CWebUser {


	public function loginRequired() {
		$app = \Yii::app();
		$request = $app->getRequest();

		if(!$request->getIsAjaxRequest()) {
			$baseUrl = $request->getBaseUrl();
			if($baseUrl && $app->getLanguage() != $app->getDefaultLanguage()) {
				$url = substr($request->getUrl(), strlen($baseUrl));
				$url = $baseUrl . '/' . $app->getLanguage() .  $url;
				$this->setReturnUrl($url);
			}
			else $this->setReturnUrl($request->getUrl());
		}
		elseif(isset($this->loginRequiredAjaxResponse))
		{
			echo $this->loginRequiredAjaxResponse;
			$app->end();
		}

		if(($url=$this->loginUrl)!==null)
		{
			if(is_array($url))
			{
				$route=isset($url[0]) ? $url[0] : $app->defaultController;
				$url=$app->createUrl($route,array_splice($url,1));
			}
			$request->redirect($url);
		}
		else
			throw new CHttpException(403, Yii::t('yii','Login Required'));
	}


	public function init() {
		parent::init();

		if(!$this->getIsGuest()) {
			$setting = $this->getSetting();
			if($setting[\app\models\users\User::SETTINGS_SHOW_PAGE])
				\Yii::app()->params['objectsShowType'] = $setting[\app\models\users\User::SETTINGS_SHOW_PAGE];
			if($setting[\app\models\users\User::SETTINGS_SHOW_PAGE_COUNT])
				\Yii::app()->params['objectsOnPage'] = \app\models\users\User::getSettingsShowPageCountArray($setting[\app\models\users\User::SETTINGS_SHOW_PAGE_COUNT]);

			// формируем всплывающие новости для пользователя
			\app\modules\news\models\News::setFlashNews($this);
		}
	}


	public function getAccount() {
		return $this->getState('__account');
	}


	public function setAccount($value) {
		$this->setState('__account', $value);
	}


	public function getSetting($value = null) {
		if($value === null) return $this->getState('__setting');
		else {
			$setting = $this->getState('__setting');
			if(isset($setting[$value])) return $setting[$value];
			else return null;
		}
	}


	public function setSetting($value) {
		$this->setState('__setting', $value);
	}


	public function getUrlSocial() {
		return $this->getState('__urlSocial');
	}


	public function setUrlSocial($value) {
		$this->setState('__urlSocial', $value);
	}


	public function getSocialInfo() {
		return $this->getState('__socialInfo');
	}


	public function setSocialInfo($value) {
		$this->setState('__socialInfo', $value);
	}


	public function getEmail() {
		return $this->getState('__email');
	}


	public function setEmail($value) {
		$this->setState('__email', $value);
	}


	public function getLanguage() {
		return $this->getState('__language');
	}


	public function setLanguage($value) {
		$this->setState('__language', $value);
	}


	/**
	 * Возвращает роль пользователя
	 * @return integer 
	 */
	public function getRole() {
		return $this->getState('__role');
	}


	/**
	 * Задание роли пользователя
	 * @param integer $value 
	 */
	public function setRole($value) {
		$this->setState('__role', $value);
	}


	/**
	 * Возвращает id мульти акка пользователя
	 * @return integer 
	 */
	public function getMultiUser() {
		return $this->getState('__multiUser');
	}


	/**
	 * Задание id мульти акка пользователя
	 * @param integer $value 
	 */
	public function setMultiUser($value) {
		$this->setState('__multiUser', $value);
	}


	/**
	 * Возвращает название роли пользователя
	 * @return string 
	 */
	public function getRoleName() {
		$roles = \app\models\users\User::roleName();
		$role = $this->getRole();


		return @$roles[$role];
	}


	/**
	 * Добавлено для логирования выхода пользователя
	 * @return boolean 
	 */
	public function beforeLogout() {
		$user = \Yii::app()->user;
		$user = \CHtml::link("$user->id($user->name)", array('/site/user/profile', 'id' => $user->id));
		\Yii::app()->appLog->user('EXIT_USER_TITLE', 'EXIT_USER', array('{user}' => $user));


		return true;
	}


	public function afterLogin($fromCookie) {
		$language = $this->getLanguage();
		if(!empty($language) && $language != \Yii::app()->getLanguage()) {
			if(\Yii::app()->getCurrentRoute() == 'site/user/login') {
				\Yii::app()->setLanguage($language);
				$baseUrl = \Yii::app()->request->getBaseUrl();
				$returnUrl = $this->getState('__returnUrl');
				if($baseUrl && ($f = strpos($returnUrl, $baseUrl) === 0)) $returnUrl = substr($returnUrl, strlen($baseUrl));
				if($returnUrl) {
					if(!empty(\Yii::app()->params['lang'])) {
						foreach(\Yii::app()->params['lang'] as $lang) {
							$strlen = strlen($lang)+1;
							$len[$strlen] = $strlen;
						}
						ksort($len);

						foreach($len as $ln) {
							if(@$returnUrl[$ln] == '/') {
								$lang = substr($returnUrl, 1, $ln-1);

								if(@\Yii::app()->params['lang'][$lang]) $returnUrl = substr($returnUrl, strlen($lang)+1);
							}
						}
					}

					if($language != \Yii::app()->getDefaultLanguage()) $this->setState('__returnUrl', "$baseUrl/$language$returnUrl");
					else $this->setState('__returnUrl', "$baseUrl$returnUrl");
				}
			}
			else {
				$lang = $language != \Yii::app()->getDefaultLanguage() ? "/$language" : '';
				$request = \Yii::app()->request;
				$url = $request->getBaseUrl(true) . $lang . $request->getRequestUri();
				$request->redirect($url);
			}
		}
	}


}