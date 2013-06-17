<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'app-models-object--fields-lists-form',
	'enableAjaxValidation' => true,
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php if(!@$id) : ?>
	<div class="control-group">
		<?php echo $form->labelEx($model, 'idobj_fields', array('for' => 'idobj_fields')); ?>
		<?php echo $form->dropDownList($model, 'idobj_fields', $model->idobj_fields_arr1, array('class' => '', 'id' => 'idobj_fields')); ?>
		<?php echo $form->error($model, 'idobj_fields', array('id' => 'idobj_fields_err', 'inputID' => 'idobj_fields')); ?>
		<div class="hint" id="hint-idobj_fields"><?php echo \Yii::t('admin', 'FIELDS_VALUES_HELP_FIELDS'); ?></div>
	</div>
	<?php else: ?>
		<input id="idobj_fields" type="hidden" name="idobj_fields" value="<?php echo $id; ?>">
	<?php endif; ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'parent', array('for' => 'parent')); ?>
		<?php echo $form->dropDownList($model, 'parent', $model->parent_arr1, array('class' => '', 'id' => 'parent')); ?>
		<?php echo $form->error($model, 'parent', array('id' => 'parent_err', 'inputID' => 'parent')); ?>
		<div class="hint" id="hint-parent"><?php echo \Yii::t('admin', 'FIELDS_VALUES_HELP_PARENT'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'value', array('for' => 'value')); ?>
		<?php echo $form->textArea($model, 'value', array('class' => '', 'id' => 'value')); ?>
		<?php echo $form->error($model, 'value', array('id' => 'value_err', 'inputID' => 'value')); ?>
		<div class="hint" id="hint-value"><?php echo \Yii::t('admin', 'FIELDS_VALUES_HELP_VALUE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'orders', array('for' => 'orders')); ?>
		<?php echo $form->textField($model, 'orders', array('class' => '', 'id' => 'orders')); ?>
		<?php echo $form->error($model, 'orders', array('id' => 'orders_err', 'inputID' => 'orders')); ?>
		<div class="hint" id="hint-orders"><?php echo \Yii::t('admin', 'FIELDS_VALUES_HELP_ORDERS'); ?></div>
	</div>

	<div class="control-group noline">
		<?php echo $form->labelEx($model, 'translate', array('for' => 'translate')); ?>
		<?php echo $form->checkBox($model, 'translate', array('class' => '', 'id' => 'translate')); ?>
		<?php echo $form->error($model, 'translate', array('id' => 'translate_err', 'inputID' => 'translate')); ?>
	</div>

	<div class="control-group noline">
    <?php echo $form->labelEx($model, 'disabled', array('for' => 'disabled')); ?>
    <?php echo $form->checkBox($model, 'disabled', array('class' => '', 'id' => 'disabled')); ?>
		<?php echo $form->error($model, 'disabled', array('id' => 'disabled_err', 'inputID' => 'disabled')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? \Yii::t('admin', 'CREATE') : \Yii::t('admin', 'SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
<?php
	$url = Yii::app()->createUrl('admin/object/fieldsValues/getParentFieldData');
	if(Yii::app()->request->enableCsrfValidation) {
		$csrfTokenName = Yii::app()->request->csrfTokenName;
		$csrfToken = Yii::app()->request->csrfToken;
		$csrf = "'$csrfTokenName':'$csrfToken',";
	}
	else $csrf = '';

	$jquery = <<<JQUERY
	$("#idobj_fields").change(function () {
		$.ajax({
			type: "POST",
			url: "$url",
			cache: false,
			data: {
				field: $(this).val(),
				$csrf
			},
			success: function(html){
				$("#parent").html(html);
				$("#parent").val($model->parent);
				$("#parent").trigger('change');
			}
		});
	});
JQUERY;
	Yii::app()->getClientScript()->registerScript('getFieldData-parent', $jquery);
	$jquery = "	if($('#idobj_fields').val() !== null) $('#idobj_fields').trigger('change');";
	Yii::app()->getClientScript()->registerScript('change-idobj_fields', $jquery);

?>
</div>