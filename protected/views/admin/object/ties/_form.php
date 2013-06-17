<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'app-models-object--fields-type-form',
	'enableAjaxValidation' => true,
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php if($model->isNewRecord) : ?>
	<div class="control-group">
		<?php echo $form->labelEx($model, 'idobj_type', array('for' => 'idobj_type')); ?>
		<?php echo $form->dropDownList($model, 'idobj_type', $model->idobj_type_arr1, array('class' => '', 'id' => 'idobj_type')); ?>
		<?php echo $form->error($model, 'idobj_type', array('id' => 'idobj_type_err', 'inputID' => 'idobj_type')); ?>
		<div class="hint" id="hint-idobj_type"><?php echo \Yii::t('admin', 'OBJECT_HELP_TYPE'); ?></div>
	</div>
	<?php endif ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'idobj_ties_groups', array('for' => 'idobj_ties_groups')); ?>
		<?php echo $form->dropDownList($model, 'idobj_ties_groups', $model->idobj_ties_groups_arr1, array('class' => '', 'id' => 'idobj_ties_groups')); ?>
		<?php echo $form->error($model, 'idobj_ties_groups', array('id' => 'idobj_ties_groups_err', 'inputID' => 'idobj_ties_groups')); ?>
		<div class="hint" id="hint-idobj_ties_groups"><?php echo \Yii::t('admin', 'OBJECT_HELP_TIES_GROUP'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'idobj_fields', array('for' => 'idobj_fields')); ?>
		<?php echo $form->dropDownList($model, 'idobj_fields', $model->idobj_fields_arr1, array('class' => '', 'id' => 'idobj_fields')); ?>
		<?php echo $form->error($model, 'idobj_fields', array('id' => 'idobj_fields_err', 'inputID' => 'idobj_fields')); ?>
		<div class="hint" id="hint-idobj_fields"><?php echo \Yii::t('admin', 'OBJECT_HELP_FIELD'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'orders', array('for' => 'orders')); ?>
		<?php echo $form->textField($model, 'orders', array('class' => '', 'id' => 'orders')); ?>
		<?php echo $form->error($model, 'orders', array('id' => 'orders_err', 'inputID' => 'orders')); ?>
		<div class="hint" id="hint-orders"><?php echo \Yii::t('admin', 'OBJECT_HELP_ORDERS'); ?></div>
	</div>
	
	<?php if($model->filtredField()) : ?>
		<div class="control-group">
			<?php echo $form->labelEx($model, 'filter', array('for' => 'filter')); ?>
			<?php echo $form->dropDownList($model, 'filter', $model->filter_arr1, array('class' => '', 'id' => 'filter')); ?>
			<?php echo $form->error($model, 'filter', array('id' => 'filter_err', 'inputID' => 'filter')); ?>
			<div class="hint" id="hint-filter"><?php echo \Yii::t('admin', 'OBJECT_HELP_FILTER'); ?></div>
		</div>
	<?php endif; ?>

	<div class="control-group noline">
    <?php echo $form->labelEx($model, 'required', array('for' => 'required')); ?>
    <?php echo $form->checkBox($model, 'required', array('class' => '', 'id' => 'required')); ?>
		<?php echo $form->error($model, 'required', array('id' => 'required_err', 'inputID' => 'required')); ?>
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
	$jquery = "if($('#idobj_fields').val() !== null) $('#idobj_fields').trigger('change');";
	Yii::app()->getClientScript()->registerScript('change-idobj_fields', $jquery);


	if($model->isNewRecord) {
		$url = Yii::app()->createUrl('admin/object/ties/getFieldsFieldData');
		if(Yii::app()->request->enableCsrfValidation) {
			$csrfTokenName = Yii::app()->request->csrfTokenName;
			$csrfToken = Yii::app()->request->csrfToken;
			$csrf = "'$csrfTokenName':'$csrfToken',";
		}
		else $csrf = '';

		$jquery = <<<JQUERY
	$("#idobj_type").change(function () {
		$.ajax({
			type: "POST",
			url: "$url",
			cache: false,
			data: {
				field: $(this).val(),
				$csrf
			},
			success: function(html){
				$("#idobj_fields").html(html);
			}
		});
	});
JQUERY;
		Yii::app()->getClientScript()->registerScript('getFieldData-idobj_type', $jquery);
	}
	$jquery = "if($('#idobj_type').val() !== null) $('#idobj_type').trigger('change');";
	Yii::app()->getClientScript()->registerScript('change-idobj_type', $jquery);
?>
</div>