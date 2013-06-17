<?php

namespace app\controllers\admin;

/**
 * Новости
 */
class NewsController extends \app\components\AdminController {


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
				'roles' => array('moder'),
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
			\Yii::t('admin', 'BREADCRUMBS_NEWS') => array('admin/news'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_NEWS');


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
		$model = new \app\models\news\News;
		$model->createFields();

		$this->performAjaxValidation($model);


		$validate = false;
		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields']);
			if($validate = $model->getManager()->fieldsValidate())
				$model->setFieldsAttributes($_POST['app\models\fields']);
			else 
				$model->addErrors($model->getManager()->fieldsGetErrors());
		}

		if(isset($_POST['app\models\news\News'])) {
			$model->attributes = $_POST['app\models\news\News'];
			if($validate && $model->validate() && $model->save())
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
		$model->createFields($model->attributes);

		$this->performAjaxValidation($model);

		$validate = false;
		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields']);
			if($validate = $model->getManager()->fieldsValidate())
				$model->setFieldsAttributes($_POST['app\models\fields']);
			else 
				$model->addErrors($model->getManager()->fieldsGetErrors());
		}

		if(isset($_POST['app\models\news\News'])) {
			$model->attributes = $_POST['app\models\news\News'];
			if($validate && $model->validate() && $model->save())
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
		$model = new \app\models\news\News('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['app\models\news\News']))
			$model->attributes = $_GET['app\models\news\News'];

		$this->render('index', array('model' => $model));
	}


	/**
	 * Загрузка модели
	 * @param integer $id
	 * @return \app\models\news\News
	 * @throws \CHttpException при не возможности загрузки модели
	 */
	public function loadModel($id) {
		$model = \app\models\news\News::model()->findByPk($id);
		if($model === null)
			throw new \CHttpException(404, \Yii::t('main', 'ERROR_NOT_FOUND_PAGE'));
		return $model;
	}


	/**
	 * Ajax валидация
	 * @param \app\models\news\News $model 
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'news-form')
		{
			echo \CActiveForm::validate($model);
			\Yii::app()->end();
		}
	}


}