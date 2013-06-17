<?php

namespace app\modules\passField\models;

/**
 * Форма сброса пароля пользователя
 */
class EditPass {


	public function edit($model) {
		if($model->getManager()->fieldsValidate()) {
			// сохраняем поля
			$model->getManager()->fieldsUpdate();
			\Yii::app()->message->add($model->getManager()->fieldsInfo());
			return true;
		}
		else return false;
	}


}