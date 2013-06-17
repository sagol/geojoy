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
	$.fn.yiiGridView.update('app-models-object--category-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'CATEGORY_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-object--category-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'idobj_category', 'id' => 'table_id'),
		array('name' => 'img', 'id' => 'table_img', 'class' => '\app\components\ImgColumnVal', 'path' => \Yii::app()->request->getBaseUrl() . \Yii::app()->params['uploadUrl']),
		array('name' => 'idobj_type', 'id' => 'table_td3', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'tree', 'id' => 'table_td2'),
		array('name' => 'name', 'id' => 'table_td'),
		array('name' => 'alias', 'id' => 'table_td2'),
		array('name' => 'moderate', 'id' => 'table_td2'),
		array('name' => 'disabled', 'id' => 'table_td2'),
		array(
			'class' => '\app\components\ButtonColumn',
			'deleteConfirmation' => \Yii::t('admin', 'CONFIRM_DELETE_CATEGORY'),
		),
	),
)); ?>