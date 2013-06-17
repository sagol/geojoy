<?php
$label = Yii::t('nav', 'FORM_OBJECT_ADD');

$this->breadcrumbs += array($label);
?>

<h1><?php echo $label; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model, 'adv' => $adv)); ?>