<?php

namespace app\components\core;

\Yii::import('zii.widgets.grid.CGridView');

/**
 * Виджет вывода табличной информации
 */
class GridView extends \CGridView {


	public $cssFile = false;
	public $pager = array(
		'class' => 'CLinkPager',
		'cssFile' => false,
		'header' => false,
	);


}