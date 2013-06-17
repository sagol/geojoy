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
	$.fn.yiiGridView.update('app-models-object--fields-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-object--fields-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'idobj_fields', 'id' => 'table_id'),
		array('name' => 'parent', 'id' => 'table_td3', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'name', 'id' => 'table_td2'),
		array('name' => 'title', 'id' => 'table_td'),
		array('name' => 'type', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'units', 'id' => 'table_td2'),
		array(
			'class' => '\app\components\ButtonColumn',
			'deleteConfirmation' => \Yii::t('admin', 'CONFIRM_DELETE_FIELD'),
			'template' => '{delete} {update} {view} {values}',
			'buttons' => array(
				'values' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("admin/object/fieldsValues/index", array("id" => $data->primaryKey))',
					'visible' => '$data->type >= 8 && $data->type <= 11',
					'options' => array('class' => 'value', 'title' => \Yii::t('admin', 'VALUES')),
				),
			),
		),
	),
)); ?>