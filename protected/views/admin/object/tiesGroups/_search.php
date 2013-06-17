<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idobj_ties_groups', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idobj_ties_groups', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'name', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'name', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->