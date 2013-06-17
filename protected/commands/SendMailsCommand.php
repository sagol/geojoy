<?php

/**
 * Отправка новостей на email пользователей 
 */
class SendMailsCommand extends CConsoleCommand {


	public function run($args) {
		$defaultLanguage = \Yii::app()->getLanguage();
		$sql = 'SELECT n.idnews, n.title, n.brief, n.news, n.create, u.idusers, e.idemails 
			FROM emails e
			LEFT JOIN users u USING(idusers)
			LEFT JOIN news n ON n.idnews = e.id
			WHERE e.status = 0 AND e.type = 1
			LIMIT :limit';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':limit', \Yii::app()->params['sendMailsCount'], \PDO::PARAM_INT);
		$dataReader = $command->query();

		$sql = 'UPDATE emails
				SET send = NOW(), status = :status, error = :error
				WHERE idemails = :id';
		$command1 = \Yii::app()->db->createCommand($sql);

		while(($data = $dataReader->read()) !== false) {
			$user = new \app\models\users\User;
			$find = $user->user($data['idusers'], 'sendEmail');
			if(!$find) continue;

			$userName = $user->value(\app\models\users\User::NAME_NAME);
			$userEmail = $user->value(\app\models\users\User::NAME_EMAIL);
			$language = $user->field(\app\models\users\User::NAME_MAIN_LANGUAGE)->getValue();

			if($language == null) $language = $defaultLanguage;
			\Yii::app()->setLanguage($language);

			$mailer = \Yii::app()->mailer;
			$mailer->IsHTML(true);
			$mailer->AddAddress($userEmail);
			$mailer->Subject = \Yii::t('mail', 'MAIL_NEWS_SUBJECT');

			$data = \app\modules\news\models\News::unPackMultiLang($data);
			$mailer->Body = $this->renderFile(\Yii::getPathOfAlias('app.views.email.news') . '.php', 
				array(
					'title' => $data['title'][$language],
					'news' => $data['news'][$language],
					'date' => $data['create'],
					'username' => $userName,
				), true);

			if($mailer->Send()) {
				\Yii::app()->appLog->mail('MAIL_NEWS_TITLE', 'MAIL_NEWS', array('{user}' => $userName, '{email}' => $userEmail, '{news}' => $data['idnews']));
				$status = 1;
				$error = null;
			}
			else {
				$error = $mailer->ErrorInfo;
				\Yii::app()->appLog->mail('MAIL_NEWS_TITLE', 'MAIL_NEWS_ERROR', array('{user}' => $userName, '{email}' => $userEmail, '{news}' => $data['idnews'], '{error}' => $error), \app\components\AppLog::TYPE_ERROR);
				$status = 2;
			}

			$command1->bindValue(':id', $data['idemails'], \PDO::PARAM_INT);
			$command1->bindValue(':error', $error, \PDO::PARAM_INT);
			$command1->bindValue(':status', $status, \PDO::PARAM_INT);
			$rowCount = $command1->execute();
		}

		return 0; // всё хорошо, выходим с кодом 0
	}


}
