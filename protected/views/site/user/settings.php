<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>
<div class="form">
	<p><?php echo Yii::t('nav', 'FORM_SETTINGS_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'activation-form',
		'action' => array('/site/user/settings'),
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'showPage', array('class' => 'control-label')); ?>
			<?php echo $form->dropDownList($model, 'showPage', array(Yii::t('nav', 'DEFAULT'), Yii::t('nav', 'SHOW_PAGE_LINE'), Yii::t('nav', 'SHOW_PAGE_PAGES')), array('class' => '', 'id' => 'showPage')); ?>
			<?php echo $form->error($model, 'showPage', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'showPageCount', array('class' => 'control-label')); ?>
			<?php echo $form->dropDownList($model, 'showPageCount', \app\models\users\User::getSettingsShowPageCountArray(), array('class' => '', 'id' => 'showPageCount')); ?>
			<?php echo $form->error($model, 'showPageCount', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'socialInfoVisible', array('class' => 'control-label')); ?>
			<?php echo $form->radioButtonList($model, 'socialInfoVisible', array(Yii::t('nav', 'NO'), Yii::t('nav', 'YES')), array('class' => '', 'id' => 'socialInfoVisible')); ?>
			<?php echo $form->error($model, 'socialInfoVisible', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'subscription', array('class' => 'control-label')); ?>
			<?php echo $form->checkBoxList($model, 'subscription', array(\app\models\users\User::SUB_PRIVATE_MESSAGE => \Yii::t('nav', 'PRIVATE_MESSAGE'), \app\models\users\User::SUB_SITE_NEWS => \Yii::t('nav', 'SITE_NEWS')), array('class' => '', 'id' => 'subscription')); ?>
			<?php echo $form->error($model, 'subscription', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_SETTINGS_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>

</div>