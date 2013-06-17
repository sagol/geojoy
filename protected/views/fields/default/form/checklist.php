<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->checkBoxList($model, $model->getName(), $model->data(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm(), 'separator' => "", 'template' => '<div class="checkbox-wrapper">{input}</div> {label}')); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>