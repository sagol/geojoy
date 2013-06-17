<?php
$this->breadcrumbs += array(Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_OK'));
?>

<div class="control-group">

	<h2><?php echo Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_OK'); ?></h2>

	<div class="control-group noline">
		<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
	</div>

	<p><?php echo Yii::t('app\modules\emailField\EmailFieldModule.fields', 'FORM_CONFIRMATION_EMAIL_OK_INTRO'); ?></p>

</div>