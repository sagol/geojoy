<?php

$this->breadcrumbs += array(
	$model->votedname . ' vs ' . $model->username,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idusers), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE'))),
);
?>

<h1><?php echo \Yii::t('admin', 'KARMA_ID', array('{id}' => $model->idusers)); ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data' => $model,
	'htmlOptions' => array('class' => 'admin table table-striped'),
	'attributes' => array(
		'idobj_karma',
		array(
			'label' => \Yii::t('admin', 'KARMA_COLUMN_USER'),
			'value' => '(ID: ' . $model->idusers . ') ' . $model->username
		),
		array(
			'label' => \Yii::t('admin', 'KARMA_COLUMN_VOTED'),
			'value' => '(ID: ' . $model->voted . ') ' . $model->votedname
		),
		'comment',
		'points',
		'moderated',
	),
)); ?>
