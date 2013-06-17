<?php

namespace app\modules\passField\controllers;

/**
 * Пользователи
 */
class ResetController extends \app\components\Controller {


	/**
	 * Вывод страницы ввода кода для сброса пароля пользователя
	 * @param string $code 
	 */
	public function actionIndex($code = null) {
		if(!\Yii::app()->user->getIsGuest()) $this->redirect(array('/site/objects/index'));

		$model = new \app\modules\passField\models\ResetCodeForm;

		if(\Yii::app()->request->getRequestType() == 'GET' && $code) {
			$model->code = $code;
			if($model->validate()) {
				// получения настроек полей пользователя для страницы "pass"
				$user = new \app\models\users\User;
				$user->user(0, 'passReset');
				$this->render('reset', array('model' => $user, 'code' => $model->code));


				return;
			}
		}
		elseif(isset($_POST['app\modules\passField\models\ResetCodeForm'])) {
			$model->attributes = $_POST['app\modules\passField\models\ResetCodeForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate()) {
				// получения настроек полей пользователя для страницы "pass"
				$user = new \app\models\users\User;
				$user->user(0, 'passReset');
				$this->render('reset', array('model' => $user, 'code' => $model->code));


				return;
			}
		}


		$this->render('resetCode', array('model' => $model, 'code' => $model->code));
	}


	/**
	 * Вывод страницы для ввода нового пароля
	 * Выводится после ввода кода сброса пароля
	 */
	public function actionReset() {
		if(!\Yii::app()->user->getIsGuest()) $this->redirect(array('/site/objects/index'));

		$code = \Yii::app()->request->getParam('code');

		$model = new \app\modules\passField\models\ResetCodeForm;
		$model->code = $code;
		if(!$model->validate()) {
			$this->render('resetNo');
			return;
		}

		$user = new \app\models\users\User;
		// получения настроек полей пользователя для страницы "pass"
		$find = $user->user($model->user, 'passReset');
		if(!$find) throw new \CHttpException(500, \Yii::t('main', 'ERROR_NOT_RESET_PASSWORD'));

		// collect user input data
		if(isset($_POST['app\models\fields'])) {
			$user->getManager()->fieldsAttributes($_POST['app\models\fields']);
			if($model->reset($user))
				$this->redirect(array('reset/resetOk'));
		}
            

		$this->render('reset', array('model' => $user, 'code' => $code));
	}


	public function actionResetOk() {
		$this->render('resetOk');
	}


}