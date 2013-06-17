<?php

namespace app\components;

class Message {


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
	 * Инициализация
	 */
	public function init() {
	}


	public function add($message, $type = self::TYPE_INFO) {
		if(empty($message)) return;

		$session = \Yii::app()->session;
		$msg = $session->get('message');

		if(is_array($message)) {
			foreach($message as $m)
				$msg[] = $m;
		}
		else $msg[] = $message;

		$session->add('message', $msg);
	}


	public function get() {
		$message = \Yii::app()->session->remove('message');
		if(empty($message)) return '';


		return implode('<br>', $message);
	}


}