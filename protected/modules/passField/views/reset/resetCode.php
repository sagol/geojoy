<?php

$this->breadcrumbs += array(Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_CODE_PASS'));
?>

<div class="form">
	<p><?php echo Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_CODE_PASS_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'reset-code-form',
		'action' => array('reset/index'),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
		</div>

		<div class="control-group noline">
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'code'); ?>
			<?php echo $form->textField($model, 'code', array('class' => '')); ?>
			<?php echo $form->error($model, 'code', array('class' => 'alert-message-span')); ?>
		</div>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_CODE_PASS_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>