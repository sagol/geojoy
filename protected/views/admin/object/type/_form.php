<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'app-models-object--type-form',
	'enableAjaxValidation' => true,
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
    <?php echo $form->labelEx($model, 'name', array('for' => 'name')); ?>
		<?php echo $form->textField($model, 'name', array('class' => '', 'id' => 'name')); ?>
		<?php echo $form->error($model, 'name', array('id' => 'name_err', 'inputID' => 'name')); ?>
		<div class="hint" id="hint-name"><?php echo \Yii::t('admin', 'OBJECTS_TYPES_HELP_NAME'); ?></div>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? \Yii::t('admin', 'CREATE') : \Yii::t('admin', 'SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>