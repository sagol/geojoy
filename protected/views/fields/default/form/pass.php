<?php if($model->getCkeckOldPass()) : ?>
	<div class="control-group">
		<?php echo $form->labelEx($model, $model->getNameOld(), array('class' => 'control-label')); ?>
		<?php echo $form->passwordField($model, $model->getNameOld(), array('class' => '', 'id' => $model->getNameOld(), 'name' => $model->getNameInFormOld())); ?>
		<?php echo $form->error($model, $model->getNameOld(), array('inputID' => $model->getNameOld())); ?>
	</div>
<?php endif ?>

<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->passwordField($model, $model->getName() . '_html', array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>

<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName2(), array('class' => 'control-label')); ?>
	<?php echo $form->passwordField($model, $model->getName2(), array('class' => '', 'id' => $model->getName2(), 'name' => $model->getNameInForm2())); ?>
	<?php echo $form->error($model, $model->getName2(), array('inputID' => $model->getName2())); ?>
	
</div>