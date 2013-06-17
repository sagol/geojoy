<?php

$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
?>

<div class="control-group">
	<h2><?php echo Yii::t('nav', 'NAV_PRIVATE_CABINET'); ?></h2>
</div>
<div class="control-group">
	<?php echo CHtml::link(
		Yii::t('nav', 'USER_PROFILE'),
		array('/site/user/profile'),
		array('class' => '')
	); ?>
</div>
<div class="control-group">
	<?php echo CHtml::link(
		Yii::t('nav', 'USER_PROFILE_EDIT'),
		array('/site/user/edit'),
		array('class' => '')
	); ?>
</div>
<div class="control-group">
	<?php echo CHtml::link(
		Yii::t('nav', 'USER_PROFILE_PASSWORD'),
		array('/site/user/password'),
		array('class' => '')
	); ?>
</div>