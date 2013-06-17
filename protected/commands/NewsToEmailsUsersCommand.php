<?php

/**
 * Формирование email`ов для отправки новостей пользователям
 */
class NewsToEmailsUsersCommand extends CConsoleCommand {


	public function run($args) {
		$sql = 'SELECT to_email_news()';
		$command = \Yii::app()->db->createCommand($sql);
		$rowCount = $command->execute();


		return 0; // всё хорошо, выходим с кодом 0
	}


}