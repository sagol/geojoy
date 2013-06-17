<div class="control-group">
<?php
	$langOrder = \Yii::app()->params['langOrder'];
	$language = \Yii::app()->getLanguage();
	unset($langOrder[$language]);
?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#tab_<?php echo $model->getName() . '_' . $language; ?>" data-toggle="tab"><?php echo $language; ?></a></li>
	<?php foreach($langOrder as $lang) : ?>
		<li><a href="#tab_<?php echo $model->getName() . '_' . $lang; ?>" data-toggle="tab"><?php echo $lang; ?></a></li>
	<?php endforeach ?>
</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_<?php echo $model->getName() . '_' . $language; ?>">
			<?php echo $form->labelEx($model, $model->getName() . '_' . $language, array('class' => 'control-label')); ?>
			<?php // echo $form->textArea($model, $model->getName() . '_' . $language, array('class' => '', 'id' => $model->getName() . '_' . $language, 'name' => $model->getNameInForm() . '[' . $language . ']')); ?>
			<?php /*$this->widget('ext.redactor.ImperaviRedactorWidget', array(
				'model' => $model,
				'htmlOptions' => array('id' => $model->getName() . '_' . $language, 'name' => $model->getNameInForm() . '[' . $language . ']'),
				'attribute' => $model->getName() . '_' . $language,
			));*/ ?>
			<?php $this->widget('ext.extckeditor.ExtCKEditor', array(
				'model' => $model,
				'htmlOptions' => array('id' => $model->getName() . '_' . $language, 'name' => $model->getNameInForm() . '[' . $language . ']'),
				'attribute' => $model->getName() . '_' . $language,
			)); ?>
			<?php /*$this->widget('ext.tinymce.ETinyMce', array(
				'model' => $model,
				'htmlOptions' => array('id' => $model->getName() . '_' . $language, 'name' => $model->getNameInForm() . '[' . $language . ']'),
				'attribute' => $model->getName() . '_' . $language,
				'editorTemplate' => 'full',
			));*/ ?>
		</div>
		<?php foreach($langOrder as $lang) : ?>
			<div class="tab-pane" id="tab_<?php echo $model->getName() . '_' . $lang; ?>">
				<?php echo $form->labelEx($model, $model->getName() . '_' . $lang, array('class' => 'control-label')); ?>
				<?php // echo $form->textArea($model, $model->getName() . '_' . $lang, array('class' => '', 'id' => $model->getName() . '_' . $lang, 'name' => $model->getNameInForm() . '[' . $lang . ']')); ?>
				<?php /*$this->widget('ext.redactor.ImperaviRedactorWidget', array(
					'model' => $model,
					'htmlOptions' => array('id' => $model->getName() . '_' . $lang, 'name' => $model->getNameInForm() . '[' . $lang . ']'),
					'attribute' => $model->getName() . '_' . $lang,
				));*/ ?>
				<?php $this->widget('ext.extckeditor.ExtCKEditor', array(
					'model' => $model,
					'htmlOptions' => array('id' => $model->getName() . '_' . $lang, 'name' => $model->getNameInForm() . '[' . $lang . ']'),
					'attribute' => $model->getName() . '_' . $lang,
				)); ?>
				<?php /*$this->widget('ext.tinymce.ETinyMce', array(
					'model' => $model,
					'htmlOptions' => array('id' => $model->getName() . '_' . $lang, 'name' => $model->getNameInForm() . '[' . $lang . ']'),
					'attribute' => $model->getName() . '_' . $lang,
					'editorTemplate' => 'full',
				));*/ ?>
			</div>
		<?php endforeach ?>
	</div>
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
</div>