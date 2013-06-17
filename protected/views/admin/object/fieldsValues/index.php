<?php

$this->menu = array(
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create', 'id' => $id), 'linkOptions' => array('target' => '_blank')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('app-models-object--fields-lists-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_VALUES_LIST'); ?></h1>
<?php if($id) echo 

\Yii::t('admin', 'FIELD_NAME', array('{name}' => $fieldName)); ?>
<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php 
	if(!$id)
		$columns = array(
			array('name' => 'idobj_fields_values', 'id' => 'table_id'),
			array('name' => 'idobj_fields', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'parent', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'value', 'id' => 'table_td'),
			array('name' => 'orders', 'id' => 'table_td2'),
			array('name' => 'translate', 'id' => 'table_td2'),
			array('name' => 'disabled', 'id' => 'table_td2'),
			array(
				'class' => '\app\components\ButtonColumn',
			)
		);
	else 
		$columns = array(
			array('name' => 'parent', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
			array('name' => 'idobj_fields_values', 'id' => 'table_td1'),
			array('name' => 'value', 'id' => 'table_td'),
			array('name' => 'orders', 'id' => 'table_td2'),
			array('name' => 'translate', 'id' => 'table_td2'),
			array('name' => 'disabled', 'id' => 'table_td2'),
			array(
				'class' => '\app\components\ButtonColumn',
			)
		);

	$this->widget('\app\components\core\GridView', array(
		'id' => 'app-models-object--fields-lists-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'cssFile'  =>  false,
		'itemsCssClass'  =>  'admin table table-striped',
		'columns' => $columns,
	));
?>