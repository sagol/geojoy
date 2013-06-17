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
	$.fn.yiiGridView.update('users-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'USERS_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-users--users-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'idusers', 'id' => 'table_id'),
		array('name' => 'profile', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'role', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'name', 'id' => 'table_td2'),
		array('name' => 'status', 'id' => 'table_td2', 'class' => '\app\components\DataColumnVal'),
		array('name' => 'date', 'id' => 'table_td2', 'class' => '\app\components\DateTimeColumn'),
		array('name' => 'email', 'id' => 'table_td'),
		array(
			'class' => '\app\components\ButtonColumn',
		),
	),
)); ?>
