<div class="form create_objects">

<?php 
	if($model->isNewRecord) $action = array('site/objects/add', 'params' => $this->categoryTree, 'adv' => $adv);
	else $action = array('site/objects/edit', 'id' => $model->idobjects);

	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'app-models-object--objects-form',
		'action' => $action,
		'enableAjaxValidation' => false,
	));
?>
	<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED')?></p>

	<?php echo $form->errorSummary($model->getManager()->fields()); ?>

	<?php if(Yii::app()->user->checkAccess('moder') && !$model->isNewRecord) : ?>
		<div class="control-group">
			<?php echo $form->labelEx($model, 'idobj_category', array('for' => 'idobj_category', 'class' => 'control-label')); ?>
			<?php echo $form->dropDownList($model, 'idobj_category', $model->getCategoryList(), array('class' => '', 'id' => 'idobj_category')); ?>
			<?php 
				$errorIdobj_category = $form->error($model, 'idobj_category', array('id' => 'idobj_category_err', 'inputID' => 'idobj_category'));
				echo $errorIdobj_category;
			?>
		</div>
		<?php if($errorIdobj_category) : ?>
			<div class="control-group">
				<?php echo $form->labelEx($model, 'moveCategory', array('for' => 'moveCategory', 'class' => 'control-label')); ?>
				<?php echo $form->checkBox($model, 'moveCategory'); ?>
				<?php echo $form->error($model, 'moveCategory', array('class' => 'errorMessage')); ?>
			</div>
		<?php endif ?>
	<?php endif ?>

	<?php $i = 0; ?>
	<?php $model->getManager()->setForm($form);
		foreach($model->getManager()->getGroups() as $groupName => $group) : ?>
		<?php $i++; ?>
		<div class="spoiler_title"><?php echo Yii::t('fields', $groupName); ?><div class="undraw<?php if($i < 3) echo ' open'; ?>"></div></div>
		<div class="spoiler_fields<?php if($i < 3) echo ' open'; ?>">
			<?php foreach($group as $name => &$field) : ?>
				<?php echo $model->render($name, 'multilang', 'form'); ?>
			<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
  
  <div class="clear"></div>
	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t('nav', 'FORM_OBJECT_CREATE') : Yii::t('nav', 'FORM_OBJECT_SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>