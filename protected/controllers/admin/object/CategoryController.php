<?php

namespace app\controllers\admin\object;

/**
 * Категории
 */
class CategoryController extends \app\components\AdminController {


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
				'actions' => array('create', 'update', 'index', 'view', 'delete'),
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
			\Yii::t('admin', 'BREADCRUMBS_OBJECTS_CATEGORY') => array('admin/object/category'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_OBJECTS_CATEGORY');


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
		$model = new \app\models\object\Category;

		$this->performAjaxValidation($model);

		if(isset($_POST['app\models\object\Category'])) {
			$model->attributes = $_POST['app\models\object\Category'];
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

		if(isset($_POST['app\models\object\Category'])) {
			$model->attributes = $_POST['app\models\object\Category'];
			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('update', array('model' => $model));
	}


	/**
	 * Удаление
	 * @param integer $id
	 * @throws \CHttpException если не POST запрос
	 */
	public function actionDelete($id) {
		if(\Yii::app()->request->isPostRequest) {
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
		$model = new \app\models\object\Category('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['app\models\object\Category']))
			$model->attributes = $_GET['app\models\object\Category'];

		$this->render('index', array(
			'model' => $model,
		));
	}


	/**
	 * Загрузка модели
	 * @param integer $id
	 * @return \app\models\object\Category
	 * @throws \CHttpException при не возможности загрузки модели
	 */
	public function loadModel($id) {
		$model = \app\models\object\Category::model()->findByPk($id);
		if($model === null)
			throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		return $model;
	}


	/**
	 * Ajax валидация
	 * @param \app\models\object\Category $model 
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'app-models-object--category-form')
		{
			echo \CActiveForm::validate($model);
			\Yii::app()->end();
		}
	}


}