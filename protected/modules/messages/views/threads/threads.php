<?php

	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
	/*табы*/ $this->renderPartial('app.views.layouts.userTabs');
?>

<div class="mails">
<?php /*табы*/ $this->renderPartial('app.views.layouts.userMessagesTabs', array('filter' => $filter)); ?>
<div class="mails_list">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'data-form',
		'enableAjaxValidation' => false,
	)); ?>
	<ul>
		<?php foreach($threads as $thread) : ?>
		<?php $interlocutor = \app\models\users\User::name($thread['interlocutor']); ?>
		<li id="data-del-<?php echo $thread['id']; ?>" class="<?php echo ($thread['notread'] ? 'new' : ''); ?>">
				<?php echo CHtml::link(
					'',
					array('/messages/threads/print', 'id' => $thread['id']),
					array('class' => 'print', 'title' => 'print', 'target' => '_blank')
				); ?>
				<div class="mail_user"><?php echo $interlocutor; ?></div>

				<input class="checkbox" type="checkbox" name="threads[]" value="<?php echo $thread['id']; ?>">
				<div class="mail_title">
					<?php 
						if($thread['id'])
							echo CHtml::link(
									$thread['text'],
									array('/messages/threads/thread', 'filter' => $filter, 'id' => $thread['id']),
									array('class' => '')
							);
						else
							echo CHtml::link(
									$thread['text'],
									array('/messages/threads/thread', 'filter' => $filter, 'id' => $thread['id'], 'user' => $thread['interlocutor']),
									array('class' => '')
							);
					?>
					(<?php echo $thread['name']; ?>)
				</div>
				<div class="mail_object">
				<?php
					if($thread['id']) {
						echo CHtml::link(
							\Yii::t('app\modules\messages\MessagesModule.messages', 'OBJECT_ID', array('{id}' => $thread['id'])),
							array('/site/objects/view/', 'id' => $thread['id']),
							array('class' => '', 'target' => '_blank')
						);
					}
					else
						echo CHtml::link(
							$interlocutor,
							array('/site/user/profile', 'id' => $thread['interlocutor']),
							array('class' => '', 'target' => '_blank')
						);
				?>
				</div>
				<div class="clear"></div>
			</li>
			
		<?php endforeach; ?>
	</ul>
		
	<?php $this->endWidget(); ?>
</div>


</div>







