<?php

namespace app\modules\messages\controllers;

/**
 * 
 */
class ThreadsController extends \app\components\Controller {


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
				'actions' => array('index', 'thread', 'answer', 'del', 'print'),
				'roles' => array('authorized'),
			),

			array('deny',  // deny all users
				'users' => array('*'),
			),
		);
	}


	/**
	 * 
	 */
	public function actionIndex($page = 1, $filter = 'all') {
		/*$model = new \app\modules\messages\models\Threads;

		$this->render('threads', array(
			'filter' => $filter,
			'threads' => $model->getThreads($page, $filter),
			'model' => $model,
		));*/

		$model = new \app\modules\messages\models\Test;

		if($filter == 'all')
			$this->render('all', array(
				'model' => $model,
				'filter' => $filter,
			));
		else
			$this->render('index', array(
				'model' => $model,
				'filter' => $filter,
			));
	}


	public function actionDel() {
		$threads = $_GET['threads'];
		$model = new \app\modules\messages\models\Threads;
		echo $model->del($threads);
		\Yii::app()->end();
	}


	public function actionThread($id, $user = null, $filter = 'all') {
		$threads = new \app\modules\messages\models\Threads;
		$model = new \app\modules\messages\models\MessageForm;

		if(isset($_POST['messages'])) {
			$model->attributes = $_POST['messages'];
			if($model->validate()) {
				// получаем данные кому сообщение, какое объявление и какой id треда
				$threads->getThread($id, $user, $model, 1);
				if($model->save()) {
					\Yii::app()->message->add(\Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_MESSAGE_SEND'));
					$this->redirect(array('/messages/threads/index'));
				}
			}
		}
		list($thread, $replay) = $threads->getThread($id, $user, $model);

		$this->render('thread', array(
			'filter' => $filter,
			'id' => $id,
			'thread' => $thread,
			'model' => $model,
			'writer' => \Yii::app()->user->id,
			'replay' => $replay,
		));
	}


	public function actionPrint($id) {
		$threads = new \app\modules\messages\models\Threads;
		$model = new \app\modules\messages\models\MessageForm;
		list($thread, $replay) = $threads->getThread($id, null, $model);

		$this->layout = '//layouts/siteContainerPrint';
		$this->render('threadPrint', array(
			'thread' => $thread,
			'writer' => \Yii::app()->user->id,
			'user' => $model->user,
		));
	}


}
