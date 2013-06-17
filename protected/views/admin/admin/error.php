<?php

$this->breadcrumbs += array(
	\Yii::t('admin', 'BREADCRUMBS_ERROR'),
);
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php echo CHtml::encode($message); ?>
</div>