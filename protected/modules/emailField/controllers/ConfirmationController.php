<?php

namespace app\modules\emailField\controllers;

/**
 * Пользователи
 */
class ConfirmationController extends \app\components\Controller {


	public function actionIndex($code = null) {
		if(!\Yii::app()->user->getIsGuest()) $this->redirect(array('/site/objects/index'));

		$model = new \app\modules\emailField\models\ConfirmationEmailForm;

		if(\Yii::app()->request->getRequestType() == 'GET' && $code) {
			$model->code = $code;
			if($model->validate() && $model->confirmation())
				$this->redirect(array('confirmation/confirmationOk'));
		}
		elseif(isset($_POST['app\modules\emailField\models\ConfirmationEmailForm'])) {
			$model->attributes = $_POST['app\modules\emailField\models\ConfirmationEmailForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->confirmation())
				$this->redirect(array('confirmation/confirmationOk'));
		}

		$this->render('confirmation', array('model' => $model));
	}


	public function actionConfirmationOk() {
		$this->render('confirmationOk');
	}


}