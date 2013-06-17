<?php
$this->breadcrumbs += array(Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASS_OK'));
?>

<div class="form-horizontal control-group">

	<h2><?php echo Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASS_OK'); ?></h2>

	<div class="control-group">
		<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
	</div>

	<p><?php echo Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASS_OK_INTRO'); ?></p>

</div>