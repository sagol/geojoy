<?php

namespace app\models\bookmarks;

/**
 * Закладки пользователя
 */
class Bookmarks extends \CModel{

	/**
	 * Список имен атрибутов 
	 */
	public function attributeNames() {
	
	}


	/**
	 * Добавить
	 * @param integer $id
	 * @return boolean 
	 */
	public function add($id) {
		if(!($multiUser = \Yii::app()->user->multiUser)) {
			$this->addError('bookmarks', \Yii::t('nav', 'BOOKMARKS_YOU_NOT_LOGIN'));
			return false;
		}

		$sql = 'SELECT * 
			FROM obj_bookmarks 
			WHERE idusers = :idusers AND multiuser = :multiuser';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idusers', $id, \PDO::PARAM_INT);
		$command->bindParam(':multiuser', $multiUser, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) {
			$this->addError('bookmarks', \Yii::t('nav', 'BOOKMARKS_EXIST'));
			return false;
		}

		$sql = 'INSERT INTO obj_bookmarks (idusers, owner, multiuser) 
			VALUES (:idusers, :owner, :multiuser)';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idusers', $id, \PDO::PARAM_INT);
		$command->bindValue(':owner', \Yii::app()->user->id, \PDO::PARAM_INT);
		$command->bindParam(':multiuser', $multiUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();


		return $rowCount;
	}


	/**
	 * Удалить
	 * @param integer $idbookmarks
	 * @return boolean 
	 */
	public function delete($idbookmarks) {
		if(!($multiUser = \Yii::app()->user->multiUser)) {
			$this->addError('bookmarks', \Yii::t('nav', 'BOOKMARKS_YOU_NOT_LOGIN'));
			return false;
		}

		$sql = 'DELETE FROM obj_bookmarks 
			WHERE idobj_bookmarks = :idbookmarks AND multiuser = :multiuser';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':idbookmarks', $idbookmarks, \PDO::PARAM_INT);
		$command->bindParam(':multiuser', $multiUser, \PDO::PARAM_INT);
		$rowCount = $command->execute();

		if(!$rowCount) $this->addError('bookmarks', \Yii::t('nav', 'BOOKMARKS_NOT_EXIST_OR_NOT_PERMISSION'));


		return $rowCount;
	}


	/**
	 * Список
	 * @param integer $page
	 * @return array 
	 */
	public function index($page = 1) {
		$bookmarks = false;
		$limit = \Yii::app()->params['bookmarksOnPage'];
		$offset = $limit*($page-1);

		$sql = 'SELECT ob.*, u.name 
			FROM obj_bookmarks ob 
			LEFT JOIN users u USING(idusers) 
			WHERE ob.multiuser = :multiuser 
			LIMIT :limit OFFSET :offset';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':multiuser', \Yii::app()->user->multiUser, \PDO::PARAM_INT);
		$command->bindParam(':limit', $limit, \PDO::PARAM_INT);
		$command->bindParam(':offset', $offset, \PDO::PARAM_INT);
		$dataReader = $command->query();

		while(($data = $dataReader->read()) !== false)
			$bookmarks[] = $data;


		return $bookmarks;
	}


	/**
	 * Количество
	 * @return integer 
	 */
	public static function indexCount() {
		$sql = 'SELECT COUNT(*) 
			FROM obj_bookmarks 
			WHERE multiuser = :multiuser';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':multiuser', \Yii::app()->user->multiUser, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) !== false) $count = $data['count'];
		else $count = 0;


		return $count;
	}


	/**
	 * Данные для вывода объявлений, на которые указывает закладка
	 * @param integer $id
	 * @return boolean 
	 */
	public static function paramsBookmark($id) {
		$return = false;

		$sql = 'SELECT ob.*, u.name 
			FROM obj_bookmarks ob 
			LEFT JOIN users u USING(idusers) 
			WHERE idobj_bookmarks = :id';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $id, \PDO::PARAM_INT);
		$dataReader = $command->query();

		if(($data = $dataReader->read()) === false) return false;
		if($data['multiuser'] != \Yii::app()->user->multiUser) return false;

		$return = array($data['idusers'], $data['name']);


		return $return;
	}


}