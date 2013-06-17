<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName()); ?>
	<?php echo $form->textField($model, $model->getName() . '[from]', array('class' => 'filter-int', 'id' => $model->getName() . '_from', 'name' => $model->getNameInForm() . '[from]')); ?>
	<?php echo $form->error($model, $model->getName() . '[from]'); ?>
	<span class="filter-int">-</span>
	<?php echo $form->textField($model, $model->getName() . '[to]', array('class' => 'filter-int', 'id' => $model->getName() . '_to', 'name' => $model->getNameInForm() . '[to]')); ?>
	<?php echo $form->error($model, $model->getName() . '[from]'); ?>
</div>