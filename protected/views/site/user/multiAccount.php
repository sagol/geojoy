<?php /*табы*/ $this->renderPartial('app.views.layouts.userTabs'); ?>

<div class="form">

	<p><strong><?php echo Yii::t('nav', 'FORM_MULTI_ACCOUNT_INTRO'); ?></strong></p>

	<p><strong><?php echo Yii::t('nav', 'FORM_MULTI_ACCOUNT_YOU_ACCOUNTS'); ?></strong></p>
	

	<?php foreach($accounts as $account) : ?>
		<div class="control-group">
			<label class="control-label">
				<?php echo \Yii::t('nav', 'NAME'); ?>
			</label>
			<?php
				echo '<span>' .$account['name'];
				if(\Yii::app()->user->multiUser != $account['idusers']) echo CHtml::link(
					Yii::t('nav', 'NAV_MULTI_ACCOUNT_WITHDRAW'),
					array('/site/user/multiAccountWithdraw', 'id' => $account['idusers']),
					array('class' => '')
				);
				else echo ' (' . Yii::t('nav', 'FORM_MULTI_ACCOUNT_MAIN_ACCOUNT') . ')</span>';
			?>
			
		</div>
	<?php endforeach; ?>


	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'id' => 'multi-account-form',
		'action' => array('/site/user/multiAccount'),
		'enableAjaxValidation' => false,
	)); ?>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'createCode', array('class' => 'control-label')); ?>
			<div id="code">
				<?php
					if($code) echo $code;
					else echo CHtml::ajaxLink(
						Yii::t('nav', 'FORM_MULTI_ACCOUNT_CREATE'),
						array('/site/user/multiAccountCode'),
						array('success' => 'function(html){$("#code").html(html);}'),
						array('class' => '')
					);
				?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model, 'code', array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, 'code', array('class' => '')); ?>
			<?php echo $form->error($model, 'code', array('class' => 'errorMessage')); ?>
		</div>

		<div class="control-group noline">
			<?php echo CHtml::submitButton(Yii::t('nav', 'FORM_MULTI_ACCOUNT_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
		</div>
	<?php $this->endWidget(); ?>
</div>