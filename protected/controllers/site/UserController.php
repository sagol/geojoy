<?php

namespace app\controllers\site;

/**
 * Пользователи
 */
class UserController extends \app\components\Controller {


	/**
	 * Событие по умолчанию
	 * @var string 
	 */
	public $defaultAction = 'objects';


	/**
	 * Конфигурирование фильтров контроллера
	 * @return array 
	 */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}


	/**
	 * Конфигурирование правил проверки доступа к событиям контроллера
	 * @return array 
	 */
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('setNewMailNo', 'setNewMail', 'sendMailActivation'),
				'users' => array('*'),
			),

			array('allow',
				'actions' => array('login', 'registration', 'activation', 'activationok', 'registered', 'recovery'),
				'users' => array('?'),
			),

			array('allow',
				'actions' => array('multiAccountWithdraw', 'objects', 'profile', 'edit', 'multiAccount', 'multiAccountCode', 'multiAccountOk', 'settings', 'logout'),
				'roles' => array('authorized'),
			),

			array('allow',
				'actions' => array('delete'),
				'roles' => array('moder'),
			),

			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


	/**
	 * Вывод страницы пользователя
	 * @param integer $page 
	 */
	public function actionObjects($page = 1) {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		// получение кол-ва для пагинации

		if(\Yii::app()->user->checkAccess('companyUser'))
			$queryParams = array(
				'needCount' => true,
				'skipFields' => true,
				'criteria' => array(
					'idusers' => \Yii::app()->user->id,
					// пользователь должен выдеть все свои объявления
					// '!=spam' => 2, и те, что в спаме
					// '!=moderate' => 1, и те, что в на модерации
					// 'disabled' => 0, и те, что в отключенной категории
					// и те, у которых вышел срок жизни
					// 'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
				),
			);
		else 
			$queryParams = array(
				'needCount' => true,
				'skipFields' => true,
				'criteria' => array(
					'multiuser' => \Yii::app()->user->multiUser,
					// пользователь должен выдеть все свои объявления
					// '!=spam' => 2, и те, что в спаме
					// '!=moderate' => 1, и те, что в на модерации
					// 'disabled' => 0, и те, что в отключенной категории
					// и те, у которых вышел срок жизни
					// 'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
				),
			);

		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) $this->pages = array(
			'count' => ceil($objectsCount/$objectsOnPage),
			'active' => $page,
			'url' => array('/site/user/objects'),
		);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData);

		if(empty($objects)) $this->render('objectsNo');
		else {
			$paramsObjectsShowType = \Yii::app()->params['objectsShowType'];
			if($paramsObjectsShowType == 1 && \Yii::app()->request->isAjaxRequest) {
				$json['url'] = '';
				if($objectsCount > $objectsOnPage) {
					if($this->pages['count'] > $this->pages['active']) {
						$params = \app\components\widgets\Pages::pageUrl($this->pages['active']+1, $this->pages['url']);
						$url = $params[0];
						unset($params[0]);
						$json['url'] = $this->createAbsoluteUrl($url, $params);
					}
				}

				$json['status'] = 'ok';
				$json['html'] = $this->renderPartial('objects', array('objects' => $objects, 'notShowTabs' => true), true);
				echo json_encode($json);
				\Yii::app()->end();
			}
			else $this->render('objects', array('objects' => $objects));
		}
	}


	/**
	 * Вывод страницы профиля пользователя
	 * @param integer $id 
	 */
	public function actionProfile($id = null) {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		$model = new \app\models\users\User;

		$user = \Yii::app()->user;
		if($user->getIsGuest() && $id === null) $this->redirect($user->loginUrl);
		elseif($id === null) $id = (int)$user->id;

		if($model->user($id)) {
			if($model->field(\app\models\users\User::NAME_SOCIAL_INFO_VISIBLE)->getValue()) $socialAccounts = \app\models\users\MultiAccount::userSocialAccounts();
			else $socialAccounts = array();
			$this->render('profile', array('model' => $model, 'socialAccounts' => $socialAccounts));
		}
		else $this->render('profileNo');
	}


	/**
	 * Вывод страницы профиля пользователя
	 */
	public function actionEdit() {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		$model = new \app\models\users\User;

		$user = (int)\Yii::app()->user->id;

		$find = $model->user($user, 'form');

		$url = $this->createUrl('/site/user/edit');
		$extData = array('url' => $url, 'adv' => $user);
		$model->getManager()->fieldsEventProcessing($this->id, $this->action->id, $extData);

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'profile-form-edit') {
			echo $model->ajaxValidate();
			\Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields']);
			// validate user input and redirect to the previous page if valid
			if($model->save($user))
				$this->redirect(array('/site/user/profile'));
		}

		if($find)
			$this->render('edit', array('model' => $model));
		else $this->render('profileNo');
	}


	/**
	 * Вывод формы логина
	 */
	public function actionLogin() {
		$model = new \app\models\users\LoginForm;

		$service = \Yii::app()->request->getQuery('service');

		if(!empty($service)) {
			$authIdentity = \Yii::app()->eauth->getIdentity($service);
			$authIdentity->redirectUrl = \Yii::app()->params['pageAfterLogin'];
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('/site/user/login');

			if ($authIdentity->authenticate()) {
				$identity = new \app\components\ServiceUserIdentity($authIdentity);

				// Успешный вход в соцсеть
				if ($identity->authenticate()) {

					// проверка на первую регистрацию или выполнение регистрации
					if(!$model->loginSocial($identity)) {
						\Yii::app()->session->add('registrationSocialIdentity', $identity);
						if($model->hasErrors()) {
							\Yii::app()->session->add('loginSocialError', array(
								'code' => $model->getErrorCode(),
								'error' => $model->getErrors('loginSocial'),
							));
							
							$authIdentity->redirectUrl = $this->createAbsoluteUrl('/site/user/login');
							// Специальный редирект с закрытием popup окна
							$authIdentity->redirect();
						}

						$authIdentity->redirectUrl = $this->createAbsoluteUrl('/site/user/registered');
					}

					// Специальный редирект с закрытием popup окна
					$authIdentity->redirect();
				}
				else {
					// Закрываем popup окно и перенаправляем на cancelUrl
					$authIdentity->cancel();
				}
			}
			// перенаправить на страницу ошибки
			// Что-то пошло не так, перенаправляем на страницу входа
			$this->redirect(array('/site/user/login'));
		}
		else {
			// if it is ajax validation request
			if(isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
				echo \CActiveForm::validate($model);
				\Yii::app()->end();
			}

			// collect user input data
			if(isset($_POST['app\models\users\LoginForm'])) {
				$model->attributes = $_POST['app\models\users\LoginForm'];
				// validate user input and redirect to the previous page if valid
				if($model->validate() && $model->login())
					$this->redirect(\Yii::app()->user->getReturnUrl(\Yii::app()->params['pageAfterLogin']));
			}
		}

		$this->leftMenu = array(
			array('label' => \Yii::t('nav', 'NAV_REGISTRATION'), 'url' => array('/site/user/registration')),
			array('label' => \Yii::t('nav', 'NAV_RECOVERY_PASSWORD'), 'url' => array('/password/recovery')),
		);

		$loginSocialError = \Yii::app()->session->get('loginSocialError');
		if(!empty($loginSocialError)) {
			$errorCode = $loginSocialError['code'];
			$loginSocialError = (array)$loginSocialError['error'];
		}
		else {
			$loginSocialError = array();
			$errorCode = $model->getErrorCode();
		}

		// display the login form
		$this->render('login', array(
			'model' => $model,
			'errorCode' => $errorCode,
			'socialError' => implode('<br>', $loginSocialError),
		));
	}


	/**
	 * Выход пользователя
	 */
	public function actionLogout() {
		\Yii::app()->user->logout();
		$this->redirect(\Yii::app()->params['pageAfterLogin']);
	}


	/**
	 * Форма регистрации пользователя
	 */
	public function actionRegistration() {
		$profile = \Yii::app()->request->getParam('profile', \app\models\users\User::PROFILE_USER);
		$profileUser = new \app\models\users\User;
		$profileUser->user(0, 'reg', \app\models\users\User::PROFILE_USER);

		$profileCompany = new \app\models\users\User;
		$profileCompany->user(0, 'reg', \app\models\users\User::PROFILE_COMPANY);

		$service = \Yii::app()->request->getQuery('service');

		if(!empty($service)) {
			$authIdentity = \Yii::app()->eauth->getIdentity($service);
			$authIdentity->redirectUrl = \Yii::app()->params['pageAfterSocialRegistration'];
			$authIdentity->cancelUrl = $this->createAbsoluteUrl('/site/user/registration');

			if ($authIdentity->authenticate()) {
				$identity = new \app\components\ServiceUserIdentity($authIdentity);

				// Успешный вход в соцсеть
				if ($identity->authenticate()) {
					// проверка на первую регистрацию или выполнение регистрации
					if(!\app\models\users\LoginForm::loginSocial($identity)) {
						\Yii::app()->session->add('registrationSocialIdentity', $identity);
						$authIdentity->redirectUrl = $this->createAbsoluteUrl('/site/user/registered');
					}

					// Специальный редирект с закрытием popup окна
					$authIdentity->redirect();
				}
				else {
					// Закрываем popup окно и перенаправляем на cancelUrl
					$authIdentity->cancel();
				}
			}

			// перенаправить на страницу ошибки
			// Что-то пошло не так, перенаправляем на страницу входа
			$this->redirect(array('/site/user/registration'));
		}
		else {
			if($profile == 1) $model = & $profileUser;
			elseif($profile == 2) $model = & $profileCompany;

			// if it is ajax validation request
			if(isset($_POST['ajax']) && ($_POST['ajax'] === 'registration-form-user' || $_POST['ajax'] === 'registration-form-company')) {
				echo $model->ajaxValidate();
				\Yii::app()->end();
			}

			// collect user input data
			if(isset($_POST['app\models\fields'])) {
				$model->getManager()->fieldsAttributes($_POST['app\models\fields']);

				if($model->registration())
					$this->redirect(array('/site/user/activation'));
			}
		}

		$this->leftMenu = array(
			array('label' => \Yii::t('nav', 'NAV_LOGIN'), 'url' => array('/site/user/login')),
			array('label' => \Yii::t('nav', 'NAV_RECOVERY_PASSWORD'), 'url' => array('/password/recovery')),
		);

		$this->render('registration', array(
			'profileUser' => $profileUser,
			'profileCompany' => $profileCompany,
			'profile' => $profile,
		));
	}


	/**
	 * Форма активации аккаунта пользователя
	 * @param string $code 
	 */
	public function actionActivation($code = null) {
		$model = new \app\models\users\ActivationForm;

		if(\Yii::app()->request->getRequestType() == 'GET' && $code) {
			$model->code = $code;
			if($model->validate() && $model->activation())
				$this->redirect(array('/site/user/activationok'));
		}
		elseif(isset($_POST['app\models\users\ActivationForm'])) {
			$model->attributes = $_POST['app\models\users\ActivationForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->activation()) {
				// удаляем из сессии инфу регистраци через соц сеть
				\Yii::app()->session->remove('registrationSocialIdentity');
				$this->redirect(array('/site/user/activationok'));
			}
		}

		$this->render('activation', array('model' => $model));
	}


	public function actionSendMailActivation() {
		$user = \Yii::app()->session->get('userNotActivated');
		if(!empty($user['id']) && $user['id'] && \app\models\users\User::sendMailActivation($user))
			$this->redirect(array('/site/user/activation'));
		else $this->render('activationNo');
	}


	public function actionSetNewMail() {
		$identity = \Yii::app()->session->get('registrationSocialIdentity');
		if(!($identity instanceof \app\components\ServiceUserIdentity))
			$this->redirect(array('/site/user/login'));

		$loginSocialError = \Yii::app()->session->get('loginSocialError');
		if(empty($loginSocialError) || $loginSocialError['code'] != \app\components\UserIdentity::ERROR_YOU_NOT_ACTIVATED)
			$this->redirect(array('/site/user/login'));

		$user = \Yii::app()->session->get('userNotActivated');
		if(empty($user) && !$user['id']) $this->redirect(array('/site/user/login'));


		$model = new \app\models\users\User;
		$find = $model->user($user['id'], 'regSocialSetMail');

		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields']);

			if($model->registrationSocialSetNewMail($user['id']))
				$this->redirect(array('/site/user/activation'));
		}

		$this->render('registered', array('model' => $model, 'action' => array('/site/user/setNewMail')));
	}


	/**
	 * Вывод сообщения об успешной активации
	 */
	public function actionActivationOk() {
		$this->render('activationok');
	}


	/* TODO вроде не используется
	public function actionSetNewMailNo() {
		$this->render('setNewMailNo');
	}*/


	/**
	 * Форма подтверждения регистрации через соцсети, выводится после успешного входа через соцсеть
	 * с аккаунта не зарегистрированного на сайте
	 */
	public function actionRegistered() {
		$identity = \Yii::app()->session->get('registrationSocialIdentity');

		if($identity instanceof \app\components\ServiceUserIdentity) {

			$model = new \app\models\users\User;
			$find = $model->user(0, 'regSocial');

			if(isset($_POST['app\models\fields'])) {
				$model->getManager()->fieldsAttributes($_POST['app\models\fields']);

				if($model->registrationSocial($identity))
					$this->redirect(array('/site/user/activation'));
			}

			$this->render('registered', array('model' => $model, 'action' => array('/site/user/registered')));
		}
		else $this->redirect(array('/site/user/registration'));
	}


	/**
	 * Восстановления пароля
	 * @return boolean 
	 */
	public function actionRecovery() {
		$model = new \app\models\users\RecoveryPasswordForm;

		$this->leftMenu = array(
			array('label' => \Yii::t('nav', 'NAV_LOGIN'), 'url' => array('/site/user/login')),
			array('label' => \Yii::t('nav', 'NAV_REGISTRATION'), 'url' => array('/site/user/registration')),
		);

		if(isset($_POST['app\models\users\RecoveryPasswordForm'])) {
			$model->attributes = $_POST['app\models\users\RecoveryPasswordForm'];
			if($model->validate()) {
				if($model->recovery()) $this->redirect(array('/passField/reset/index'));
				else $this->render('recoveryNo');

				return true;
			}
		}


		$this->render('recovery', array('model' => $model));
	}


	/**
	 * Создание мульти акка
	 */
	public function actionMultiAccount() {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		$model = new \app\models\users\MultiAccount;

		if(isset($_POST['app\models\users\MultiAccount'])) {
			$model->attributes = $_POST['app\models\users\MultiAccount'];
			if($model->validate())
				if($model->createMultiAccount()) $this->redirect(array('/site/user/multiAccountOk'));
		}

		$code = $model->multiAccountCode();
		$accounts = $model->userAccounts();

		$this->render('multiAccount', array(
			'model' => $model,
			'code' => $code,
			'accounts' => $accounts,
		));
	}


	/**
	 * Отсоединение от мульти акка
	 */
	public function actionMultiAccountWithdraw($id) {
		$model = new \app\models\users\MultiAccount;
		$model->withdraw($id);

		$this->redirect(array('/site/user/multiAccount'));
	}


	/**
	 * Успешное создание мульти акка
	 */
	public function actionMultiAccountOk() {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		$this->render('multiAccountOk');
	}


	/**
	 * Генерация кода подтвреждения для создание мульти акка
	 */
	public function actionMultiAccountCode() {
		if(\Yii::app()->user->getIsGuest()) {
			echo \Yii::t('nav', 'NEED_LOGIN');
			\Yii::app()->end();
		}

		if(\Yii::app()->request->isAjaxRequest) {
			echo \app\models\users\MultiAccount::multiAccountCreateCode();
			\Yii::app()->end();
		}

		$this->redirect(array('/site/objects/index'));
	}

	/**
	 * 
	 */
	public function actionSettings() {
		$this->breadcrumbs += array(
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		$model = new \app\models\users\SettingsForm;

		if(isset($_POST['app\models\users\SettingsForm'])) {
			$model->attributes = $_POST['app\models\users\SettingsForm'];

			if($model->validate() && $model->save()) {
				\Yii::app()->message->add(\Yii::t('user', 'OPTIONS_SAVES'));
				$this->redirect(array('/site/user/settings'));
			}
			else {echo 'error'; die;}
		}

		$model->load();
		$this->render('settings', array(
			'model' => $model,
		));
	}
	/**
	 * Удаление пользователя
	 * @param integer $id 
	 */
	public function actionDelete($id) {
		\app\models\users\User::delete($id);
	}


}