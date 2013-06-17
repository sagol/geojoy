		<div class="blockquote">
			<h4><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'USER_WRITE', array('{user}' => ($message['writer'] == $writer ? 'You' : $message['name']))); ?></h4>
			<div class="mark <?php echo $message['mark'] ? '' : 'notmark';?>" data="<?php echo $message['idmessages']; ?>"></div>
			<div class="note"><?php echo Yii::app()->getDateFormatter()->formatDateTime($message['date'], 'medium', 'short'); ?></div>
			<div class="mail_text">
				<?php echo $message['text']; ?>
			</div>
		</div>