<?php

/**
 * Удаление просроченных кодов 
 */
class ClearCodesCommand extends CConsoleCommand {


	public function run($args) {
		$sql = "DELETE FROM codes 
			WHERE NOW() >= date + :days::interval";
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':days', '1 day', \PDO::PARAM_STR);
		$rowCount = $command->execute();


		return 0; // всё хорошо, выходим с кодом 0
	}


}