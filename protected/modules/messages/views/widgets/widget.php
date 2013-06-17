<div class="message">
<div class="spoiler_title"><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'WRITE_MESSAGE_AUTHOR');?><div class="undraw"></div></div>

	<div class="message_body">
	<?php
		$params = $action;
		unset($params[0]);
		$url = Yii::app()->createUrl($action[0], $params);

		$form = $this->beginWidget('CActiveForm', array(
			'id' => 'messages-form',
			'action' => $action,
			'enableClientValidation' => true,
			'clientOptions' => array(
				'validateOnSubmit' => true,
				'afterValidate' => "js:function(form, data, hasError) {
					if(!hasError){  
						jQuery('#messages-form input:submit').attr('disabled', 'disabled').addClass('disabled');
						$.ajax({
							'type': 'POST',
							'url': '$url',
							'cache': false,
							'dataType': 'json',
							'data': $('#messages-form').serialize(),
							'success': function(data){
								if(data.status == 'ok') jQuery('#messages_text').val('');
								jQuery('#messages-form input:submit').removeAttr('disabled').removeClass('disabled');
								jQuery('#messages-form').find('.info').html(data.info);
								if(data.message) jQuery('#mails-list').prepend(data.message)
							},
						}); 
						return false;    
					}
				}",
			),
		));
	?>
		<div class="info"></div>
		<?php echo $form->labelEx($model, 'text', array('for' => 'text')); ?>
		<?php echo $form->textArea($model, 'text', array('class' => '', 'id' => 'messages_text', 'name' => 'messages[text]')); ?>
		<?php echo $form->error($model, 'text', array('id' => 'messages_text_error', 'inputID' => 'messages_text')); ?>
		<?php echo CHtml::submitButton(Yii::t('app\modules\messages\MessagesModule.messages', 'WIDGET_SUBMIT_BUTTON'), array('class' => 'btn btn-primary')); ?>
	<?php $this->endWidget(); ?>
	</div>
</div>