<?php
	$label = Yii::t('nav', 'FORM_SELECT_CATEGORY');

	$this->breadcrumbs += array($label);
?>

<h1><?php echo $label; ?></h1>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'id' => 'app-models-object--objects-form',
	'enableAjaxValidation' => false,
)); ?>

	<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED')?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'category', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'category', $model->category(), array('class' => '', 'id' => 'category', 'options' => $model->categoryDisabled())); ?>
		<?php echo $form->error($model, 'category'); ?>
		<div id="description"></div>
	</div>

	<div class="control-group">
		<ul>
		<?php foreach($category as $id => $value) : ?>
			<?php if(!empty($value['description'])) : ?><li><?php echo Yii::t('lists', $value['description']); ?></li><?php endif ?>
		<?php endforeach ?>
		</ul>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_SELECT_CATEGORY_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>