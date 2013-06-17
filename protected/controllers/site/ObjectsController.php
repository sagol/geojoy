<?php

namespace app\controllers\site;

/**
 * Объявления
 */
class ObjectsController extends \app\components\Controller {


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
				'actions' => array('indexMap', 'categoryMap', 'index', 'category', 'view', 'getFieldData'),
				'users' => array('*'),
			),

			array('allow',
				'actions' => array('edit', 'del', 'objectUp'),
				'roles' => array('editOrDeleteObject'),
			),

			array('allow',
				'actions' => array('add', 'selectCategory', 'spam'),
				'roles' => array('authorized'),
			),

			array('allow',
				'actions' => array('toSpam', 'notSpam', 'moderateOk'),
				'roles' => array('moder'),
			),

			array('deny',
				'users' => array('*'),
				'deniedCallback' => array('\app\components\DeniedCallbacks', 'objects'),
			),
		);
	}


	public function actionIndexMap() {
		$this->breadcrumbs = array('NAV_ALL_OBJECTS');

		\Yii::app()->params['objectsOnPage'] = null;

		// получение параметров фильтра и игнорирование кеша при их наличии
		$filterParams = \app\components\object\Filter::getInstanse()->params();
		// получение из кеша
		$paramsCategoryPage = \Yii::app()->params['cache']['categoryPage'];
		$appLang = \Yii::app()->getLanguage();
		if($paramsCategoryPage !== -1 && empty($filterParams)) {
			$cache = \Yii::app()->cache;

			$html = $cache->get("indexPageMap-$appLang");
			if($html !== false) {
				$this->renderText($html);

				unset($cache, $html);
				return true;
			}
		}

		$queryParams = array(
			'skipFields' => true,
			'criteria' => array(
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'on_map' => 1,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!\Yii::app()->user->getIsGuest() && $spam = \Yii::app()->session->get('spam')) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}

		$managersObject = \app\managers\Objects::getInstanse();
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData, $data);

		if(empty($objects)) {
			$this->render('indexNo');

			return true;
		}

		// рендерим только сам вид без макета
		$html = $this->renderPartial('indexMap', array(
			'objects' => $objects,
			'defaultMap' => \Yii::app()->params['defaultMapInterface'],
			'objectButtonRouter' => array('/site/objects/index'),
		), true);

		// сохранение в кеш
		if($paramsCategoryPage !== -1 && empty($filterParams))
			$cache->set("indexPageMap-$appLang", $html, $paramsCategoryPage);

		// рендерим макет
		$this->renderText($html);

		unset($cache, $html);


		return true;
	}


	public function actionCategoryMap($categoryTree) {
		$category = \app\components\object\Category::getInstanse()->data($categoryTree);
		$this->breadcrumbs = $category['breadcrumbs'];
		$this->categoryPath = $category['path'];
		$this->categoryTree = $categoryTree;

		\Yii::app()->params['objectsOnPage'] = null;

		// получение параметров фильтра и игнорирование кеша при их наличии
		$filterParams = \app\components\object\Filter::getInstanse()->params();
		// получение из кеша
		$paramsCategoryPage = \Yii::app()->params['cache']['categoryPage'];
		$appLang = \Yii::app()->getLanguage();
		if($paramsCategoryPage !== -1 && empty($filterParams)) {
			$cache = \Yii::app()->cache;

			$html = $cache->get("categoryPageMap-$this->categoryPath-$appLang");
			if($html !== false) {
				$this->renderText($html);

				unset($cache, $html);

				return true;
			}
		}

		$queryParams = array(
			'skipFields' => true,
			'criteria' => array(
				'idobj_category' => $category['id'],
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'on_map' => 1,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!\Yii::app()->user->getIsGuest() && $spam = \Yii::app()->session->get('spam')) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}

		$managersObject = \app\managers\Objects::getInstanse();
		$objsData = $managersObject->filter($category['type'], $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData, $data);

		if(empty($objects)) {
			$this->render('indexNo');

			return true;
		}

		// рендерим только сам вид без макета
		$html = $this->renderPartial('indexMap', array(
			'objects' => $objects,
			'defaultMap' => \Yii::app()->params['defaultMapInterface'],
			'objectButtonRouter' => array('/site/objects/category', 'params' => $this->categoryTree),
		), true);

		// сохранение в кеш
		if($paramsCategoryPage !== -1 && empty($filterParams))
			$cache->set("categoryPageMap-$this->categoryPath-$appLang", $html, $paramsCategoryPage);

		// рендерим макет
		$this->renderText($html);
		unset($cache, $html);


		return true;
	}


	/**
	 * Общая страница категорий
	 * @param integer $page
	 * @return boolean 
	 */
	public function actionIndex($page = 1) {
		$isGuest = (int)\Yii::app()->user->getIsGuest();
		if(!$isGuest)
			$spam = \Yii::app()->session->get('spam');

		// получение параметров фильтра и игнорирование кеша при их наличии
		$filterParams = \app\components\object\Filter::getInstanse()->params();
		// получение из кеша
		$paramsCategoryPage = \Yii::app()->params['cache']['categoryPage'];
		// для ролей moder и выше игнорировать кеш страницы
		// при фильтрации игнорировать кеш страницы
		// при наличии объявлений отмеченых как спам
		$ignoreCache = \Yii::app()->user->checkAccess('moder') || !empty($filterParams) || !empty($spam);
		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($paramsCategoryPage !== -1 && !$ignoreCache) {
			$appLang = \Yii::app()->getLanguage();
			$cache = \Yii::app()->cache;

			$result = $cache->get("indexPage-$appLang-G$isGuest-$page-$objectsOnPage");
			if($result !== false) {
				list($html, $pageCount) = $result;
				if($pageCount) {
					$this->pages = array(
						'count' => $pageCount,
						'active' => $page,
						'url' => array('/site/objects/index'),
					);
				}
				$this->renderText($html);
				unset($cache, $result, $html);
				return true;
			}
		}


		$this->breadcrumbs = array('NAV_ALL_OBJECTS');

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!empty($spam)) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$pageCount = ceil($objectsCount/$objectsOnPage);
		if($objectsCount > $objectsOnPage)
			$this->pages = array(
				'count' => $pageCount,
				'active' => $page,
				'url' => array('/site/objects/index'),
			);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!empty($spam)) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);

		unset($managersObject, $queryParams, $objsData, $spam);

		if(empty($objects)) {
			$this->render('indexNo');

			return true;
		}

		// рендерим только сам вид без макета
		$html = $this->renderPartial('index', array('objects' => $objects, 'mapButtonRouter' => array('/site/objects/indexMap')), true);

		// сохранение в кеш
		if($paramsCategoryPage !== -1 && !$ignoreCache)
			$cache->set("indexPage-$appLang-G$isGuest-$page-$objectsOnPage", array($html, $pageCount), $paramsCategoryPage);

		$paramsObjectsShowType = \Yii::app()->params['objectsShowType'];
		if($paramsObjectsShowType == 1 && \Yii::app()->request->isAjaxRequest) {
			$json['url'] = '';
			if($objectsCount > $objectsOnPage) {
				if($this->pages['count'] > $this->pages['active']) {
					$params = \app\components\widgets\Pages::pageUrl($this->pages['active']+1, $this->pages['url']);
					$url = $params[0];
					unset($params[0]);
					$json['url'] = $this->createAbsoluteUrl($url, $params);
				}
			}

			$json['status'] = 'ok';
			$json['html'] = $html;
			echo json_encode($json);
			\Yii::app()->end();
		}
		// рендерим макет
		else $this->renderText($html);

		unset($cache, $result, $html);


		return true;
	}


	/**
	 * Cтраница категорий
	 * @param string $categoryTree
	 * @param integer $page
	 * @return boolean 
	 */
	public function actionCategory($categoryTree, $page = 1) {
		$category = \app\components\object\Category::getInstanse()->data($categoryTree);
		$this->categoryPath = $category['path'];
		$this->categoryTree = $categoryTree;

		$isGuest = (int)\Yii::app()->user->getIsGuest();
		if(!$isGuest)
			$spam = \Yii::app()->session->get('spam');

		// получение параметров фильтра и игнорирование кеша при их наличии
		$filterParams = \app\components\object\Filter::getInstanse()->params();
		// получение из кеша
		$paramsCategoryPage = \Yii::app()->params['cache']['categoryPage'];
		// для ролей moder и выше игнорировать кеш страницы
		// при фильтрации игнорировать кеш страницы
		// при наличии объявлений отмеченых как спам
		$ignoreCache = \Yii::app()->user->checkAccess('moder') || !empty($filterParams) || !empty($spam);
		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($paramsCategoryPage !== -1 && !$ignoreCache) {
			$appLang = \Yii::app()->getLanguage();
			$cache = \Yii::app()->cache;

			$result = $cache->get("categoryPage-$this->categoryPath-$appLang-G$isGuest-$page-$objectsOnPage");
			if($result !== false) {
				list($html, $pageCount) = $result;
				if($pageCount) {
					$this->pages = array(
						'count' => $pageCount,
						'active' => $page,
						'url' => $category['menu']['url'],
					);
				}
				$this->renderText($html);
				unset($cache, $result, $html);
				return true;
			}
		}

		$this->breadcrumbs = $category['breadcrumbs'];

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'idobj_category' => $category['id'],
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!empty($spam)) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter($category['type'], $queryParams);
		unset($queryParams);

		$pageCount = ceil($objectsCount/$objectsOnPage);
		if($objectsCount > $objectsOnPage)
			$this->pages = array(
				'count' => $pageCount,
				'active' => $page,
				'url' => $category['menu']['url'],
			);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.show DESC',
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!empty($spam)) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}
		$objsData = $managersObject->filter($category['type'], $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);

		unset($managersObject, $queryParams, $objsData, $spam);

		if(empty($objects)) {
			$this->render('indexNo');

			return true;
		}

		// рендерим только сам вид без макета
		$html = $this->renderPartial('index', array('objects' => $objects, 'mapButtonRouter' => array('/site/objects/categoryMap', 'params' => $this->categoryTree)), true);

		// сохранение в кеш
		if($paramsCategoryPage !== -1 && !$ignoreCache)
			$cache->set("categoryPage-$this->categoryPath-$appLang-G$isGuest-$page-$objectsOnPage", array($html, $pageCount), $paramsCategoryPage);

		$paramsObjectsShowType = \Yii::app()->params['objectsShowType'];
		if($paramsObjectsShowType == 1 && \Yii::app()->request->isAjaxRequest) {
			$json['url'] = '';
			if($objectsCount > $objectsOnPage) {
				if($this->pages['count'] > $this->pages['active']) {
					$params = \app\components\widgets\Pages::pageUrl($this->pages['active']+1, $this->pages['url']);
					$url = $params[0];
					unset($params[0]);
					$json['url'] = $this->createAbsoluteUrl($url, $params);
				}
			}

			$json['status'] = 'ok';
			$json['html'] = $html;
			echo json_encode($json);
			\Yii::app()->end();
		}
		// рендерим макет
		else $this->renderText($html);

		unset($cache, $result, $html);


		return true;
	}


	/**
	 * Форма выбора категории для добавления объявления
	 */
	public function actionSelectCategory() {
		$model = new \app\models\object\SelectCategoryForm;

		if(isset($_POST['app\models\object\SelectCategoryForm'])) {
			$model->attributes = $_POST['app\models\object\SelectCategoryForm'];

			$path = \app\components\object\Category::getInstanse()->data($model->category, 'path');

			if($path)
				$this->redirect(array($path . '/add'));
		}


		$this->render('selectCategory', array('model' => $model, 'category' => \app\components\object\Category::getInstanse()->data()));
	}


	/**
	 * Ajax валидация
	 * @param \app\models\object\Object $model 
	 */
	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax'] === 'app-models-object--objects-form') {
			echo \CActiveForm::validate($model);
			\Yii::app()->end();
		}
	}


	/**
	 * Вывод формы создания
	 * @param string $categoryTree
	 * @return boolean 
	 */
	public function actionAdd($categoryTree) {
		$category = \app\components\object\Category::getInstanse()->data($categoryTree);
		$this->breadcrumbs = $category['breadcrumbs'];
		$this->categoryPath = $category['path'];
		$this->categoryTree = $categoryTree;

		// если не задан тип категории, то в нее нельзя добавлять
		if(!$category['type']) {
			$this->render('addNo');
			return;
		}

		if(empty($_REQUEST['adv']))
			$this->redirect($this->createUrl($this->categoryPath . '/add') . '?adv=' . \Yii::app()->user->id . time());
		else $adv = $_REQUEST['adv'];


		// создание
		$model = new \app\models\object\Object;
		$model->create($category, $adv);

		// обработка файлов
		$url = $this->createUrl($this->categoryPath . '/add') . '?adv=' . $adv;
		$extData = array('url' => $url, 'adv' => $adv);
		$model->getManager()->fieldsEventProcessing($this->id, $this->action->id, $extData);

		// ajax валидация
		// $this->performAjaxValidation($model);

		// сохранение
		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields'], array('adv' => $adv));
			if($model->save())
				$this->redirect(array('view', 'id' => $model->idobjects));
		}

		$this->render('add', array('model' => $model, 'adv' => $adv));
	}


	/**
	 * Вывод формы редактирования
	 * @param integer $id
	 * @throws \CHttpException если нету прав на редактирование
	 */
	public function actionEdit($id) {
		// загрузка для редактирования
		$model = \app\models\object\Object::load($id, \app\managers\Manager::ACCESS_TYPE_EDIT);

		$category = \app\components\object\Category::getInstanse()->data($model->categoryTree);
		$this->breadcrumbs = $category['breadcrumbs'];
		$this->categoryPath = $category['path'];
		$this->categoryTree = $model->categoryTree;

		// обработка файлов
		$url = $this->createUrl('/site/objects/edit', array('id' => $id)) . '?adv=' . $id;
		$extData = array('url' => $url, 'adv' => $id);
		$model->getManager()->fieldsEventProcessing($this->id, $this->action->id, $extData);

		// ajax валидация
		//$this->performAjaxValidation($model);

		// сохранение
		if(isset($_POST['app\models\fields'])) {
			$model->getManager()->fieldsAttributes($_POST['app\models\fields'], array('adv' => $id));
			if(isset($_POST['app\models\object\Object'])) $model->attributes = $_POST['app\models\object\Object'];

			if($model->validate() && $model->save()) $this->redirect(array('view', 'id' => $model->idobjects));
		}

		$this->render('edit', array('model' => $model));
	}


	/**
	 * Удаление
	 * @param integer $id
	 * @throws \CHttpException если нету прав на удаление
	 */
	public function actionDel($id)  {
		if(!is_numeric($id)) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$model = \app\models\object\Object::load($id, \app\managers\Manager::ACCESS_TYPE_EDIT);
		$model->delete($id);

		$this->categoryPath = \app\components\object\Category::getInstanse()->data($model->categoryTree, 'path');

		if(!isset($_GET['ajax']))
			$this->redirect(isset($_GET['returnUrl']) ? $_GET['returnUrl'] : array('/' . $this->categoryPath));
	}


	/**
	 * Детальный вывод
	 * @param integer $id 
	 */
	public function actionView($id, $print = false) {
		// загрузка
		$model = \app\models\object\Object::load($id, 'read');

		\app\components\object\Category::getInstanse()->setCurCategory($model->categoryTree);
		$category = \app\components\object\Category::getInstanse()->data($model->categoryTree);
		$this->breadcrumbs = $category['breadcrumbs'];
		$this->categoryPath = $category['path'];
		$this->categoryTree = $model->categoryTree;
		$user = new \app\models\users\User;
		$user->user($model->idusers, 'object');

		$checkAccessEditOrDeleteObject = \Yii::app()->user->checkAccess('editOrDeleteObject', array('curUser' => \Yii::app()->user, 'multiUser' => $model->multiUser, 'idusers' => $model->idusers));

		$status = null;
		if($model->moderate == \app\models\object\Object::OBJECT_MODERATE_NEED)
			$status = \Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_MODERATE');
		elseif($model->spam == \app\models\object\Object::OBJECT_SPAM_EXACTLY)
			$status = \Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_SPAM');
		elseif($model->categoryDisabled)
			$status = \Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_CATEGORY_DISABLED');
		elseif($model->getManager()->hasField(\app\models\object\Object::FIELD_LIFETIME)) {
			$curDate = new \DateTime();
			$date = new \DateTime($model->field(\app\models\object\Object::FIELD_LIFETIME)->getValueDate());
			$interval = $date->diff($curDate);
			if(!$interval->invert) $status = \Yii::t('nav', 'NAV_OBJECT_DETAIL_OBJECT_LIFETIME');
		}

		if($user->field(\app\models\users\User::NAME_SOCIAL_INFO_VISIBLE)->getValue()) $socialAccounts = \app\models\users\MultiAccount::userSocialAccounts();
		else $socialAccounts = array();

		if(!$checkAccessEditOrDeleteObject && !empty($status))
			$this->render('viewStatus', array('model' => $model, 'user' => $user, 'status' => $status, 'socialAccounts' => $socialAccounts));
		elseif($print) {
			$this->layout = '//layouts/siteContainerPrint';
			$this->render('viewPrint', array('model' => $model, 'user' => $user, 'status' => $status, 'socialAccounts' => $socialAccounts));
		}
		else $this->render('view', array('model' => $model, 'user' => $user, 'status' => $status, 'checkAccessEditOrDeleteObject' => $checkAccessEditOrDeleteObject, 'socialAccounts' => $socialAccounts));
	}


	/**
	 * Изменение спам статуса объявления на "возможно спам" для пользователя
	 * @param integer $id 
	 */
	public function actionSpam($id) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$spam = \app\models\object\Object::spam($id);
		if($spam) echo 'ok';
		\Yii::app()->end();
	}


	/**
	 * Изменение спам статуса объявления на "спам"
	 * @param integer $id 
	 */
	public function actionToSpam($id) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$spam = \app\models\object\Object::editSpam($id, \app\models\object\Object::OBJECT_SPAM_EXACTLY);
		if($spam) echo 'ok';
		\Yii::app()->end();
	}


	/**
	 * Изменение спам статуса объявления на "не спам"
	 * @param integer $id 
	 */
	public function actionNotSpam($id) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$spam = \app\models\object\Object::editSpam($id, \app\models\object\Object::OBJECT_SPAM_NOT);
		if($spam) echo 'ok';
		\Yii::app()->end();
	}


	/**
	 * Изменение статуса модерация объявления на "выполнена"
	 * @param integer $id 
	 */
	public function actionModerateOk($id) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$moderate = \app\models\object\Object::moderateOk($id, \app\models\object\Object::OBJECT_MODERATE_OK);
		if($moderate) echo 'ok';
		\Yii::app()->end();
	}


	/**
	 * Поднятие объявления вверх
	 * @param integer $id 
	 */
	public function actionObjectUp($id) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$model = \app\models\object\Object::load($id, 'read');
		$objectUp = $model->objectUp();
		if($objectUp) echo 'ok';
		else echo \Yii::t('nav', 'WAS_LITTLE_TIME');
		\Yii::app()->end();
	}


	/**
	 * Получение по ajax запросу данных поля для определенного значения родителя
	 * @throws \CHttpException если не ajax запрос
	 */
	public function actionGetFieldData() {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		$field = (int)$_POST['field'];
		$parentValue = (int)$_POST['parentValue'];
		$type = $_POST['type'];
		if(isset($_POST['needUse'])) $needUse = (int)$_POST['needUse'];
		else $needUse = 0;

		$fieldsManager = new \app\managers\Object(\app\managers\Manager::ACCESS_TYPE_EDIT);
		$fieldsManager->ajax($field, $parentValue, $needUse, $type);
	}


}