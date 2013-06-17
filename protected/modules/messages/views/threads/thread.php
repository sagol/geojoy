<?php
	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
	/*табы*/ $this->renderPartial('app.views.layouts.userTabs');

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
	<?php /*табы*/ $this->renderPartial('app.views.layouts.userMessagesTabs', array('filter' => $filter)); ?>
	<?php 
		if($id) {
			$this->widget('\app\modules\messages\components\widgets\Message', array(
				'action' => array('/messages/messages/writer', 'id' => $id, 'user' => $replay),
				'view' => 'contacts',
			));
		}
		else {
			$this->widget('\app\modules\messages\components\widgets\Message', array(
				'action' => array('/messages/messages/writerContacts', 'user' => $replay),
				'view' => 'contacts',
			));
		}
	?>

	<?php echo Yii::t('app\modules\messages\MessagesModule.messages', 'NAV_MESSAGES_THREAD', array('{user}' => app\models\users\User::name($model->user))); ?>
	<div id="mails-list">
	<?php foreach($thread as $message) : ?>
		<div class="blockquote">
			<h4><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'USER_WRITE', array('{user}' => ($message['writer'] == $writer ? 'You' : $message['name']))); ?></h4>
			<div class="mark <?php echo $message['mark'] ? '' : 'notmark';?>" data="<?php echo $message['id']; ?>"></div>
			<div class="note"><?php echo Yii::app()->getDateFormatter()->formatDateTime($message['date'], 'medium', 'short'); ?></div>
			<div class="mail_text">
				<?php echo $message['text']; ?>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>