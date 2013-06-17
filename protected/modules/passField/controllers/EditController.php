<?php

namespace app\modules\passField\controllers;

/**
 * Пользователи
 */
class EditController extends \app\components\Controller {


	/**
	 * Вывод страницы ввода кода для сброса пароля пользователя
	 * @param string $code 
	 */
	public function actionIndex() {
		if(\Yii::app()->user->getIsGuest()) $this->redirect(\Yii::app()->user->loginUrl);

		$model = new \app\modules\passField\models\EditPass;

		$user = new \app\models\users\User;
		// получения настроек полей пользователя для страницы "pass"
		$find = $user->user(\Yii::app()->user->id, 'passEdit');

		if(isset($_POST['app\models\fields'])) {
			$user->getManager()->fieldsAttributes($_POST['app\models\fields']);

			if($model->edit($user))
				$this->redirect(array('edit/editOk'));
		}


		if($find) $this->render('edit', array('model' => $user));
		else throw new \CHttpException(403, \Yii::t('main', 'ERROR_NOT_PERMISSION'));
	}


	public function actionEditOk() {
		$this->render('editOk');
	}


}