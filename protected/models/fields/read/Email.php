<?php

namespace app\models\fields\read;

/**
 * Поле email
 */
class Email extends Field {


	protected $_oldEmail;


	/**
	 * Инициализация поля 
	 */
	public function init() {
		$this->_type = self::EMAIL;

		parent::init();
	}


	/**
	 * Распаковка значения поля
	 * @param type string
	 * @return boolean 
	 */
	public function unPackValue($value) {
		$sql = $this->sqlSelect();
		$value = $value[$sql[0]['field']];
		if($this->isSetFieldIndex()) {
			if(isset($value[$this->_fieldIndex])) $value = $value[$this->_fieldIndex];
			else return false;
		}

		if(substr($value, 0, 4) == 'old:') {
			$f = strpos($value, ']')+1;
			$date = new \DateTime(substr($value, 5, $f-6));
			$curDate = new \DateTime();
			$interval = $date->diff($curDate);
			$f1 = strpos($value, ';');
			if($interval->days == 0) {
				$this->_oldEmail = substr($value, $f, $f1-$f);
				$value = substr($value, $f1+1, -1);
			}
			else $value = substr($value, $f, $f1-$f);
		}
		$this->_value = $value;


		return true;
	}


	protected function _unPackOldValue($value) {
		$oldEmail = null;
		if(substr($value, 0, 4) == 'old:') {
			$f = strpos($value, ']')+1;
			$date = new \DateTime(substr($value, 5, $f-6));
			$curDate = new \DateTime();
			$interval = $date->diff($curDate);
			$f1 = strpos($value, ';');
			if($interval->days == 0) {
				$oldEmail = substr($value, $f, $f1-$f);
				$value = substr($value, $f1+1, -1);
			}
			else $value = substr($value, $f, $f1-$f);
		}


		return array($oldEmail, $value);
	}


}