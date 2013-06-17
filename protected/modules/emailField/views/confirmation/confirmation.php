<?php

$this->breadcrumbs += array(Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL'));
?>

<div class="form">
	<h2><?php echo Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL'); ?></h2>

	<p><?php echo Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'confirmation-email-form',
		'action' => array('confirmation/index'),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
		</div>

		<div class="control-group">
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'code'); ?>
			<?php echo $form->textField($model, 'code', array('class' => '')); ?>
			<?php echo $form->error($model, 'code', array('class' => 'alert-message-span')); ?>
		</div>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>