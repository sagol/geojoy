<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName()); ?>
	<?php echo $form->checkBoxList($model, $model->getName(), $model->data(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm(), 'separator' => "")); ?>
	<?php echo $form->error($model, $model->getName()); ?>
</div>