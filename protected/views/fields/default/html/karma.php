<?php
echo '<div class="karma-value">' . $value .'</div>';
if($model->show()) :
	echo '<span id="karma-' . $model->manager->getId() . '" class="karma-edit">';
	echo \CHtml::link(
		'',
		array('/karmaField/karma/index', 'id' => $model->manager->getId(), 'action' => 'up'),
		array('class' => 'plus karma', 'title' => \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'USER_KARMA_UP'), 'data-id' => $model->manager->getId())
	);
	echo \CHtml::link(
		'',
		array('/karmaField/karma/index', 'id' => $model->manager->getId(), 'action' => 'down'),
		array('class' => 'minus karma', 'title' => \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'USER_KARMA_DOWN'), 'data-id' => $model->manager->getId())
	);
	echo '</span>';

	$jquery = "$('.plus.karma').click(function(){
		var comment = $('#comment-' + $(this).attr('data-id'));
		comment.find('#action').val('up');
		comment.modal({
			backdrop: false
		})
		comment.modal('show');
		return false;
	});";
	\Yii::app()->getClientScript()->registerScript('click-up', $jquery);
	$jquery = "$('.minus.karma').click(function(){
		var comment = $('#comment-' + $(this).attr('data-id'));
		comment.find('#action').val('down');
		comment.modal({
			backdrop: false
		})
		comment.modal('show');
		return false;
	});";
	\Yii::app()->getClientScript()->registerScript('click-down', $jquery);

	$url = \Yii::app()->createUrl('/karmaField/karma/index');

	if(\Yii::app()->request->enableCsrfValidation) {
		$csrfTokenName = \Yii::app()->request->csrfTokenName;
		$csrfToken = \Yii::app()->request->csrfToken;
		$csrf = "data[\"$csrfTokenName\"] = '$csrfToken';";
	}
	else $csrf = '';

	$formErrorEmptyComment = \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_ERROR_EMPTY_COMMENT');
	$jquery = <<<JQUERY
	$('.karma-comment .btn-primary').click(function(){
		var modal = $(this).parent().parent().parent();

		data = new Object();
		data["KarmaForm[comment]"] = modal.find('textarea').val();
		data["KarmaForm[action]"] = modal.find('#action').val();
		data["id"] = modal.attr('data-id');
		$csrf

		$.ajax({
			type: "POST",
			url: "$url",
			cache: false,
			beforeSend: function(xhr) {
				if(!modal.find('textarea').val()) {
					modal.find('.error').html('$formErrorEmptyComment');
					return false;
				}

				return true;
			},
			data: data,
			success: function(html){
				var karma = $('#karma-' + modal.attr('data-id'));
				karma.find('.minus.karma').remove();
				karma.find('.plus.karma').remove();
				if(html == 'ok') modal.modal('hide');
				else modal.find('.error').html(html);
			},
		});

		return false;
	});

JQUERY;
	\Yii::app()->getClientScript()->registerScript('comment-send', $jquery);
?>
	<div class="karma-comment hide" id="comment-<?php echo $model->manager->getId(); ?>" data-id="<?php echo $model->manager->getId(); ?>">
		<div class="karma-body">
			<div class="karma-header">
				<a class="close" data-dismiss="modal">×</a>
				<h3><?php echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_HEADER'); ?></h3>
			</div>
			<div class="karma-text">
				<?php echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_INTRO'); ?>
				<input id="action" type="hidden" value="">
				<textarea></textarea>
				<div class="error"></div>
			</div>
			<div class="karma-footer">
				<a href="#" class="btn btn-primary"><?php echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_SEND'); ?></a>
				<a href="#" class="btn" data-dismiss="modal"><?php echo \Yii::t('app\modules\karmaField\KarmaFieldModule.fields', 'FORM_KARMA_CANCEL'); ?></a>
			</div>
		</div>
	</div>
<?php

endif;

?>