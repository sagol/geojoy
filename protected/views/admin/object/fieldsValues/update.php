<?php
$this->breadcrumbs += array(
	$model->idobj_fields_values => array('view', 'id' => $model->idobj_fields_values),
	\Yii::t('admin', 'EDITING'),
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'VIEW'), 'url' => array('view', 'id' => $model->idobj_fields_values)),
);
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_VALUES_EDIT', array('{id}' => $model->idobj_fields_values)); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>