<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'idmessages', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idmessages', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'notread', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'notread', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'reservation', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'reservation', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'writer', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'writer', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'replay', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'replay', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'text', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'text', array('class' => '')); ?>
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