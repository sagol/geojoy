<?php

/**
 * Удаление не активированных email`ов
 */
class ClearEmailsCommand extends CConsoleCommand {


	public function run($args) {
		$sql = "UPDATE users 
			SET {field} = substring({field} FROM position(']' IN {field})+1 FOR position(';' IN {field}) - position(']' IN {field})-1)
			WHERE {field} LIKE 'old:[%' AND substring({field} FROM 6 FOR position(']' IN {field})-6)::timestamp  + :days::interval <= NOW()";
		$sql = str_replace('{field}', \app\models\users\User::NAME_EMAIL, $sql);
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindValue(':days', '1 day', \PDO::PARAM_STR);
		$rowCount = $command->execute();


		return 0; // всё хорошо, выходим с кодом 0
	}


}