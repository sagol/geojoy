<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_category', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idobj_category', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_type', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'idobj_type', $model->idobj_type_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'tree', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'tree', $model->tree_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'name', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'name', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'alias', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'alias', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'moderate', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'moderate', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'disabled', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'disabled', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->