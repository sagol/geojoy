<?php
	if(Yii::app()->request->enableCsrfValidation) {
		$csrfTokenName = Yii::app()->request->csrfTokenName;
		$csrfToken = Yii::app()->request->csrfToken;
		$csrf = "'$csrfTokenName':'$csrfToken',";
	}
	else $csrf = '';

	$jquery = <<<JQUERY
	$.ajax({
		type: 'POST',
		url: $(this).attr('href'),
		cache: false,
		dataType: 'json',
		data: {
			$csrf
		},
		success: function(data){
			if(data.status == 'ok') {
				$('#pageButton').before(data.html);
				if(data.url) $('#pageButton').attr('href', data.url);
				else $('#pageButton').remove();
			}
		},
	});
	return false;
JQUERY;
	
	$cs = \Yii::app()->getClientScript();
	$cs->registerScript('pageButton', "$('body').on('click', '#pageButton', function(){{$jquery}});");

	echo CHtml::link($next['title'], $next['url'], array('class' => 'btn', 'id' => 'pageButton'));