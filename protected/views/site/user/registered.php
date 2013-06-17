<?php

$this->breadcrumbs += array(Yii::t('nav', 'FORM_REGISTERED'));
?>

<div class="form">

	<p><?php echo Yii::t('nav', 'FORM_REGISTERED_INTRO'); ?></p>

	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => $action,
		'id' => 'activation-form',
		'enableAjaxValidation' => false,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
			<p class="note"><?php echo \Yii::app()->message->get(); ?></p>
		</div>

		<div class="control-group noline">
			<?php echo $form->hiddenField($model, 'registered', array('class' => '', 'value' => 1)); ?>
			<?php echo $form->error($model, 'code', array('class' => 'errorMessage')); ?>
		</div>

		<?php
			$model->getManager()->setForm($form);
			foreach($model->getManager()->getOrders() as $name => $field) :
				echo $model->render($name, 'default', 'form');
			endforeach;
		?>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_REGISTERED_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>

</div>