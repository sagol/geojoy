<?php
	$baseUrl = Yii::app()->request->getBaseUrl();
	$maxFiles = $model->getMaxUploadFiles();
	$fieldName = $model->getName();
	$textMain = \Yii::t('fields', 'FOTO_ON_MAIN');
	$textToMain = \Yii::t('fields', 'FOTO_TO_MAIN');
	$id = $model->getManager()->getId();
	$type = $model->getTable();
	$new = ($model->getManager()->getIsNewRecord() ? '&new=1' : '');

	$jquery = <<<JQUERY
	$('#fileupload-$fieldName').fileupload({
		url: '$baseUrl/photosField/upload/ajax.html?name=$fieldName&id=$id&type=$type{$new}',
		maxNumberOfFiles: $maxFiles,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
		maxFileSize: 5000000,
		dropZone: $('#fileupload-$fieldName'),
		uploadTemplateId: 'template-upload-$fieldName',
		downloadTemplateId: 'template-download-$fieldName',
	});

	// Load existing files:
	$.getJSON('$baseUrl/photosField/upload/uploaded.html?name=$fieldName&id=$id&type=$type{$new}', function (result) {
		if (result && result.length) {
			var that = $('#fileupload-$fieldName');
			$(that).fileupload('option', 'done')
			.call(that, null, {result: result});
		}
	});

	$('body').delegate('div.main', 'click', function(){
		var div = $(this);
		if(!div.hasClass('set')) {
			$.getJSON('$baseUrl/photosField/upload/setMain.html?name=$fieldName&id=$id&type=$type{$new}&foto=' + div.attr('data'), function (result) {
				if(result.status == 'ok') {
					$('div.main.set').toggleClass('set').text('$textToMain');
					div.toggleClass('set').text('$textMain');
				}
			});
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

	$cs = \Yii::app()->getClientScript();
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
<div class="control-group noline" id="fileupload-<?php echo $model->getName(); ?>">
	<?php echo $form->labelEx($model, $model->getName(), array('class' => 'control-label')); ?>
	<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
	<div class="fileupload-buttonbar">
		<div class="buttonbar">
			<!-- The fileinput-button span is used to style the file input field as button -->
			<div class="btn btn-success fileinput-button">
				<i class="icon-plus icon-white"></i> <?php echo \Yii::t('nav', 'FORM_UPLOAD_FILES_ADD_FILES'); ?>
				<input type="file" name="<?php echo $model->getNameInForm(); ?>" multiple>
			</div>
			<button type="submit" class="btn btn-primary start">
				<i class="icon-upload icon-white"></i> <?php echo \Yii::t('nav', 'FORM_UPLOAD_FILES_START_ALL'); ?>
			</button>
			<button type="reset" class="btn btn-warning cancel">
				<i class="icon-ban-circle icon-white"></i> <?php echo \Yii::t('nav', 'FORM_UPLOAD_FILES_CANCEL_ALL'); ?>
			</button>
			
      <button type="button" class="btn btn-danger delete">
				<i class="icon-trash icon-white"></i> <?php echo \Yii::t('nav', 'FORM_UPLOAD_FILES_DESTROY_ALL'); ?>
			</button>
			<input type="checkbox" class="toggle">
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
	<?php echo $form->error($model, $model->getName(), array('inputID' => $model->getName())); ?>
	</div>
	
	
</div>
<!-- The template to display files available for upload -->
<script id="template-upload-<?php echo $model->getName(); ?>" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-upload fade">
		<td class="preview"><span class="fade"></span></td>
		<td class="name">{%=file.name%}
    <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div></td>
		<td class="size">{%=o.formatFileSize(file.size)%}</td>
		{% if (file.error) { %}
		<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
		{% } else if (o.files.valid && !i) { %}
		<td class="start">{% if (!o.options.autoUpload) { %}
			<button class="btn btn-primary">
			<i class="icon-upload icon-white"></i> {%=locale.fileupload.start%}
			</button>
		{% } %}</td>
		{% } else { %}
		<td colspan="2"></td>
		{% } %}
	</tr>
	{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download-<?php echo $model->getName(); ?>" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-download fade">
		{% if (file.error) { %}
		<td></td>
		<td class="name">{%=file.name%}</td>
		<td class="size">{%=o.formatFileSize(file.size)%}</td>
		<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
		{% } else { %}
		<td class="preview">{% if (file.thumbnail_url) { %}
			<a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
		{% } %}</td>
		<td class="name">
			<p>{%=file.name%}</p>
			{% if (file.main) { %}
			<div class="btn main set" data="{%=file.url%}"><?php echo $textMain; ?></div>
			{% } else { %}
			<div class="btn main" data="{%=file.url%}"><?php echo $textToMain; ?></div>
			{% } %}
		</td>
		<td class="size">{%=o.formatFileSize(file.size)%}</td>
		<td class="delete">
		<button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
			<i class="icon-trash icon-white"></i> {%=locale.fileupload.destroy%}
		</button>
		<input type="checkbox" name="delete" value="1">
		</td>
		{% } %}
	</tr>
	{% } %}
</script>