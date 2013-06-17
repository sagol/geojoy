<?php

namespace app\components;

\Yii::import('zii.widgets.grid.CDataColumn');

/**
 * Вывод даты
 */
class DateTimeColumn extends \CDataColumn {


	/**
	 * Вывод даты
	 * @param integer $row
	 * @param mixed $data 
	 */
	protected function renderDataCellContent($row, $data) {
		$name = $this->name;

		if($this->value !== null)
			$value = $this->evaluateExpression($this->value, array('data' => $data, 'row' => $row));
		else if($this->name !== null)
			$value = \CHtml::value($data, $name);
		echo $value === null ? $this->grid->nullDisplay : \Yii::app()->dateFormatter->formatDateTime($value, 'medium', 'medium');
	}


}