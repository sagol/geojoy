<div class="form create">

<?php
	if($model->isNewRecord) $action = array('admin/news/create');
	else $action = array('admin/news/update', 'id' => $model->getPrimaryKey());
	$form = $this->beginWidget('CActiveForm', array(
		'id' => 'news-form',
		'action' => $action,
		'enableAjaxValidation' => true,
	));
?>

	<?php echo \Yii::t('admin', 'FIELDS_REQUIRED'); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php
		$fieldsManager = &$model->getManager();
		$fieldsManager->setForm($form);
	?>
	<?php $fieldsManager->render('title', 'multilang', 'form'); ?>
	<?php $fieldsManager->render('brief', 'multilang', 'form'); ?>
	<?php $fieldsManager->render('news', 'multilang', 'form'); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'status', array('for' => 'status')); ?>
		<?php echo $form->dropDownList($model, 'status', array(\Yii::t('admin', 'NEWS_DRAFT'), \Yii::t('admin', 'NEWS_PUBLISHED')), array('class' => '', 'id' => 'status')); ?>
		<?php echo $form->error($model, 'status', array('id' => 'status_err', 'inputID' => 'status')); ?>
		<div class="hint" id="hint-status"><?php echo \Yii::t('admin', 'NEWS_HELP_STATUS'); ?></div>
	</div>

	<?php if($model->isNewRecord) : ?>
	<div class="control-group">
		<?php echo $form->labelEx($model, 'type', array('for' => 'type')); ?>
		<?php echo $form->dropDownList($model, 'type', array(\Yii::t('admin', 'NEWS_SITE'), \Yii::t('admin', 'NEWS_USERS_INFO'), \Yii::t('admin', 'NEWS_EMAIL_INFO')), array('class' => '', 'id' => 'type')); ?>
		<?php echo $form->error($model, 'type', array('id' => 'type_err', 'inputID' => 'type')); ?>
		<div class="hint" id="hint-type"><?php echo \Yii::t('admin', 'NEWS_HELP_TYPE'); ?></div>
	</div>
	<?php endif ?>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'create', array('for' => 'create')); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'cssFile' => false,
			'language' => \Yii::app()->getLanguage(),
			'name' => 'create',
			'model' => $model,
			'attribute' => 'create',
		)); ?>
		<?php echo $form->error($model, 'create', array('id' => 'create_err', 'inputID' => 'create')); ?>
		<div class="hint" id="hint-create"><?php echo \Yii::t('admin', 'NEWS_HELP_CREATE'); ?></div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model, 'publish', array('for' => 'publish')); ?>
		<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'cssFile' => false,
			'language' => \Yii::app()->getLanguage(),
			'name' => 'publish',
			'model' => $model,
			'attribute' => 'publish',
		)); ?>
		<?php echo $form->error($model, 'publish', array('id' => 'publish_err', 'inputID' => 'publish')); ?>
		<div class="hint" id="hint-publish"><?php echo \Yii::t('admin', 'NEWS_HELP_PUBLISH'); ?></div>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton($model->isNewRecord ? \Yii::t('admin', 'CREATE') : \Yii::t('admin', 'SAVE'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>
</div>