<?php

$this->menu = array(
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('app-models-object--fields-type-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'TIES_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php 

	$this->widget('\app\components\core\GridView', array(
		'id' => 'app-models-object--fields-type-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'cssFile'  =>  false,
		'itemsCssClass'  =>  'admin table table-striped',
		'columns' => array(
			array('name' => 'idobj_ties', 'id' => 'table_id'),
			array('name' => 'idobj_type', 'id' => 'table_td', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'idobj_fields', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'idobj_ties_groups', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'orders', 'id' => 'table_td2'),
			array('name' => 'filter', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'required', 'id' => 'table_td2'),
			array('name' => 'disabled', 'id' => 'table_td2'),
			array(
				'class' => '\app\components\ButtonColumn',
				'deleteConfirmation' => \Yii::t('admin', 'CONFIRM_DELETE_TIES'),
			),
		),
	));
?>