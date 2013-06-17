<?php if($value !== null) : ?>
<div class="control-group">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<?php if($model->getUrl()) : ?>
		<a class="<?php echo $model->getServise(); ?>"><span><?php echo $value; ?></span></a>
	<?php else : ?>
		<div class="<?php echo $model->getServise(); ?>"><span><?php echo $value; ?></span></div>
	<?php endif ?>
</div>
<?php endif; ?>