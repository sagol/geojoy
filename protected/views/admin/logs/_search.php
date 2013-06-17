<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idlogs', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idlogs', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'type', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'type', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'action', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'action', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'title', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'title', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'message', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'message', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'date', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'date', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->