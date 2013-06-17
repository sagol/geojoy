<?php

$this->breadcrumbs += array(
	$model->value,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idobj_fields_values)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idobj_fields_values), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),

);
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_VALUES_ID', array('{id}' => $model->idobj_fields_values)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idobj_fields_values',
		array(
			'label' => \Yii::t('admin', 'FIELDS_VALUES_COLUMN_FIELD'),
			'value' => '(ID: ' . $model->idobj_fields . ') ' . $model->idobj_fields_val
		),
		'parent_val',
		'value',
		'translate',
		'orders',
		'disabled',
	),
)); ?>
