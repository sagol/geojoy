<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php echo $form->dropDownList($model, $model->getName(), $model->data(), array('class' => '', 'id' => $model->getName(), 'name' => $model->getNameInForm(), 'prompt' => \Yii::t('fields', 'NOT_SELECT'))); ?>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>