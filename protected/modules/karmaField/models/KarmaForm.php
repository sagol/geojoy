<?php

namespace app\modules\karmaField\models;

/**
 * Форма кармы
 */
class KarmaForm extends \CFormModel {


	/**
	 * Пользователь
	 * @var integeer 
	 */
	public $idusers;
	/**
	 * Голосовавший пользователь
	 * @var integeer 
	 */
	public $voted;
	/**
	 * Поднятие/опускание кармы
	 * @var string 
	 */
	public $action;
	/**
	 * Комментарий к карме
	 * @var string 
	 */
	public $comment;


	/**
	 * Правила валидации модели
	 * @return array 
	 */
	public function rules() {
		return array(
			array('idusers, voted, comment, action', 'required'),
			array('idusers, voted, moderated, points', 'numerical', 'integerOnly' => true),
			array('comment', 'length', 'max' => 500),
		);
	}


	/**
	 * Labels для полей формы
	 * @return array 
	 */
	public function attributeLabels() {
		return array(
			'comment' => \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_FIELD_COMMENT'),
		);
	}


	/**
	 * Голосование
	 * @return boolean 
	 */
	public function voting() {
		if(($voted = \Yii::app()->user->id) != true) return false;
		if($this->action == 'up') $points = 1;
		elseif($this->action == 'down') $points = -1;

		$sql = 'INSERT INTO obj_karma (idusers, voted, comment, points) 
			VALUES(:id, :voted, :comment, :points)';
		$command = \Yii::app()->db->createCommand($sql);
		$command->bindParam(':id', $this->idusers, \PDO::PARAM_INT);
		$command->bindParam(':voted', $this->voted, \PDO::PARAM_INT);
		$command->bindParam(':comment', $this->comment, \PDO::PARAM_INT);
		$command->bindParam(':points', $points, \PDO::PARAM_INT);
		$rowCount = $command->execute();


		return $rowCount;
	}


}