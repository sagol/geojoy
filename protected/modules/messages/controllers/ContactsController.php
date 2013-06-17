<?php

namespace app\modules\messages\controllers;

/**
 * 
 */
class ContactsController extends \app\components\Controller {


	/**
	 * Конфигурирование фильтров контроллера
	 * @return array 
	 */
	public function filters() {
		return array(
			'accessControl',
		);
	}


	/**
	 * Конфигурирование правил проверки доступа к событиям контроллера
	 * @return array 
	 */
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('index', 'del',),
				'roles' => array('authorized'),
			),

			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


	/**
	 * 
	 */
	public function actionIndex($page = 1) {
		$model = new \app\modules\messages\models\Contacts;


		$this->render('contacts', array(
			'filter' => 'contacts',
			'contacts' => $model->getContacts($page),
			'model' => $model,
		));
	}


	public function actionDel() {
		$contacts = $_POST['contacts'];
		$model = new \app\modules\messages\models\Contacts;
		echo $model->del($contacts);
		\Yii::app()->end();
	}


}