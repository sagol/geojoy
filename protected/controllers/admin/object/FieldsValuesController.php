<?php

namespace app\controllers\admin\object;

/**
 * Значения полей
 */
class FieldsValuesController extends \app\components\AdminController {


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
				'actions' => array('create', 'update', 'index', 'view', 'getParentFieldData', 'delete'),
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
			\Yii::t('admin', 'BREADCRUMBS_OBJECTS_FIELDS_VALUES') => array('admin/object/fieldsValues'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_OBJECTS_FIELDS_VALUES');


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
	 * @param integer $id
	 * @throws \CHttpException если $id не число
	 */
	public function actionCreate($id = '') {
		if($id !== '' && !is_numeric($id)) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$model = new \app\models\object\FieldsValues;

		$this->performAjaxValidation($model);

		if(isset($_POST['app\models\object\FieldsValues']))
		{
			$model->attributes = $_POST['app\models\object\FieldsValues'];
			if($id != '') $model->idobj_fields = $id;

			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('create', array(
			'model' => $model,
			'id' => $id,
		));
	}


	/**
	 * Редактирование
	 * @param integer $id 
	 */
	public function actionUpdate($id) {
		$model = $this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST['app\models\object\FieldsValues']))
		{
			$model->attributes = $_POST['app\models\object\FieldsValues'];

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
	 * Список
	 * @param integer $id
	 * @throws \CHttpException если $id не число
	 */
	public function actionIndex($id = '') {
		if($id !== '' && !is_numeric($id)) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$model = new \app\models\object\FieldsValues('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['app\models\object\FieldsValues']))
			$model->attributes = $_GET['app\models\object\FieldsValues'];

		if($id != '') {
			$model->idobj_fields = $id;
			$fieldName = $model->idObjectFields(false, $id);
		}

		$this->render('index', array(
			'model' => $model,
			'id' => $id,
			'fieldName' => @$fieldName,
		));
	}


	/**
	 * Загрузка модели
	 * @param integer $id
	 * @return \app\models\object\FieldsValues
	 * @throws \CHttpException при не возможности загрузки модели
	 */
	public function loadModel($id) {
		$model = \app\models\object\FieldsValues::model()->findByPk($id);
		if($model === null)
			throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		return $model;
	}


	/**
	 * Ajax валидация
	 * @param \app\models\object\FieldsValues $model 
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'app-models-object--fields-lists-form')
		{
			echo \CActiveForm::validate($model);
			\Yii::app()->end();
		}
	}


	/**
	 * Значения родителя
	 */
	public function actionGetParentFieldData() {
		if(!\Yii::app()->request->isAjaxRequest) \Yii::app()->end();

		$field = (int)$_POST['field'];
		$model = new \app\models\object\FieldsValues;
		$options = $model->getParentFieldData($field);

		$this->renderPartial('getFieldData', array('options' => $options));
		\Yii::app()->end();
	}


}