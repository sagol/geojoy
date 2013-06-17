<?php

namespace app\controllers\admin;

/**
 * Логи
 */
class LogsController extends \app\components\AdminController {


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
				'actions' => array('index', 'view'),
				'roles' => array('moder'),
			),
			array('allow',
				'actions' => array('delete'),
				'roles' => array('admin'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}


	/**
	 * Выполняется перед любым событием
	 * @param string $action
	 * @return boolean 
	 */
	public function beforeAction($action) {
		$this->breadcrumbs += array(
			\Yii::t('admin', 'BREADCRUMBS_LOGS') => array('admin/logs'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_LOGS');


		return true;
	}


	/**
	 * Детальный вывод
	 * @param integer $id 
	 */
	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id),
		));
	}


	/**
	 * Удаление
	 * @param integer $id
	 * @throws \CHttpException если не POST запрос
	 */
	public function actionDelete($id) {
		if(\Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
		}
		else
			throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));
	}


	/**
	 * Вывод списка
	 */
	public function actionIndex() {
		$model = new \app\models\logs\Logs('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Logs']))
			$model->attributes = $_GET['Logs'];

		$this->render('index', array(
			'model' => $model,
		));
	}


	/**
	 * Загрузка модели
	 * @param integer $id
	 * @return \app\models\logs\Logs
	 * @throws \CHttpException при не возможности загрузки модели
	 */
	public function loadModel($id) {
		$model = \app\models\logs\Logs::model()->findByPk($id);

		if($model === null)
			throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		return $model;
	}


}