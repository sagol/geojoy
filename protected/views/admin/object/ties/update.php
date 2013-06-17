<?php
$this->breadcrumbs += array(
	$model->idobj_ties => array('view', 'id' => $model->idobj_ties),
	\Yii::t('admin', 'EDITING'),
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'VIEW'), 'url' => array('view', 'id' => $model->idobj_ties)),
);
?>

<h1><?php echo \Yii::t('admin', 'TIES_EDIT', array('{id}' => $model->idobj_ties)); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>