<?php
	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
?>

<div class="mails">
	<h3><?php 	echo Yii::t('app\modules\messages\MessagesModule.messages', 'NAV_MESSAGES_THREAD', array('{user}' => app\models\users\User::name($user))); ?></h3>
	<?php foreach($thread as $message) : ?>
		<div class="<?php echo ($message['notread'] ? 'blockquote' : 'blockquote'); ?>">
			<h4><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'USER_WRITE', array('{user}' => ($message['writer'] == $writer ? 'You' : $message['name']))); ?></h4>
			<div class="mark <?php echo $message['mark'] ? '' : 'notmark';?>" data="<?php echo $message['id']; ?>"></div>
			<div class="note"><?php echo Yii::app()->getDateFormatter()->formatDateTime($message['date'], 'medium', 'short'); ?></div>
			<div class="mail_text">
				<?php echo $message['text']; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>