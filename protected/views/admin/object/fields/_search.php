<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_fields', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idobj_fields', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'type', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'type', $model->typeField(), array('class' => '', 'prompt' => \Yii::t('lists', 'ALL'))); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'parent', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'parent', $model->parent_arr, array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'name', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'name', array('size' => 20, 'maxlength' => 20, 'class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'title', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'title', array('size' => 50, 'maxlength' => 50, 'class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'units', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'units', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->