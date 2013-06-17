<?php

namespace app\modules\avatarField\controllers;

/**
 * 
 */
class WebcamController extends \app\components\Controller {


	/**
	 *
	 */
	public function actionUpload($name) {
		if(!\Yii::app()->request->getIsPostRequest()) {
			echo '{
				"error": 1,
				"message": "' . \Yii::t('main', 'ERROR_INVALID_REQUEST') . '"
			}';
			\Yii::app()->end();
		}

		$fieldName = str_replace(array('..', '\\', '/'), '', $name);
		$input = file_get_contents('php://input');
		$name = 'webcam_tmp_' . microtime(true);
		$userId = \Yii::app()->user->id;

		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		$uploadUrl = \Yii::app()->params['uploadUrl'] . '/' . 'fields';

		$fileName = $uploadDir . DS . $userId . DS . $fieldName . DS . $name;
		$url = $uploadUrl . '/' . $userId . '/' . $fieldName . '/' . $name;

		foreach(glob($uploadDir . DS . $userId . DS . $fieldName . DS . 'webcam_tmp_*') as $name)
			unlink($name);

		$result = file_put_contents($fileName, $input);
		if(!$result) {
			echo '{
				"error": 1,
				"message": "' . \Yii::t('main', 'ERROR_SAVE_IMAGE') . '"
			}';
			\Yii::app()->end();
		}

		$info = getimagesize($fileName);
		if($info['mime'] != 'image/jpeg'){
			unlink($fileName);
			echo '{
				"error": 1,
				"message": "' . \Yii::t('main', 'ERROR_UPLOAD_NOT_JPG') . '"
			}';
			\Yii::app()->end();
		}

		echo '{
			"error": 0,
			"message": "' . \Yii::t('app\modules\avatarField\AvatarFieldModule.fields', 'IMAGE_SAVED') . '",
			"name": "' . $url . '"
		}';
		\Yii::app()->end();
	}


}