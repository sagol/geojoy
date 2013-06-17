<?php

/**
 * Удаление данных из фильтра для объявлений с истекшим сроком жизни. 
 */
class CheckLifetimeCommand extends CConsoleCommand {


	const LIMIT = 50;


	public function run($args) {
		if(Yii::app()->mutex->lock('checkLifetime', 600)) {
			$sql = 'SELECT idobjects 
				FROM objects 
				WHERE lifetime_date < NOW() AND disabled = 0
				LIMIT :limit offset :offset';
			$command = Yii::app()->db->createCommand($sql);
			$command->bindValue(':limit', self::LIMIT, \PDO::PARAM_INT);

			$offset = 0;
			$ids = array();
			while(true) {
				$command->bindParam(':offset', $offset, \PDO::PARAM_INT);
				$dataReader = $command->query();
				if(($data = $dataReader->readAll()) == false) break;

				foreach($data as $id) {
					$object = \app\models\object\Object::load($id['idobjects'], \app\managers\Manager::ACCESS_TYPE_EDIT);
					$object->getManager()->fieldsUpdateCount('-');
					$ids[] = $id['idobjects'];
				}

				$sql = 'UPDATE objects 
					SET disabled = 2
					WHERE idobjects IN (' . implode(',', $ids) . ')';
				Yii::app()->db->createCommand($sql)->execute();

				$offset += self::LIMIT;
			}

			Yii::app()->mutex->unlock();
		}

		return 0; // всё хорошо, выходим с кодом 0
	}


}