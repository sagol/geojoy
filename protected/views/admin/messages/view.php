<?php
$this->breadcrumbs += array(
	$model->idmessages,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idmessages), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),
);
?>

<h1><?php echo \Yii::t('admin', 'MESSAGES_ID', array('{id}' => $model->idmessages)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idmessages',
		'notread',
		'reservation',
		'writer_val',
		'replay_val',
		'text:html',
		'date',
	),
)); ?>
