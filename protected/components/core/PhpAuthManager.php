<?php

namespace app\components\core;

/**
 * Добавлена авторизация пользователей
 */
class PhpAuthManager extends \CPhpAuthManager {


	/**
	 * Загрузка ролей из файла и присвоение роли пользователю
	 */
	public function init() {
		// иерархия ролей в protected/config/auth.php
		if($this->authFile === null)
			$this->authFile = \Yii::getPathOfAlias('app.config.auth') . '.php';

		parent::init();

		// Для гостей у нас и так роль по умолчанию guest.
		$user = \Yii::app()->user;

		if(!$user->getIsGuest()) {
			$role = \app\models\users\User::roleName();
			// назначение роли
			$this->assign($role[(int)$user->role], $user->id);
		}
	}


}