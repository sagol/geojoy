<?php $this->breadcrumbs += array(Yii::t('nav', 'FORM_REGISTRATION')); ?>

<div class="form">

	<p><?php echo Yii::t('nav', 'FORM_REGISTRATION_INTRO'); ?></p>
	<ul class="nav nav-tabs">
		<li<?php if($profile == 1) echo ' class="active"';?>><a href="#user" data-toggle="tab"><?php echo Yii::t('nav', 'FORM_REGISTRATION_USER')?></a></li>
		<li<?php if($profile == 2) echo ' class="active"';?>><a href="#company" data-toggle="tab"><?php echo Yii::t('nav', 'FORM_REGISTRATION_COMPANY')?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane<?php if($profile == 1) echo ' active';?>" id="user">
			<?php $form = $this->beginWidget('CActiveForm', array(
				'action' => Yii::app()->createUrl($this->route),
				'id' => 'registration-form-user',
				'action' => array('/site/user/registration'),
				'enableAjaxValidation' => true,
			)); ?>
				<div class="control-group noline">
					<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
					<p class="errorSummary"><?php echo $form->errorSummary($profileUser); ?></p>
				</div>

				<div class="control-group noline">
					<input type="hidden" name="profile" value="1">
				</div>

				<?php
					$profileUser->getManager()->setForm($form);
					foreach($profileUser->getManager()->getOrders() as $name => $field) :
						echo $profileUser->render($name, 'default', 'form');
					endforeach;
				?>
				<div class="control-group noline">
					<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_REGISTRATION_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
				</div>
			<?php $this->endWidget(); ?>
		</div>
		<div class="tab-pane<?php if($profile == 2) echo ' active';?>" id="company">
			<?php $form = $this->beginWidget('CActiveForm', array(
				'action' => Yii::app()->createUrl($this->route),
				'id' => 'registration-form-company',
				'action' => array('/site/user/registration'),
				'enableAjaxValidation' => true,
			)); ?>
				<div class="control-group noline">
					<p class="note"><?php echo Yii::t('nav', 'FIELDS_REQUIRED'); ?></p>
					<p class="errorSummary"><?php echo $form->errorSummary($profileCompany); ?></p>
				</div>

				<div class="control-group noline">
					<input type="hidden" name="profile" value="2">
				</div>

				<?php
					$profileCompany->getManager()->setForm($form);
					foreach($profileCompany->getManager()->getOrders() as $name => $field) :
						echo $profileCompany->render($name, 'default', 'form');
					endforeach;
				?>
				<div class="control-group noline">
					<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_REGISTRATION_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>