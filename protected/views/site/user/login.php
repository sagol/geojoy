<?php
$this->breadcrumbs += array(Yii::t('nav', 'FORM_LOGIN'));
?>
<div class="form social">
	<?php $this->widget('ext.eauth.EAuthWidget', array('cssFile' => false)); ?>
	<span class="alarm"><?php echo $socialError; ?></span>
	<?php if($socialError && $errorCode == \app\components\UserIdentity::ERROR_YOU_NOT_ACTIVATED) : ?>
		<?php echo CHtml::link(
			Yii::t('nav', 'NAV_SEND_MAIL_ACTIVATION'),
			array('/site/user/sendMailActivation'),
			array('class' => 'repeat')
		);?>
		<?php echo CHtml::link(
			Yii::t('nav', 'NAV_SET_NEW_MAIL'),
			array('/site/user/setNewMail'),
			array('class' => 'another')
		);?>
	<?php endif ?>
</div>
<div class="form">
	
	<p><?php echo Yii::t('nav', 'FORM_LOGIN_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'login-form',
		'action' => array('/site/user/login'),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
			<p class="errorSummary"><?php echo $form->errorSummary($model); ?></p>
		</div>

		<?php if(!$socialError && $errorCode == \app\components\UserIdentity::ERROR_YOU_NOT_ACTIVATED) : ?>
		<div class="control-group">
			<?php echo CHtml::link(
				Yii::t('nav', 'NAV_SEND_MAIL_ACTIVATION'),
				array('/site/user/sendMailActivation'),
				array('class' => '')
			);?>
		</div>
		<?php endif ?>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'email', array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, 'email', array('class' => '')); ?>
			<?php echo $form->error($model, 'email', array('class' => 'errorMessage')); ?>
		</div>
	
		<div class="control-group">
			<?php echo $form->labelEx($model, 'password', array('class' => 'control-label')); ?>
			<?php echo $form->passwordField($model, 'password', array('class' => '')); ?>
			<?php echo $form->error($model, 'password', array('class' => 'errorMessage')); ?>
			
		</div>

		<div class="control-group noline">
			<?php echo $form->checkBox($model, 'rememberMe'); ?>
			<?php echo $form->label($model, 'rememberMe', array('class' => 'control-label')); ?>
			<?php echo $form->error($model, 'rememberMe', array('class' => 'errorMessage')); ?>
		</div>
	
		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_LOGIN_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
	<?php echo CHtml::link(
  	Yii::t('nav', 'NAV_REGISTRATION'),
  	array('/site/user/registration'),
  	array('class' => 'registration')
  );?>
  <?php echo CHtml::link(
  	Yii::t('nav', 'NAV_RECOVERY_PASSWORD'),
  	array('/password/recovery'),
  	array('class' => 'recovery')
  );?>
  <div class="clear"></div>
</div>
