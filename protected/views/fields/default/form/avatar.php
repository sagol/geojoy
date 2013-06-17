<?php
	$baseUrl = Yii::app()->request->getBaseUrl();
	$name = $model->getName();
	$jquery = <<<JQUERY
	var camera = $('.$name-camera'),
		screen =  $('.$name-screen');


	/*----------------------------------
		Установки веб камеры
	----------------------------------*/


	webcam.set_swf_url('$baseUrl/js/webcam/webcam.swf');
	webcam.set_api_url('$baseUrl/avatarField/webcam/upload.html?name=$name'); // Скрипт загрузки
	webcam.set_quality(80); // Качество фотографий JPEG
	webcam.set_shutter_sound(true, '$baseUrl/js/webcam/shutter.mp3');

	// Генерируем код HTML для камеры и добавляем его на страницу:	
	screen.html(
		webcam.get_html(screen.width(), screen.height())
	);

	/*------------------------------
		Обработчики событий
	-------------------------------*/

	camera.find('.shootButton').click(function(){
		webcam.freeze();
		camera.find('a').toggleClass('hide');
		return false;
	});
	
	camera.find('.resetButton').click(function(){
		webcam.reset();
		camera.find('a').toggleClass('hide');
		return false;
	});
	
	camera.find('.uploadButton').click(function(){
		webcam.upload();
		webcam.reset();
		camera.find('a').toggleClass('hide');
		return false;
	});

	camera.find('.settings').click(function(){
		webcam.configure('camera');
	});

	/*---------------------- 
		Возвратные вызовы
	----------------------*/
	
	
	webcam.set_hook('onLoad',function(){
		// Когда FLASH загружен, разрешаем доступ 
		// к кнопкам "Снимаю" и "Установка"
		$('.shootButton').removeClass('hide');
	});
	
	webcam.set_hook('onComplete', function(msg){
		// Данный ответ возвращается upload.php
		// и содержит имя изображения в формате объекта JSON

		msg = $.parseJSON(msg);

		if(msg.error){
			alert(msg.message);
		}
		else {
			$('.$name-foto').html('<img src="' + msg.name + '">');
			$('#set-ava-webcam').html('<img src="' + msg.name + '">');
			$('#{$name}Webcam').val(msg.name);
		}
	});
	
	webcam.set_hook('onError',function(e){
		screen.html(e);
	});

	$('.set-ava').click(function(){
		var set = $(this);
		if(!set.hasClass('set')) {
			$('.set-ava').removeClass('set');
			set.addClass('set');
			$('#{$name}Set').val(set.attr('data-set'));
		}
	});
JQUERY;

	$cs = \Yii::app()->getClientScript();
	// скрипт инициализации
	$cs->registerScript($name . '-webcam', $jquery);
	$cs->registerScriptFile($baseUrl . '/js/webcam/webcam.js', \CClientScript::POS_END);

	$maxFiles = 1;
	$fieldName = $model->getName();

	$jquery = <<<JQUERY
	$('#fileupload-$fieldName').fileupload({
		url: '$baseUrl/avatarField/upload/ajax.html?name=$name',
		autoUpload: true,
		maxNumberOfFiles: $maxFiles,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
		maxFileSize: 5000000,
		dropZone: $('#fileupload-$fieldName'),
		uploadTemplateId: 'template-upload-$fieldName',
		downloadTemplateId: 'template-download-$fieldName',
	}).bind('fileuploaddone', function(e, data){
		$('#set-ava-upload').html('<img src="' + data.result[0].url + '">');
		$('#{$name}Upload').val(data.result[0].url);
	});


	// Load existing files:
	$.getJSON('$baseUrl/avatarField/upload/uploaded.html?name=$name', function (result) {
		if (result && result.length) {
			var that = $('#fileupload-$fieldName');
			$(that).fileupload('option', 'done')
			.call(that, null, {result: result});

			$('#set-ava-upload').html('<img src="' + result[0].url + '">');
			if(result.length >= $maxFiles) that.find('.fileinput-button').addClass('disabled').find('input').prop('disabled', true);
		}
	});
JQUERY;

				$fileUploadErrors = '
