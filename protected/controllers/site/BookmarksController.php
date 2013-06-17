<?php

namespace app\controllers\site;

/**
 * Закладки пользователя
 */
class BookmarksController extends \app\components\Controller {


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
				'actions' => array('add', 'delete'), // для работы ajax, проверка доступа выполняется в модели
				'users' => array('*'),
			),

			array('allow',
				'actions' => array('show', 'index'),
				'roles' => array('authorized'),
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
			\Yii::t('nav', 'NAV_PRIVATE_CABINET') => array('/site/user'),
		);

		if(\Yii::app()->user->checkAccess('user')) {
			$this->leftMenu = array(
				array('label' => \Yii::t('nav', 'YOU_OBJECTS'), 'url' => array('/site/user')),
				array('label' => \Yii::t('nav', 'YOU_BOOKMARKS'), 'url' => array('/site/bookmarks/index')),
				array('label' => \Yii::t('nav', 'USER_PROFILE'), 'url' => array('/site/user/profile')),
				array('label' => \Yii::t('nav', 'USER_PROFILE_EDIT'), 'url' => array('/site/user/edit')),
				array('label' => \Yii::t('nav', 'USER_PROFILE_PASSWORD'), 'url' => array('/site/user/password')),
			);
		}


		return true;
	}


	/**
	 * Добавление закладки
	 * @param integer $id 
	 */
	public function actionAdd($id) {
		if(\Yii::app()->request->isAjaxRequest) {
			$model = new \app\models\bookmarks\Bookmarks;
			if($model->add($id)) echo 'ok';
			else echo $model->getError('bookmarks');
			\Yii::app()->end();
		}

		$this->redirect(array('/site/objects/index'));
	}


	/**
	 * Удаление закладки
	 * @param integer $id 
	 */
	public function actionDelete($id) {
		$model = new \app\models\bookmarks\Bookmarks;
		if($model->delete($id)) echo 'ok';
		else echo $model->getError('bookmarks');


		\Yii::app()->end();
	}


	/**
	 * Вывод списка закладок
	 * @param integer $page 
	 */
	public function actionIndex($page = 1) {
		$model = new \app\models\bookmarks\Bookmarks;
		$bookmarks = $model->index($page);
		$bookmarksCount = $model->indexCount();

		$bookmarksOnPage = \Yii::app()->params['bookmarksOnPage'];
		if($bookmarksCount > $bookmarksOnPage) $this->pages = array(
			'count' => ceil($bookmarksCount/$bookmarksOnPage),
			'active' => $page,
			'url' => array('/site/bookmarks/index'),
		);

		if(empty($bookmarks)) $this->render('indexNo');
		else $this->render('index', array('bookmarks' => $bookmarks));
	}


	/**
	 * Вывод содержимого закладок
	 * @param integer $id
	 * @param integer $page 
	 */
	public function actionShow($id, $page = 1) {
		list($idusers, $userName) = \app\models\bookmarks\Bookmarks::paramsBookmark($id);

		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'criteria' => array(
				'idusers' => $idusers,
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
			),
		);
		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) $this->pages = array(
			'count' => ceil($objectsCount/\Yii::app()->params['objectsOnPage']),
			'active' => $page,
			'url' => array('/site/bookmarks/show', 'id' => $id),
		);

		$queryParams = array(
			'keepSql' => true,
			'skipFields' => true,
			'page' => $page,
			'limit' => \Yii::app()->params['objectsOnPage'],
			'orderBy' => 'o.modified DESC',
		);
		$objsData = $managersObject->filter('main', $queryParams);

		if(!empty($objsData)) foreach($objsData as $data)
			$objects[$data['idobjects']] = \app\models\object\Object::load($data['idobjects'], 'read', $data);
		unset($managersObject, $queryParams, $objsData);

		if(empty($objects)) $this->render('objectsNo');
		else {
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
				$json['html'] = $this->renderPartial('objects', array('objects' => $objects, 'userName' => $userName, 'notShowTabs' => true), true);
				echo json_encode($json);
				\Yii::app()->end();
			}
			else $this->render('objects', array('objects' => $objects, 'userName' => $userName));
		}
	}


}