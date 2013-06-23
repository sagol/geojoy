<?php

namespace app\components;

\Yii::import('zii.widgets.grid.CDataColumn');

/**
 * Вывод данных столбца
 */
class DataColumnVal extends \CDataColumn {


	/**
	 * Вывод значения вместо id
	 * @param integer $row
	 * @param mixed $data 
	 */
	protected function renderDataCellContent($row, $data) {
		if($data instanceof \app\models\object\Fields) echo $data->{$this->name . '_val'};
		elseif($data instanceof \CActiveRecord) echo $data->{$this->name . '_val'};
		else parent::renderDataCellContent($row, $data);
	}


}
