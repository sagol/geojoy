<?php

$this->breadcrumbs += array(
	Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASSWORD'),
);
?>

<div class="form">
	<p><?php echo Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASSWORD_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'registration-form',
		'action' => array('reset/reset'),
		'enableAjaxValidation' => true,
	)); ?>

		<input type="hidden" value="<?php echo $code; ?>" name="code"/>
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
		</div>

		<div class="control-group noline">
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<?php
			$model->getManager()->setForm($form);
			foreach($model->getManager()->getOrders() as $name => $field) :
				echo $model->render($name, 'default', 'form');
			endforeach;
		?>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('app\modules\passField\PassFieldModule.fields', 'FORM_RESET_PASSWORD_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>
