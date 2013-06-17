<?php

namespace app\modules\messages\components\widgets;

/**
 * Виджет формы сообщения
 */
class Message extends \app\components\core\Widget {


	/**
	 * Адрес на который будет отправлен запрос в формате роутера Yii ('ControllerID/ActionID')
	 * @var string $action the URL route.
	 */
	public $action = array('/messages/threads/writer');
	public $view = 'widget';

	/**
	 * Выполнение виджета
	 * @return boolean 
	 */
	public function run() {
		if(!\Yii::app()->user->id) return false;

		$model = new \app\modules\messages\models\MessageForm;

		$config['action'] = $this->action;
		$config['model'] = $model;

		$this->render($this->view, $config);
	}


}