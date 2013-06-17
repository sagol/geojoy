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
	$.fn.yiiGridView.update('app-models-object--type-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'OBJECTS_TYPES_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-object--type-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'idobj_type', 'id' => 'table_id'),
		array('name' => 'name', 'id' => 'table_td'),
		array('name' => 'name', 'id' => 'table_td', 'value' => 'Yii::t("lists", $data->name);'),
		array(
			'class' => '\app\components\ButtonColumn',
		),
	),
)); ?>
