<?php

namespace app\modules\photosField\controllers;

/**
 * 
 */
class UploadController extends \app\components\Controller {


	/**
	 *
	 */
	public function actionAjax($name, $id, $type, $new = false) {
		if(!\Yii::app()->request->getIsPostRequest())
			throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		if(!empty($_FILES['app\models\fields']['name'][$name]))
			\app\modules\photosField\models\Upload::init($name, $id, $type, $new)->post();


		\Yii::app()->end();
	}


	public function actionSetMain($name, $id, $type, $new = false) {
		\app\modules\photosField\models\Upload::init($name, $id, $type, $new)->setMain();
		\Yii::app()->end();
	}


	public function actionDelete($name, $id, $type, $new = false) {
		\app\modules\photosField\models\Upload::init($name, $id, $type, $new)->delete();
		\Yii::app()->end();
	}


	public function actionUploaded($name, $id, $type, $new = false) {
		\app\modules\photosField\models\Upload::init($name, $id, $type, $new)->get();
		\Yii::app()->end();
	}


}