<?php

namespace app\models\search;

/**
 * 
 */
class Search extends \CComponent {


	public function searchObjects($id, $page, &$controller) {
		$objects = array();
		// получение кол-ва для пагинации
		$queryParams = array(
			'needCount' => true,
			'skipFields' => true,
			'skipFilter' => true,
			'criteria' => array(
				'!=spam' => 2,
				'!=moderate' => 1,
				'disabled' => 0,
				'OR' => array('>=lifetime_date' => 'NOW()', '><lifetime_date'),
				'~idobjects' => array('value' => $id, 'param' => 'param', 'function' => 'idobjects::text'),
			),
		);
		// убираем из выборки объявления помеченые пользователем, как спам
		if(!\Yii::app()->user->getIsGuest() && $spam = \Yii::app()->session->get('spam')) {
			$spam['param'] = 'param';
			$queryParams['criteria'][] = array('!()idobjects' => $spam);
		}

		$managersObject = \app\managers\Objects::getInstanse();
		$objectsCount = $managersObject->filter('main', $queryParams);
		unset($queryParams, $spam);

		$objectsOnPage = \Yii::app()->params['objectsOnPage'];
		if($objectsCount > $objectsOnPage) {
			// получаем параметры фильтра из сессии пользователя
			$controller->pages = array(
				'count' => ceil($objectsCount/$objectsOnPage),
				'active' => $page,
				'url' => array('/site/site/search', 'search' => $id),
			);
		}

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


		return $objects;
	}


	public function searchUser($name, $page, &$controller) {
		$fieldName = \app\models\users\User::NAME_NAME;

		$sql = 'SELECT COUNT(idusers) 
			FROM users 
			WHERE status > 0 AND ' . $fieldName . ' LIKE :name';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':name', "%$name%", \PDO::PARAM_STR);
		$searchCount = $command->queryScalar();


		$searchOnPage = \Yii::app()->params['searchOnPage'];
		if($searchCount > $searchOnPage) {
			// получаем параметры фильтра из сессии пользователя
			$controller->pages = array(
				'count' => ceil($searchCount/$searchOnPage),
				'active' => $page,
				'url' => array('/site/site/search', 'search' => $name),
			);
		}

		$users = array();
		$page = ($page-1)*$searchOnPage;

		$sql = "SELECT idusers 
			FROM users 
			WHERE status > 0 AND $fieldName LIKE :name
			ORDER BY $fieldName
			LIMIT $searchOnPage OFFSET $page";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':name', "%$name%", \PDO::PARAM_STR);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false) {
			$users[$data['idusers']] = new \app\models\users\User;
			$users[$data['idusers']]->user($data['idusers']);
		}


		return $users;
	}


}