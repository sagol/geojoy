<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idusers', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idusers', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'profile', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'profile', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'status', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'status', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'date', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'date', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'email', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'email', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->