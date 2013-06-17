<?php
$this->breadcrumbs += array(
	$model->idnews => array('view', 'id' => $model->idnews),
	\Yii::t('admin', 'EDITING'),
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'VIEW'), 'url' => array('view', 'id' => $model->idnews)),
);
?>

<h1><?php echo \Yii::t('admin', 'NEWS_EDIT', array('{id}' => $model->idnews)); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>