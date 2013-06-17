<?php

namespace app\modules\messages\controllers;

/**
 * 
 */
class MessagesController extends \app\components\Controller {


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
			/*array('allow',
				'actions' => array('writer', 'writerContacts'),
				'users' => array('*'),
			),

			array('allow',
				'actions' => array('mark'),
				'roles' => array('authorized'),
			),*/

			array('allow',
				'actions' => array('writer', 'writerContacts', 'mark'),
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
	public function actionMark() {
		$id = (int)$_POST['id'];
		$mark = !($_POST['mark'] === 'false');

		$messages = new \app\modules\messages\models\Messages;
		if($messages->mark($id, $mark)) echo '{"status": "ok", "id": ' . $id . '}';
		else echo '{"status": "error", "id": ' . $id . '}';
		\Yii::app()->end();
	}


	public function actionWriter($id, $user = null) {
		$idThread = $id;
		unset($id);
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		if(\Yii::app()->user->getIsGuest()) {
			echo \Yii::t('nav', 'NEED_LOGIN');
			\Yii::app()->end();
		}

		$model = new \app\modules\messages\models\MessageForm;

		if(isset($_POST['messages'])) {
			$model->attributes = $_POST['messages'];

			if($model->validate()) {
				$model->idThread = $idThread;
				$model->idObjects = $idThread;
				$model->reservation = 1;

				// определяем владельца объявления
				// загружаем объявление полностью, т.к. перед созданием сообщение объявление должно находиться в кеше
				if($user === null) {
					$object = \app\models\object\Object::load($idThread, 'read');
					$user = $model->user = $object->idusers;
				}
				else $model->user = $user;

				if($message = $model->save()) {
					$curLanguage = \Yii::app()->getLanguage();
					$id = $message['idmessages'];
					$subscription = \app\models\users\User::subscription($model->user, \app\models\users\User::SUB_PRIVATE_MESSAGE);
					if($subscription['sub']) {
						$user = new \app\models\users\User;
						$find = $user->user($model->user, 'sendEmail');

						if($find) {
							$userName = $user->value(\app\models\users\User::NAME_NAME);
							$language = $user->field(\app\models\users\User::NAME_MAIN_LANGUAGE)->getValue();
							if($language) \Yii::app()->setLanguage($language);
							else \Yii::app()->setLanguage(\Yii::app()->getDefaultLanguage());
						}
						else $username = 'User';

						$mailer = \Yii::app()->mailer;
						$mailer->IsHTML(true);
						$mailer->AddAddress($subscription['email']);
						$mailer->Subject = \Yii::t('mail', 'MAIL_PRIVATE_MESSAGE_SUBJECT');
						$mailer->getView('subscriptionPrivateMessages', array(
							'idThread' => $idThread,
							'text' => $model->text,
							'username' => $userName,
						));
						if($mailer->Send()) \Yii::app()->appLog->mail('MAIL_PRIVATE_MESSAGE_TITLE', 'MAIL_PRIVATE_MESSAGE', array('{message}' => \CHtml::link($id, array('/admin/messages/view/', 'id' => $id)), '{email}' => $subscription['email']));
						else {
							\Yii::app()->appLog->mail('MAIL_PRIVATE_MESSAGE_TITLE', 'MAIL_PRIVATE_MESSAGE_ERROR', array('{message}' => \CHtml::link($id, array('/admin/messages/view/', 'id' => $id)), '{email}' => $subscription['email'], '{error}' => $mailer->ErrorInfo), \app\components\AppLog::TYPE_ERROR);
						}
					}

					\Yii::app()->setLanguage($curLanguage);
					$josn['status'] = 'ok';
					$josn['info'] = \Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_MESSAGE_SEND');
					$josn['message'] = $this->renderPartial('threadMessage', array(
						'message' => $message,
						'writer' => \Yii::app()->user->id,
					), true);
					echo json_encode($josn);
					\Yii::app()->end();
				}
				else {
					$josn['status'] = 'error';
					$josn['info'] = \Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_MESSAGE_NOT_SEND');
					echo json_encode($josn);
					\Yii::app()->end();
				}
			}
			else throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));
		}
		else throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));
	}


	public function actionWriterContacts($user) {
		if(!\Yii::app()->request->isAjaxRequest) throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));

		if(\Yii::app()->user->getIsGuest()) {
			echo \Yii::t('nav', 'NEED_LOGIN');
			\Yii::app()->end();
		}

		$model = new \app\modules\messages\models\MessageForm($this->action->id);

		if(isset($_POST['messages'])) {
			$model->attributes = $_POST['messages'];
			$model->user = $user;
			if($model->validate()) {
				if($message = $model->save()) {
					$curLanguage = \Yii::app()->getLanguage();
					$id = $message['idmessages'];					
					$subscription = \app\models\users\User::subscription($user, \app\models\users\User::SUB_PRIVATE_MESSAGE, true);
					if($subscription['sub']) {
						$user = new \app\models\users\User;
						$find = $user->user($model->user, 'sendEmail');
						if(!$find) $username = 'User';

						$mailer = \Yii::app()->mailer;
						$mailer->IsHTML(true);
						$mailer->AddAddress($subscription['email']);
						$mailer->Subject = \Yii::t('mail', 'MAIL_PRIVATE_MESSAGE_SUBJECT');
						$mailer->getView('subscriptionPrivateMessages', array(
							'idThread' => $idThread,
							'text' => $model->text,
							'username' => $username,
						));

						if($mailer->Send()) \Yii::app()->appLog->mail('MAIL_PRIVATE_MESSAGE_TITLE', 'MAIL_PRIVATE_MESSAGE', array('{message}' => \CHtml::link($id, array('/admin/messages/view/', 'id' => $id)), '{email}' => $subscription['email']));
						else {
							\Yii::app()->appLog->mail('MAIL_PRIVATE_MESSAGE_TITLE', 'MAIL_PRIVATE_MESSAGE_ERROR', array('{message}' => \CHtml::link($id, array('/admin/messages/view/', 'id' => $id)), '{email}' => $subscription['email'], '{error}' => $mailer->ErrorInfo), \app\components\AppLog::TYPE_ERROR);
						}
					}

					\Yii::app()->setLanguage($curLanguage);
					$josn['status'] = 'ok';
					$josn['info'] = \Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_MESSAGE_SEND');
					$josn['message'] = $this->renderPartial('threadMessage', array(
						'message' => $message,
						'writer' => \Yii::app()->user->id,
					), true);
					echo json_encode($josn);
					\Yii::app()->end();
				}
				else {
					$josn['status'] = 'error';
					$josn['info'] = \Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_MESSAGE_NOT_SEND');
					echo json_encode($josn);
					\Yii::app()->end();
				}
			}
			else throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));
		}
		else throw new \CHttpException(400, \Yii::t('main', 'ERROR_INVALID_REQUEST'));
	}


}