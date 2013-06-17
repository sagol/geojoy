<div class="control-group noline">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->checkBox($model, $model->getName(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>