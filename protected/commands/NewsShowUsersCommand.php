<?php

/**
 * Формирование всплывающих сообщений для показа новостей 
 */
class NewsShowUsersCommand extends CConsoleCommand {


	public function run($args) {
		$sql = 'SELECT show_news()';
		$command = \Yii::app()->db->createCommand($sql);
		$rowCount = $command->execute();


		return 0; // всё хорошо, выходим с кодом 0
	}


}