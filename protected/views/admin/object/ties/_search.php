<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>
	<div class="control-group">
		<?php echo $form->label($model, 'idobj_ties', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idobj_ties', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_type', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'idobj_type', $model->idobj_type_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_ties_groups', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'idobj_ties_groups', $model->idobj_ties_groups_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_fields', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'idobj_fields', $model->idobj_fields_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'orders', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'orders', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
    <?php echo $form->label($model, 'required', array('class' => 'control-label')); ?>
    <?php echo $form->checkBox($model, 'required', array('class' => '')); ?>
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