window.locale = {
    "fileupload": {
        "errors": {
            "maxFileSize": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_MAX_FILE_SIZE') . '",
            "minFileSize": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_MIN_FILE_SIZE') . '",
            "acceptFileTypes": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_ACCEPT_FILE_TYPES') . '",
            "maxNumberOfFiles": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_MAX_NUMBER_OF_FILES') . '",
            "uploadedBytes": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_UPLOADED_BYTES') . '",
            "emptyResult": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_EMPTY_RESULT') . '",
            "notPermissionWrite": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_NOT_PERMISSION_WRITE') . '",
            "uploadErrIniSize": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_UPLOAD_ERR_INI_SIZE') . '",
            "uploadErrFormSize": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_UPLOAD_ERR_FORM_SIZE') . '",
            "uploadErrPartial": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_UPLOAD_ERR_PARTIAL') . '",
            "uploadErrNoFile": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_UPLOAD_ERR_NO_FILE') . '",
        },
        "error": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_ERROR') . '",
        "start": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_START') . '",
        "cancel": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_CANCEL') . '",
        "destroy": "' . \Yii::t('nav', 'FORM_UPLOAD_FILES_DESTROY') . '"
    }
};';

	// CSS to style the file input field as button and adjust the Bootstrap progress bars
	$cs->registerCssFile($baseUrl . '/css/jquery.fileupload-ui.css');
	// скрипт инициализации
	$cs->registerScript('upload-' . $model->getName(), $jquery);
	// скрипт локализации
	$cs->registerScript('fileUploadErrors', $fileUploadErrors, \CClientScript::POS_HEAD);

	// The jQuery UI widget factory, can be omitted if jQuery UI is already included
	$cs->registerScriptFile($baseUrl . '/js/upload/vendor/jquery.ui.widget.js', \CClientScript::POS_END);
	// The Templates and Load Image plugins are included for the FileUpload user interface
	$cs->registerScriptFile($baseUrl . '/js/upload/JavaScript-Templates/tmpl.js', \CClientScript::POS_END);
	// The Load Image plugin is included for the preview images and image resizing functionality
	$cs->registerScriptFile($baseUrl . '/js/upload/JavaScript-Load-Image/load-image.min.js', \CClientScript::POS_END);
	// The Canvas to Blob plugin is included for image resizing functionality
	$cs->registerScriptFile($baseUrl . '/js/upload/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js', \CClientScript::POS_END);
	// The Iframe Transport is required for browsers without support for XHR file uploads
	$cs->registerScriptFile($baseUrl . '/js/upload/jquery.iframe-transport.js', \CClientScript::POS_END);
	// The basic File Upload plugin
	$cs->registerScriptFile($baseUrl . '/js/upload/jquery.fileupload.js', \CClientScript::POS_END);
	// The File Upload image processing plugin
	$cs->registerScriptFile($baseUrl . '/js/upload/jquery.fileupload-ip.js', \CClientScript::POS_END);
	// The File Upload user interface plugin
	$cs->registerScriptFile($baseUrl . '/js/upload/jquery.fileupload-ui.js', \CClientScript::POS_END);
	// The XDomainRequest Transport is included for cross-domain file deletion for IE8+
