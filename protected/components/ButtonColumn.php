<?php

namespace app\components;

\Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * 
 */
class ButtonColumn extends \CButtonColumn {


	public $template='{delete} {update} {view}';

	public $viewButtonLabel = '';
	public $viewButtonImageUrl = false;
	public $viewButtonOptions = array('class' => 'view', 'title' => 'view');

	public $updateButtonLabel = '';
	public $updateButtonImageUrl = false;
	public $updateButtonOptions = array('class' => 'update', 'target' => '_blank', 'title' => 'update');

	public $deleteButtonLabel = '';
	public $deleteButtonImageUrl = false;
	public $deleteButtonOptions = array('class' => 'delete', 'title' => 'delete');


}