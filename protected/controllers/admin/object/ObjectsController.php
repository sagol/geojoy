<?php

namespace app\controllers\admin\object;

/**
 * Объявления
 */
class ObjectsController extends \app\components\AdminController {


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
				'actions' => array('index', 'spam', 'moderate'),
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
			\Yii::t('admin', 'BREADCRUMBS_OBJECTS') => array('admin/object/objects'),
		);

		$this->menuTitle = \Yii::t('admin', 'MENU_CONTROL_OBJECTS');

		$this->menu = array(
			array('label' => \Yii::t('admin', 'MENU_TO_MODERATE'), 'url' => array('admin/object/objects/moderate')),
			array('label' => \Yii::t('admin', 'MENU_MARK_SPAM'), 'url' => array('admin/object/objects')),
			array('label' => \Yii::t('admin', 'MENU_IN_SPAM'), 'url' => array('admin/object/objects/spam')),
		);

		return true;
	}


	/**
	 * Список объявлений помеченых пользователем как спам
	 * @param integer $page 
	 */
	public function actionIndex($page = 1) {
		$this->breadcrumbs += array(\Yii::t('admin', 'BREADCRUMBS_MARK_SPAM'));
		if($page > 1) $this->breadcrumbs += array($page);

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'spam' => \app\models\object\Object::OBJECT_SPAM_POSSIBLY,
				'disabled' => 0,
			),
		);
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) $this->pages = array(
			'count' => ceil($objectsCount/\Yii::app()->params['objectsOnPage']),
			'active' => $page,
			'url' => array('/admin/object/objects/index'),
		);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData);

		if(empty($objects)) $this->render('indexNo');
		else $this->render('index', array('objects' => $objects, 'editSpam' => true));
	}


	/**
	 * Список объявлений помещеных в спам
	 * @param integer $page 
	 */
	public function actionSpam($page = 1) {
		$this->breadcrumbs += array(\Yii::t('admin', 'BREADCRUMBS_SPAM'));
		if($page > 1) $this->breadcrumbs += array($page);

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'spam' => \app\models\object\Object::OBJECT_SPAM_EXACTLY,
				'disabled' => 0,
			),
		);
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) $this->pages = array(
			'count' => ceil($objectsCount/$objectsOnPage),
			'active' => $page,
			'url' => array('/admin/object/objects/spam'),
		);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData);

		if(empty($objects)) $this->render('indexNo');
		else $this->render('index', array('objects' => $objects, 'editSpam' => true));
	}


	/**
	 * Список объявлений подлежащих модерации
	 * @param integer $page 
	 */
	public function actionModerate($page = 1) {
		$this->breadcrumbs += array(\Yii::t('admin', 'BREADCRUMBS_TO_MODERATE'));
		if($page > 1) $this->breadcrumbs += array($page);

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'moderate' => \app\models\object\Object::OBJECT_MODERATE_NEED,
				'disabled' => 0,
			),
		);
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) $this->pages = array(
			'count' => ceil($objectsCount/$objectsOnPage),
			'active' => $page,
			'url' => array('/admin/object/objects/moderate'),
		);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData);

		if(empty($objects)) $this->render('indexNo');
		else $this->render('index', array('objects' => $objects, 'editModerate' => true));
	}


}