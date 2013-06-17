<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_fields_values', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idobj_fields_values', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_fields', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'idobj_fields', $model->idobj_fields_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'parent', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'parent', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'value', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'value', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'orders', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'orders', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
    <?php echo $form->label($model, 'translate', array('class' => 'control-label')); ?>
    <?php echo $form->checkBox($model, 'translate', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo $form->label($model, 'disabled', array('class' => 'control-label')); ?>
		<?php echo $form->checkBox($model, 'disabled', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->