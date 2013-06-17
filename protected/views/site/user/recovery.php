<?php

$this->breadcrumbs += array(Yii::t('nav', 'FORM_RECOVERY_PASSWORD'));
?>

<div class="form">

	<p><?php echo Yii::t('nav', 'FORM_RECOVERY_PASSWORD_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'login-form',
		'action' => Yii::app()->createUrl($this->route),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'email', array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, 'email', array('class' => '')); ?>
			<?php echo $form->error($model, 'email', array('class' => 'errorMessage')); ?>
			
		</div>
	
		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_RECOVERY_PASSWORD_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>