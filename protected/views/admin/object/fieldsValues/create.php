<?php
$this->breadcrumbs += array(
	\Yii::t('admin', 'CREATING'),
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
);
?>

<h1><?php echo \Yii::t('admin', 'FIELDS_VALUES_CREATE'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'id' => $id)); ?>