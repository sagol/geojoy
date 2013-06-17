<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>

<div class="form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'profile-form-edit',
		'action' => array('/site/user/edit'),
		'enableAjaxValidation' => true,
	)); ?>
  
		<div class="control-group noline">
			<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
			<p class="errorSummary"><?php echo $form->errorSummary($model); ?></p>
		</div>

		<?php
			$model->getManager()->setForm($form);
			foreach($model->getManager()->getOrders() as $name => $field) :
				// $value = $field->getValue();
				// if($value === null || $value === '' || $value === array()) continue;
				echo $model->render($name, 'noline', 'form');
				echo $model->renderAccessField($name, 'default', 'form');
			endforeach;
		?>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_EDIT_PROFILE_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>

	<?php $this->endWidget(); ?>
</div>
