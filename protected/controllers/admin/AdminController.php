<?php

namespace app\controllers\admin;

/**
 * Дефолтный контроллер админки
 */
class AdminController extends \app\components\AdminController {


	/**
	 * Конфигурирование фильтров контроллера
	 * @return array 
	 */
	public function filters() {
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}


	/**
	 * Конфигурирование правил проверки доступа к событиям контроллера
	 * @return array 
	 */
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('index'),
				'roles' => array('admin', 'moder'),
			),

			array('allow',
				'actions' => array('error'),
				'users' => array('*'),
			),

			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


	/**
	 * Вывод главной страницы админки
	 * @param integer $page 
	 */
	public function actionIndex($page = 1) {
		$this->redirect(array('admin/object/objects/moderate'));
	}


	/**
	 * Вывод ошибки
	 */
	public function actionError() {
	    if($error = \Yii::app()->errorHandler->error) {
	    	if(\Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}


}