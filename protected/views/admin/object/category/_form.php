<div class="form create">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'app-models-object--category-form',
	'enableAjaxValidation' => true,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'idobj_type', array('for' => 'idobj_type')); ?>
		<?php echo $form->dropDownList($model, 'idobj_type', $model->idobj_type_arr1, array('class' => '', 'id' => 'idobj_type')); ?>
		<?php echo $form->error($model, 'idobj_type', array('id' => 'idobj_type_err', 'inputID' => 'idobj_type')); ?>
		<div class="hint" id="hint-idobj_type"><?php echo \Yii::t('admin', 'CATEGORY_HELP_TYPE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'tree', array('for' => 'tree')); ?>
		<?php echo $form->dropDownList($model, 'tree', $model->order_arr, array('class' => '', 'id' => 'tree')); ?>
		<?php echo $form->error($model, 'tree', array('id' => 'tree_err', 'inputID' => 'tree')); ?>
		<div class="hint" id="hint-tree"><?php echo \Yii::t('admin', 'CATEGORY_HELP_TREE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'name', array('for' => 'name')); ?>
		<?php echo $form->textField($model, 'name', array('class' => '', 'id' => 'name')); ?>
		<?php echo $form->error($model, 'name', array('id' => 'name_err', 'inputID' => 'name')); ?>
		<div class="hint" id="hint-name"><?php echo \Yii::t('admin', 'CATEGORY_HELP_NAME'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'alias', array('for' => 'alias')); ?>
		<?php echo $form->textField($model, 'alias', array('class' => '', 'id' => 'alias')); ?>
		<?php echo $form->error($model, 'alias', array('id' => 'alias_err', 'inputID' => 'alias')); ?>
		<div class="hint" id="hint-alias"><?php echo \Yii::t('admin', 'CATEGORY_HELP_ALIAS'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'description', array('for' => 'description')); ?>
		<?php echo $form->textArea($model, 'description', array('class' => '', 'id' => 'description')); ?>
		<?php echo $form->error($model, 'description', array('id' => 'description_err', 'inputID' => 'description')); ?>
		<div class="hint" id="hint-description"><?php echo \Yii::t('admin', 'CATEGORY_HELP_DESCRIPTION'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'img', array('for' => 'img')); ?>
		<?php if($model->img) echo '<div class=""><img src="' . \Yii::app()->request->getBaseUrl() . \Yii::app()->params['uploadUrl'] . $model->img . '"/></div>'; ?>
		<?php if(!$model->isNewRecord && $model->img) : ?>
			<?php echo$form->labelEx($model, 'delete', array('for' => 'delete')); ?>
			<?php echo'<div class="">'.  $form->checkBox($model, 'delete', array('class' => '', 'id' => 'delete')). '</div>'; ?>
			<div class="hint" id="hint-delete"><?php echo \Yii::t('admin', 'CATEGORY_HELP_DELETE'); ?></div>
		<?php endif; ?>
		
    <?php echo $form->labelEx($model, 'download', array('for' => '')); ?>
    <?php echo $form->fileField($model, 'img', array('class' => '', 'id' => 'img')); ?>
		<?php echo $form->error($model, 'img', array('id' => 'img_err', 'inputID' => 'img')); ?>
		
		<div class="hint" id="hint-img"><?php echo \Yii::t('admin', 'CATEGORY_HELP_IMG'); ?></div>
	</div>

	<div class="control-group noline">
		<?php echo $form->labelEx($model, 'moderate', array('for' => 'moderate')); ?>
		<?php echo $form->checkBox($model, 'moderate', array('class' => '', 'id' => 'moderate')); ?>
		<?php echo $form->error($model, 'moderate', array('id' => 'moderate_err', 'inputID' => 'moderate')); ?>
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

</div>