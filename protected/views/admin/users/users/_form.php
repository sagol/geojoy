<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'users-users-form',
	'enableAjaxValidation' => true,
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'role', array('for' => 'role')); ?>
		<?php echo $form->dropDownList($model, 'role', $model->role_arr1, array('class' => '', 'id' => 'role')); ?>
		<?php echo $form->error($model, 'role', array('id' => 'role_err', 'inputID' => 'role')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'profile', array('for' => 'profile')); ?>
		<?php echo $form->dropDownList($model, 'profile', $model->profile_arr1, array('class' => '', 'id' => 'profile')); ?>
		<?php echo $form->error($model, 'profile', array('id' => 'profile_err', 'inputID' => 'profile')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'status', array('for' => 'status')); ?>
		<?php echo $form->dropDownList($model, 'status', $model->status_arr1, array('class' => '', 'id' => 'status')); ?>
		<?php echo $form->error($model, 'status', array('id' => 'status_err', 'inputID' => 'status')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'email', array('for' => 'email')); ?>
		<?php echo $form->textField($model, 'email', array('class' => '', 'id' => 'email')); ?>
		<?php echo $form->error($model, 'email', array('id' => 'email_err', 'inputID' => 'email')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'name', array('for' => 'name')); ?>
		<?php echo $form->textField($model, 'name', array('class' => '', 'id' => 'name')); ?>
		<?php echo $form->error($model, 'name', array('id' => 'name_err', 'inputID' => 'name')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'karma', array('for' => 'karma')); ?>
		<?php echo $form->textField($model, 'karma', array('class' => '', 'id' => 'karma')); ?>
		<?php echo $form->error($model, 'karma', array('id' => 'karma_err', 'inputID' => 'karma')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? \Yii::t('admin', 'CREATE') : \Yii::t('admin', 'SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>