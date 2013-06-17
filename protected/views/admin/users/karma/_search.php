<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="control-group">
		<?php echo $form->label($model, 'username', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'username', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'votedname', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'votedname', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'comment', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'comment', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'points', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'points', array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->