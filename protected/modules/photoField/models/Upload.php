<?php

namespace app\modules\photoField\models;

class Upload {


	static function init($fieldName, $id, $table, $new) {
		$userId = \Yii::app()->user->id;
		if(!$userId)
			throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$fieldName = str_replace(array('..', '\\', '/'), '', $fieldName);

		$baseUrl = \Yii::app()->request->getBaseUrl();
		$uploadDir = \Yii::getPathOfAlias(\Yii::app()->params['uploadDir']) . DS . 'fields';
		$uploadUrl = $baseUrl . \Yii::app()->params['uploadUrl'] . '/' . 'fields';
		if($new) {
			$uploadDir = $uploadDir . DS . 'tmp' . DS . $userId . DS . $fieldName . DS;
			$uploadUrl = $uploadUrl . '/tmp/' . $userId . '/' . $fieldName . '/';
		}
		else {
			$uploadDir = $uploadDir . DS . $userId . DS . $fieldName . DS;
			$uploadUrl = $uploadUrl . '/' . $userId . '/' . $fieldName . '/';
		}

		if(!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
		if(!file_exists($uploadDir . 'thumbnails' . DS)) mkdir($uploadDir. 'thumbnails' . DS, 0777, true);


		$uploadPhoto = new \app\modules\photoField\models\UploadPhoto(array(
			'delete_type' => 'GET',
			// урл удаления авы
			'script_url' => "$baseUrl/photoField/upload/delete/name/$fieldName/id/$id/type/$table.html",
			'upload_dir' => $uploadDir,
			// урл к картинке
			'upload_url' => $uploadUrl,
			'param_name' => $fieldName,
			'accept_file_types' => '/(\.|\/)(gif|jpe?g|png)$/i',
			'max_number_of_files' => \Yii::app()->getController()->getModule()->maxUploadFiles,
			'image_versions' => array(
				'thumbnail' => array(
					'upload_dir' => $uploadDir . 'thumbnails' . DS,
					'upload_url' => $uploadUrl . 'thumbnails/',
					'max_width' => 280,
					'max_height' => 210,
				),
			),
		));

		$id = 'A' . $id;
		$uploadPhoto->id = $id;
		$uploadPhoto->table = $table;
		$uploadPhoto->name = $fieldName;

		$session = \Yii::app()->session;
		$uploadFiles = $session->get('uploadFiles' . $table);
		if(!empty($uploadFiles[$id][$fieldName])) $uploadPhoto->files = $uploadFiles[$id][$fieldName];


		return $uploadPhoto;
	}


}