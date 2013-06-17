<div class="control-group">
<?php
	$langOrder = \Yii::app()->params['langOrder'];
	$language = \Yii::app()->getLanguage();
	unset($langOrder[$language]);
?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#<?php echo $model->getName() . '_' . $language; ?>" data-toggle="tab"><?php echo $language; ?></a></li>
	<?php foreach($langOrder as $lang) : ?>
		<li><a href="#<?php echo $model->getName() . '_' . $lang; ?>" data-toggle="tab"><?php echo $lang; ?></a></li>
	<?php endforeach ?>
</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="<?php echo $model->getName() . '_' . $language; ?>">
			<?php echo $form->labelEx($model, $model->getName() . '_' . $language, array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, $model->getName() . '_' . $language, array('class' => '', 'id' => $model->getName() . '_' . $language, 'name' => $model->getNameInForm() . '[' . $language . ']')); ?>
		</div>
		<?php foreach($langOrder as $lang) : ?>
			<div class="tab-pane" id="<?php echo $model->getName() . '_' . $lang; ?>">
				<?php echo $form->labelEx($model, $model->getName() . '_' . $lang, array('class' => 'control-label')); ?>
				<?php echo $form->textField($model, $model->getName() . '_' . $lang, array('class' => '', 'id' => $model->getName() . '_' . $lang, 'name' => $model->getNameInForm() . '[' . $lang . ']')); ?>
			</div>
		<?php endforeach ?>
	</div>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>