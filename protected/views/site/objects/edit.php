<?php
$this->breadcrumbs += array(
	$model->value('title') => array('/site/objects/view', 'id' => $model->idobjects),
	Yii::t('nav', 'FORM_OBJECT_EDIT'));
?>

<h1><?php echo Yii::t('nav', 'FORM_OBJECT_EDIT'); ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>