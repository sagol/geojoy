<?php

namespace app\components;

\Yii::import('zii.widgets.grid.CDataColumn');

/**
 * Вывод данных столбца
 */
class ImgColumnVal extends \CDataColumn {


	public $path;


	/**
	 * Вывод значения вместо id
	 * @param integer $row
	 * @param mixed $data 
	 */
	protected function renderDataCellContent($row, $data) {
		if($this->value !== null)
			$value = $this->evaluateExpression($this->value, array('data' => $data, 'row' => $row));
		else if($this->name !== null)
			$value = \CHtml::value($data, $this->name);
		echo $value === null ? $this->grid->nullDisplay : '<img src="' . $this->path . $value . '"/>';
	}


}