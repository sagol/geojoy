<?php

namespace app\modules\avatarField\controllers;

/**
 * 
 */
class UploadController extends \app\components\Controller {


	/**
	 *
	 */
	public function actionAjax($name) {
		if(!\Yii::app()->request->getIsPostRequest())
			throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$fieldName = str_replace(array('..', '\\', '/'), '', $name);
		if(!empty($_FILES['app\models\fields']['name'][$fieldName]['files'])) {
			$_FILES['app\models\fields']['name'][$fieldName] = $_FILES['app\models\fields']['name'][$fieldName]['files'];
			$_FILES['app\models\fields']['type'][$fieldName] = $_FILES['app\models\fields']['type'][$fieldName]['files'];
			$_FILES['app\models\fields']['tmp_name'][$fieldName] = $_FILES['app\models\fields']['tmp_name'][$fieldName]['files'];
			$_FILES['app\models\fields']['error'][$fieldName] = $_FILES['app\models\fields']['error'][$fieldName]['files'];
			$_FILES['app\models\fields']['size'][$fieldName] = $_FILES['app\models\fields']['size'][$fieldName]['files'];

			$uploadAvatar = $this->uploadAvatar($fieldName, $uploadDir, $uploadUrl);
			$uploadAvatar->post();
		}

		\Yii::app()->end();
	}


	public function actionDelete($name) {
		$fieldName = str_replace(array('..', '\\', '/'), '', $name);
		$this->uploadAvatar($fieldName)->delete();
		\Yii::app()->end();
	}


	public function actionUploaded($name) {
		$fieldName = str_replace(array('..', '\\', '/'), '', $name);
		$this->uploadAvatar($fieldName)->get();
		\Yii::app()->end();
	}

	protected function uploadAvatar($fieldName, &$uploadDir = null, &$uploadUrl = null) {
		$userId = \Yii::app()->user->id;
		if(!$userId)
			throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		$uploadUrl = \Yii::app()->params['uploadUrl'] . '/' . 'fields';

		$uploadDir = $uploadDir . DS . $userId . DS . $fieldName . DS;
		$uploadUrl = $uploadUrl . '/' . $userId . '/' . $fieldName . '/';

		if(!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
		if(!file_exists($uploadDir . 'thumbnails' . DS)) mkdir($uploadDir . 'thumbnails' . DS, 0777, true);

		$size = \Yii::app()->params['avatarSize'];

		$uploadAvatar = new \app\modules\avatarField\models\UploadAvatar(array(
			'delete_type' => 'GET',
			// урл удаления авы
			'script_url' => '/avatarField/upload/delete/name/' . $fieldName . '.html',
			'upload_dir' => $uploadDir,
			// урл к картинке
			'upload_url' => $uploadUrl,
			'param_name' => $fieldName,
			'accept_file_types' => '/(\.|\/)(gif|jpe?g|png)$/i',
			'max_number_of_files' => 1,
		));


		return $uploadAvatar;
	}


}