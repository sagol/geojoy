<?php

namespace app\components;

/**
 * Логирование событий сайта
 */
class AppLog {

	/**
	 * Тип события информация
	 */
	const TYPE_INFO = 0;
	/**
	 * Тип события предупреждение
	 */
	const TYPE_WARNING = 1;
	/**
	 * Тип события ошибка
	 */
	const TYPE_ERROR = 2;

	/**
	 * Пропускаемые при логировании события
	 * @var array 
	 */
	public $skipActions = array();
	/**
	 * Пропускаемые при логировании заголовки
	 * @var array 
	 */
	public $skipTitle = array();
	/**
	 * Пропускаемые при логировании типы событий
	 * @var array 
	 */
	public $skipType = array();


	/**
	 * Инициализация
	 */
	public function init() {
	}


	/**
	 * Логирование событий
	 * @param string $action событие
	 * @param string $title залоговок
	 * @param string $message текст
	 * @param integer $type (self::TYPE_*)
	 * @return integer 
	 */
	public function add($action, $title, $message, $params = array(), $type = self::TYPE_INFO) {
		if(!empty($this->skipActions) && in_array($action, $this->skipActions)) return 2; //skip
		if(!empty($this->skipType) && in_array($type, $this->skipType)) return 2; //skip
		if(!empty($this->skipTitle)) {
			if(in_array($title, $this->skipTitle)) return 2;
			if(!empty($this->skipTitle[$action]) && in_array($title, $this->skipTitle[$action])) return 2;
		}

		if(!empty($params)) {
			foreach($params as $name => $value)
				$values[] = "$name=$value";

			$params = implode(',', $values);
		}

		$sql = 'INSERT INTO logs (type, action, title, message, params) 
			VALUES(:type, :action, :title, :message, :params)';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':action', $action, \PDO::PARAM_STR);
		$command->bindParam(':title', $title, \PDO::PARAM_STR);
		$command->bindParam(':message', $message, \PDO::PARAM_STR);
		$command->bindParam(':type', $type, \PDO::PARAM_INT);
		$command->bindParam(':params', $params, \PDO::PARAM_STR);
		$rowCount = $command->execute();

		if(!$rowCount) \Yii::log('Error add app log to db.', \CLogger::LEVEL_ERROR);

		return $rowCount;
	}


	/**
	 * Логирование событий почты
	 * @param string $title залоговок
	 * @param string $message текст
	 * @param integer $type (self::TYPE_*)
	 * @return integer 
	 */
	public function mail($title, $message, $params = array(), $type = self::TYPE_INFO) {
		return self::add('mail', $title, $message, $params, $type);
	}


	/**
	 * Логирование событий объявлений
	 * @param string $title залоговок
	 * @param string $message текст
	 * @param integer $type (self::TYPE_*)
	 * @return integer 
	 */
	public function object($title, $message, $params = array(), $type = self::TYPE_INFO) {
		return self::add('object', $title, $message, $params, $type);
	}


	/**
	 * Логирование событий пользователя
	 * @param string $title залоговок
	 * @param string $message текст
	 * @param integer $type (self::TYPE_*)
	 * @return integer 
	 */
	public function user($title, $message, $params = array(), $type = self::TYPE_INFO) {
		return self::add('user', $title, $message, $params, $type);
	}


}