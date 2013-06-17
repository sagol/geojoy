<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->textArea($model, $model->getName(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>