?>
<!--[if gte IE 8]><script src="<?php echo $baseUrl ?>/js/upload/cors/jquery.xdr-transport.js"></script><![endif]-->
<div class="control-group">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-<?php echo $name; ?>" data-toggle="tab">set</a></li>
		<li><a href="#tab-<?php echo $name . 'Upload'; ?>" data-toggle="tab">upload</a></li>
		<li><a href="#tab-<?php echo $name . 'Webcam'; ?>" data-toggle="tab">webcam</a></li>
		<li><a href="#tab-<?php echo $name . 'Gravatar'; ?>" data-toggle="tab">gravatar</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab-<?php echo $name; ?>">
			<?php echo $form->labelEx($model, $name, array('class' => 'control-label')); ?>
			<?php echo $form->hiddenField($model, $name . 'Set', array('class' => '', 'id' => $name . 'Set', 'name' => $model->getNameInForm() . '[Set]')); ?>
			<div class="set-ava thumbnail<?php echo $model->getDefault('upload'); ?>" id="set-ava-upload" data-set="1">
				<?php if($model->getUpload()) echo '<img src="' . $model->getUpload() . '">'; ?>
			</div>
			<div class="set-ava thumbnail<?php echo $model->getDefault('webcam'); ?>" id="set-ava-webcam"  data-set="2">
				<?php if($model->getWebcam()) echo '<img src="' . $model->getWebcam() . '">'; ?>
			</div>
			<div class="set-ava thumbnail<?php echo $model->getDefault('gravatar'); ?>" data-set="3">
				<?php if($model->getGravatar()) echo '<img src="' . $model->getGravatar() . '">'; ?>
			</div>
		</div>
		<div class="tab-pane" id="tab-<?php echo $name . 'Upload'; ?>">
			<?php echo $form->labelEx($model, $name . 'Upload', array('class' => 'control-label')); ?>
			<?php echo $form->hiddenField($model, $name . 'Upload', array('class' => '', 'id' => $name . 'Upload', 'name' => $model->getNameInForm() . '[Upload]')); ?>
			<div  id="fileupload-<?php echo $name; ?>">
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="fileupload-buttonbar">
					<div class="buttonbar">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<div class="btn btn-success fileinput-button">
							<i class="icon-plus icon-white"></i> <?php echo \Yii::t('nav', 'FORM_UPLOAD_FILES_ADD_FILES'); ?>
							<input type="file" name="<?php echo $model->getNameInForm(); ?>[files]" multiple>
						</div>
					</div>
					<div class="progressbar">
						<!-- The global progress bar -->
						<div class="progress progress-success progress-striped active fade">
						<div class="bar" style="width:0%;"></div>
						</div>
					</div>
				
					<!-- The loading indicator is shown during image processing -->
					<div class="fileupload-loading"></div>
					
					<!-- The table listing the files available for upload/download -->
					<table class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
					<?php echo $form->error($model, $name, array('inputID' => $name . 'Upload')); ?>
				</div>
			</div>
		</div>
		<div class="tab-pane" id="tab-<?php echo $name . 'Webcam'; ?>">
			<?php echo $form->labelEx($model, $name . 'Webcam', array('class' => 'control-label')); ?>
			<?php echo $form->hiddenField($model, $name . 'Webcam', array('class' => '', 'id' => $name . 'Webcam', 'name' => $model->getNameInForm() . '[Webcam]')); ?>
			<div class="<?php echo $name; ?>-camera">
				<div class="<?php echo $name; ?>-screen"></div>
				<div class="<?php echo $name; ?>-buttons">
					<a href="#" class="shootButton hide btn"><?php echo \Yii::t('app\modules\avatarField\AvatarFieldModule.fields', 'SNAPSHOT') ?></a>
					<a href="#" class="resetButton hide btn"><?php echo \Yii::t('app\modules\avatarField\AvatarFieldModule.fields', 'AGAIN') ?></a>
					<a href="#" class="uploadButton hide btn"><?php echo \Yii::t('app\modules\avatarField\AvatarFieldModule.fields', 'IMAGE_DOWNLOAD') ?></a>
				</div>
			</div>
			<div class="<?php echo $name; ?>-foto">
				<?php if($model->getWebcam()) echo '<img src="' . $model->getWebcam() . '">'; ?>
			</div>
		</div>
		<div class="tab-pane" id="tab-<?php echo $name . 'Gravatar'; ?>">
			<?php echo $form->labelEx($model, $name . 'Gravatar', array('class' => 'control-label')); ?>
			<?php echo $form->textField($model, $name . 'Gravatar', array('class' => '', 'id' => $name . 'Gravatar', 'name' => $model->getNameInForm() . '[Gravatar]')); ?>
			<?php echo $form->error($model, $name, array('inputID' => $name . 'Gravatar')); ?>
			<?php /*echo $form->labelEx($model, $model->getName() . '_gravatar', array('class' => 'control-label')); ?>
			<?php echo $form->textArea($model, $model->getName() . '_gravatar', array('class' => '', 'id' => $model->getName() . '_gravatar', 'name' => $model->getNameInForm() . '[gravatar]')); ?>
			<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName() . '_gravatar')); */?>
		</div>
	</div>
	<!-- The template to display files available for upload -->
	<script id="template-upload-<?php echo $name; ?>" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		<tr class="template-upload fade">
			<td class="preview">
				<span class="fade"></span>
			</td>
			<td class="name">
				{%=file.name%}
				<div class="progress progress-success progress-striped active">
					<div class="bar" style="width:0%;"></div>
				</div>
			</td>
			<td class="size">{%=o.formatFileSize(file.size)%}</td>
			{% if (file.error) { %}
				<td class="error">
					<span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}
				</td>
			{% } else if (o.files.valid && !i) { %}
				<td class="start">
					{% if (!o.options.autoUpload) { %}
						<button class="btn btn-primary">
							<i class="icon-upload icon-white"></i> {%=locale.fileupload.start%}
						</button>
					{% } %}
				</td>
			{% } else { %}
				<td></td>
			{% } %}
			<td class="cancel">
				{% if (!i) { %}
					<button class="btn btn-warning">
						<i class="icon-ban-circle icon-white"></i> {%=locale.fileupload.cancel%}
					</button>
				{% } %}
			</td>
		</tr>
		{% } %}
	</script>
	<!-- The template to display files available for download -->
	<script id="template-download-<?php echo $name; ?>" type="text/x-tmpl">
		{% for (var i=0, file; file=o.files[i]; i++) { %}
		<tr class="template-download fade">
			{% if (file.error) { %}
				<td class="preview"></td>
				<td class="name">{%=file.name%}</td>
				<td class="size">{%=o.formatFileSize(file.size)%}</td>
				<td class="error"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
			{% } else { %}
				<td class="preview">
					{% if (file.url) { %}
						<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.url%}"></a>
					{% } %}
				</td>
				<td class="name">
					<p>{%=file.name%}</p>
				</td>
				<td class="size">{%=o.formatFileSize(file.size)%}</td>
				<td class="error"></td>
			{% } %}
			<td class="delete">
				<button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
					<i class="icon-trash icon-white"></i> {%=locale.fileupload.destroy%}
				</button>
			</td>
		</tr>
		{% } %}
	</script>
</div>
