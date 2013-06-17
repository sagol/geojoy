<?php

$this->breadcrumbs += array(Yii::t('nav', 'FORM_ACTIVATION'));
?>

<div class="form">

	<p><?php echo Yii::t('nav', 'FORM_ACTIVATION_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'activation-form',
		'action' => array('/site/user/activation'),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'code', array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, 'code', array('class' => '')); ?>
			<?php echo $form->error($model, 'code', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_ACTIVATION_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>
