<div class="wide form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>
	<div class="control-group">
		<?php echo $form->label($model, 'idnews', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'idnews', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'title', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'title', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'brief', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'brief', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'news', array('class' => 'control-label')); ?>
		<?php echo $form->textField($model, 'news', array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'status', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'status', array(\Yii::t('admin', 'NEWS_DRAFT'), \Yii::t('admin', 'NEWS_PUBLISHED')), array('class' => '')); ?>
	</div>

	<div class="control-group">
		<?php echo $form->label($model, 'type', array('class' => 'control-label')); ?>
		<?php echo $form->dropDownList($model, 'type', array(\Yii::t('admin', 'NEWS_SITE'), \Yii::t('admin', 'NEWS_USERS_INFO'), \Yii::t('admin', 'NEWS_EMAIL_INFO')), array('class' => '')); ?>
	</div>

	<div class="control-group noline">
		<?php echo CHtml::submitButton(\Yii::t('admin', 'FIND'), array('class' => 'btn btn-primary')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->