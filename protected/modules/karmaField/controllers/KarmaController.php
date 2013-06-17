<?php

namespace app\modules\karmaField\controllers;

/**
 * Карма
 */
class KarmaController extends \app\components\Controller {


	public function actionIndex() {
		$id = (int)$_POST['id'];
		$user = new \app\models\users\User;
		$find = $user->user($id, 'karmaForm');
		$karma = $user->field(\app\models\users\User::NAME_KARMA);

		if($karma->show()) {
			$model = new \app\modules\karmaField\models\KarmaForm;
			if(isset($_POST['KarmaForm']))
				$model->attributes = $_POST['KarmaForm'];

			$model->idusers = $id;
			$model->voted = \Yii::app()->user->id;

			if($model->validate()) {
				if($model->voting()) echo 'ok';
				else echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'KARMA_ERROR_VOTING');
			}
			else echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'KARMA_ERROR_INCORRECT_DATA');
		}
		else echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'KARMA_ERROR_YOU_CAN_NOT_VOTE');

		\Yii::app()->end();
	}


}