<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName()); ?>
	<?php echo $form->checkBox($model, $model->getName(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm())); ?>
	<?php echo $form->error($model, $model->getName()); ?>
</div>