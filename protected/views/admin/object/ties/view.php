<?php

$this->breadcrumbs += array(
	$model->idobj_ties,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idobj_ties)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idobj_ties), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),

);
?>

<h1><?php echo \Yii::t('admin', 'TIES_ID', array('{id}' => $model->idobj_ties)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idobj_ties',
		array(
			'label' => \Yii::t('admin', 'TIES_COLUMN_TYPE'),
			'value' => '(ID: ' . $model->idobj_type . ') ' . $model->idobj_type_val
		),
		array(
			'label' => \Yii::t('admin', 'TIES_COLUMN_FIELD'),
			'value' => '(ID: ' . $model->idobj_fields . ') ' . $model->idobj_fields_val
		),
		array(
			'label' => \Yii::t('admin', 'TIES_COLUMN_TIES_GROUP'),
			'value' => '(ID: ' . $model->idobj_ties_groups . ') ' . $model->idobj_ties_groups_val
		),
		'orders',
		'filter_val',
		'required',
		'disabled',
	),
)); ?>
