<?php

$this->breadcrumbs += array(
	$model->name,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idobj_category)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idobj_category), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),
);
?>

<h1><?php echo \Yii::t('admin', 'CATEGORY_ID', array('{id}' => $model->idobj_category)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idobj_category',
		array(
			'label' => \Yii::t('admin', 'OBJECTS_TYPES_COLUMN_TYPE'),
			'value' => '(ID: ' . $model->idobj_type . ') ' . $model->idobj_type_val
		),
		'tree',
		'name',
		'alias',
		'description',
		'moderate',
		'disabled',
	),
)); ?>
