<?php

namespace app\controllers\admin\object;

/**
 * Связи полей и типов объявлений
 */
class TiesController extends \app\components\AdminController {


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
				'actions' => array('create', 'update', 'index', 'view', 'getFieldsFieldData', 'getTypeFieldData', 'delete'),
				'roles' => array('admin'),
			),

			array('deny',  // deny all users
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
			\Yii::t('admin', 'BREADCRUMBS_TIES') => array('admin/object/ties'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_TIES');


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
	 * Создание
	 */
	public function actionCreate() {
		$model = new \app\models\object\Ties;

		$this->performAjaxValidation($model);

		if(isset($_POST['app\models\object\Ties']))
		{
			$model->attributes = $_POST['app\models\object\Ties'];
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('create', array(
			'model' => $model,
		));
	}


	/**
	 * Редактирование
	 * @param integer $id 
	 */
	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST['app\models\object\Ties']))
		{
			$model->attributes = $_POST['app\models\object\Ties'];

			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('update', array(
			'model' => $model,
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
		$model = new \app\models\object\Ties('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['app\models\object\Ties']))
			$model->attributes = $_GET['app\models\object\Ties'];

		$this->render('index', array('model' => $model));
	}


	/**
	 * Загрузка модели
	 * @param integer $id
	 * @return \app\models\object\Ties
	 * @throws \CHttpException при не возможности загрузки модели
	 */
	public function loadModel($id) {
		$model = \app\models\object\Ties::model()->findByPk($id);
		if($model === null)
			throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		return $model;
	}


	/**
	 * Ajax валидация
	 * @param \app\models\object\Ties $model 
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'app-models-object--fields-type-form')
		{
			echo \CActiveForm::validate($model);
			\Yii::app()->end();
		}
	}


	/**
	 * Выводит типы объявлений, в которых нету запрашиваемого поля
	 */
	public function actionGetTypeFieldData() {
		if(!\Yii::app()->request->isAjaxRequest) \Yii::app()->end();

		$field = (int)$_POST['field'];
		$model = new \app\models\object\Ties;
		$options = $model->getTypeFieldData($field);

		$this->renderPartial('getFieldData', array('options' => $options));
		\Yii::app()->end();
	}


	/**
	 * Выводит поля объявлений которых нет в запрашиваемом типе
	 */
	public function actionGetFieldsFieldData() {
		if(!\Yii::app()->request->isAjaxRequest) \Yii::app()->end();

		$field = (int)$_POST['field'];
		$model = new \app\models\object\Ties;
		$options = $model->getFieldsFieldData($field);

		$this->renderPartial('getFieldData', array('options' => $options));
		\Yii::app()->end();
	}


}