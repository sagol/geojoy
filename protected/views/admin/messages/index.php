<?php

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('messages-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'MESSAGES_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'messages-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'idmessages', 'id' => 'table_id'),
		array('name' => 'notread', 'id' => 'table_td1'),
		array('name' => 'reservation', 'id' => 'table_td2'),
		array('name' => 'writer', 'id' => 'table_td1', 'type' => 'html', 'value' => array('\app\models\messages\Messages', 'link')),
		array('name' => 'replay', 'id' => 'table_td1', 'type' => 'html', 'value' => array('\app\models\messages\Messages', 'link')),
		array('name' => 'text', 'id' => 'table_td', 'type' => 'html'),
		array('name' => 'date', 'id' => 'table_td3', 'class' => '\app\components\DateTimeColumn'),
		array(
			'class' => '\app\components\ButtonColumn',
			'template' => (Yii::app()->user->checkAccess('admin') ? ' {delete}' : '') . '{view}',
		),
	),
)); ?>
