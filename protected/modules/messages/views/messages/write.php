<?php
	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
	/*табы*/ $this->renderPartial('app.views.layouts.userTabs');

	echo Yii::t('app\modules\messages\MessagesModule.messages', 'NAV_MESSAGES_THREAD', array('{user}' => app\models\users\User::name($model->user)));
	$url = Yii::app()->createUrl('/messages/messages/mark');
	if(Yii::app()->request->enableCsrfValidation) {
		$csrfTokenName = Yii::app()->request->csrfTokenName;
		$csrfToken = Yii::app()->request->csrfToken;
		$csrf = "'$csrfTokenName':'$csrfToken',";
	}
	else $csrf = '';

	$jquery = <<<JQUERY
	$.ajax({
		type: 'POST',
		url: '$url',
		cache: false,
		dataType: 'json',
		data: {
			id: $(this).attr('data'),
			mark: $(this).hasClass('notmark'),
			$csrf
		},
		success: function(data){
			if(data.status == 'ok') {
				$('.mark[data =' + data.id + ']').toggleClass('notmark');
			}
		},
	});
JQUERY;
	
	$cs = \Yii::app()->getClientScript();
	$cs->registerScript('marks', "$('body').on('click', '.mark', function(){{$jquery}});");
?>
<div class="mails">
	<?php foreach($thread as $message) : ?>
		<div class="<?php echo ($message['notread'] ? 'blockquote' : 'blockquote'); ?>">
			<h4><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'USER_WRITE', array('{user}' => ($message['writer'] == $writer ? 'You' : $message['login']))); ?></h4>
			<div class="mark <?php echo $message['mark'] ? '' : 'notmark';?>" data="<?php echo $message['id']; ?>"></div>
			<div class="note"><?php echo Yii::app()->getDateFormatter()->formatDateTime($message['date'], 'medium', 'short'); ?></div>
			<div class="mail_text">
				<?php echo $message['text']; ?>
			</div>
		</div>
	<?php endforeach; ?>
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'app-models-object--objects-form',
// 		'action' => array('/messages/threads/answer', 'id' => $id),
		'enableAjaxValidation' => false,
	)); ?>
		<div class="mail_send">
				<?php echo $form->labelEx($model, 'text', array('for' => 'text')); ?>
				<?php echo $form->textArea($model, 'text', array('class' => '', 'id' => 'text', 'name' => 'messages[text]')); ?>
				<?php echo $form->error($model, 'text'); ?>
			<input class="btn" type="submit" value="<?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'BUTTON_SEND') ?>"/>
		</div>
	<?php $this->endWidget(); ?>
</div>