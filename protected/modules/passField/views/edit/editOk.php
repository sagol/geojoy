<?php $this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET')); ?>
<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>
<div class="form">

	<div class="control-group noline">
		<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
	</div>

	<p><?php echo Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_EDIT_PASS_OK_INTRO'); ?></p>

</div>