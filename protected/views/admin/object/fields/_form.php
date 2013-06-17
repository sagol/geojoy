<?php Yii::app()->clientScript->registerScript('show-orders-values', "
$('#type').change(function(){
	var val = $(this).val();
	if(val == " . app\fields\Field::DROPLIST . " || val == " . app\fields\Field::SELECT . " || val == " . app\fields\Field::CHECKLIST . " || val == " . app\fields\Field::RADIOLIST . ") $('#div_orders_values').removeClass('hide');
	else $('#div_orders_values').addClass('hide');
});

$('#type').trigger('change');
");
?>
<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'app-models-object--fields-form',
	'enableAjaxValidation' => true,
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'type', array('for' => 'type')); ?>
		<?php echo $form->dropDownList($model, 'type', $model->typeField(), array('class' => '', 'id' => 'type')); ?>
		<?php echo $form->error($model, 'type', array('id' => 'type_err', 'inputID' => 'type')); ?>
		<div class="hint" id="hint-type"><?php echo \Yii::t('admin', 'FIELDS_HELP_TYPE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'parent', array('for' => 'parent')); ?>
		<?php echo $form->dropDownList($model, 'parent', $model->parent_arr1, array('class' => '', 'id' => 'parent')); ?>
		<?php echo $form->error($model, 'parent', array('id' => 'parent_err', 'inputID' => 'parent')); ?>
		<div class="hint" id="hint-parent"><?php echo \Yii::t('admin', 'FIELDS_HELP_PARENT'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'name', array('for' => 'name')); ?>
		<?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20, 'class' => '', 'id' => 'name')); ?>
		<?php echo $form->error($model, 'name', array('id' => 'name_err', 'inputID' => 'name')); ?>
		<div class="hint" id="hint-name"><?php echo \Yii::t('admin', 'FIELDS_HELP_NAME'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'title', array('for' => 'title')); ?>
		<?php echo $form->textField($model, 'title', array('size' => 50, 'maxlength' => 50, 'class' => '', 'id' => 'title')); ?>
		<?php echo $form->error($model, 'title', array('id' => 'title_err', 'inputID' => 'title')); ?>
		<div class="hint" id="hint-title"><?php echo \Yii::t('admin', 'FIELDS_HELP_TITLE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'units', array('for' => 'units')); ?>
		<?php echo $form->textField($model, 'units', array('class' => '', 'id' => 'units')); ?>
		<?php echo $form->error($model, 'units', array('id' => 'units_err', 'inputID' => 'units')); ?>
		<div class="hint" id="hint-units"><?php echo \Yii::t('admin', 'FIELDS_HELP_UNITS'); ?></div>
	</div>

	<div class="control-group hide" id="div_orders_values">
		<?php echo $form->labelEx($model, 'orders_values', array('for' => 'orders_values')); ?>
		<?php echo $form->dropDownList($model, 'orders_values', array(\Yii::t('admin', 'FIELDS_HELP_ORDERS_VALUES_ORDER_MANUALLLY'), \Yii::t('admin', 'FIELDS_HELP_ORDERS_VALUES_ORDER_ALPHABETICAL')), array('class' => '', 'id' => 'orders_values')); ?>
		<?php echo $form->error($model, 'orders_values', array('id' => 'orders_values_err', 'inputID' => 'orders_values')); ?>
		<div class="hint" id="hint-orders_values"><?php echo \Yii::t('admin', 'FIELDS_HELP_ORDERS_VALUES'); ?></div>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? \Yii::t('admin', 'CREATE') : \Yii::t('admin', 'SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>