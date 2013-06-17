<?php
	$url = Yii::app()->createUrl('/messages/contacts/del');

	$confirm = \Yii::t('app\modules\messages\MessagesModule.messages', 'YOU_SURE_TO_DELETE');
	$alert = \Yii::t('app\modules\messages\MessagesModule.messages', 'NO_SELECTION');

	$jquery = <<<JQUERY
	function(){
		if($('#data-form .checkbox:checked').length) {
			if(confirm('$confirm')) {
				$.ajax({
					type: 'POST',
					url: '$url',
					cache: false,
					dataType: 'json',
					data: $("#data-form").serialize(),
					success: function(data){
						if(data.status == 'ok') {
							for(var key in data.del)
								$('#data-del-' + data.del[key]).remove();
						}
					},
				});
			}
		}
		else alert('$alert');
		return false;
	}
JQUERY;
	
	$cs = \Yii::app()->getClientScript();
	$cs->registerScript('button-del', "$('body').on('click', '#button-del', $jquery);");
?>
<?php
	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
	/*табы*/ $this->renderPartial('app.views.layouts.userTabs');
?>
<div class="mails">
<?php /*табы*/ $this->renderPartial('app.views.layouts.userMessagesTabs', array('filter' => $filter)); ?>
<a id="button-del" href="" class="delete_mail btn"><?php echo \Yii::t('app\modules\messages\MessagesModule.messages', 'TAB_BUTTON_DELETE'); ?></a>
<div class="mails_list">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'data-form',
		'enableAjaxValidation' => false,
	)); ?>
	<ul>
		<?php foreach($contacts as $contact) : ?>
			<li id="data-del-<?php echo $contact['id']; ?>">
				<input class="checkbox" type="checkbox" name="contacts[]" value="<?php echo $contact['id']; ?>">
				<div class="contact_title">
					<?php echo CHtml::link(
							$contact['name'],
							array(),
							array('class' => '', 'onclick' => '$("#data-del-' . $contact['id'] . ' .mail_form").toggleClass("hide"); return false;')
					); ?>
				</div>
				<div class="mail_form hide">
					<?php $this->widget('\app\modules\messages\components\widgets\Message', array(
						'action' => array('/messages/messages/writerContacts', 'user' => $contact['idusers']),
						'view' => 'contacts',
					)); ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
		
	<?php $this->endWidget(); ?>
</div>


</div>