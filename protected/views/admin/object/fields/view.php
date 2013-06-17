<?php
$this->breadcrumbs += array(
	$model->name,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idobj_fields)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idobj_fields), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),
);
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_ID', array('{id}' => $model->idobj_fields)); ?></h1>


<?php
	$type = $model->type;
	$available = array(
		app\fields\Field::DROPLIST,
		app\fields\Field::SELECT,
		app\fields\Field::CHECKLIST,
		app\fields\Field::RADIOLIST,
	);
if(in_array($model->type, $available))
	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'htmlOptions' => array('class' => 'admin table table-striped'),
		'attributes' => array(
			'idobj_fields',
			'parent_val',
			'type_val',
			'name',
			'title',
			'units',
			array(
				'label' => \Yii::t('admin', 'FIELDS_FIELD_ORDERS_VALUES'),
				'value' => $model->orders_values ? \Yii::t('admin', 'FIELDS_HELP_ORDERS_VALUES_ORDER_ALPHABETICAL') : \Yii::t('admin', 'FIELDS_HELP_ORDERS_VALUES_ORDER_MANUALLLY'),
			),
		),
	));
else
	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'htmlOptions' => array('class' => 'admin table table-striped'),
		'attributes' => array(
			'idobj_fields',
			'parent_val',
			'type_val',
			'name',
			'title',
			'units',
		),
	));
?>