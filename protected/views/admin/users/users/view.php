<?php

$this->breadcrumbs += array(
	$model->name,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idusers)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idusers), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE_USER'))),
);
?>

<h1><?php echo \Yii::t('admin', 'USER_ID', array('{id}' => $model->idusers)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idusers',
		'profile',
		'status',
		'date',
		'email',
		'name',
		'karma',
	),
)); ?>
