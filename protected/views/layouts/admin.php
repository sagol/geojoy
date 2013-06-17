<?php
	$baseUrl = Yii::app()->request->getBaseUrl();
$jquery = <<<JQUERY
	size();

	$(window).resize(function(){
		size();
	});

	function size() {
		var wrapperClass, wrapperWidth;
		var small = 700, medium = 996, large = 1296;

		if($('.width_S').length) {
			wrapperWidth = small;
			wrapperClass = 'width_S';
		}
		else if($('.width_M').length) {
			wrapperWidth = medium;
			wrapperClass = 'width_M';
		}
		else {
			wrapperWidth = large;
			wrapperClass = 'width_L';
		}

		var size = $(window).width();

		var wrapperClassNew;
		if(size < medium) wrapperClassNew = 'width_S';
		else if(size < large) wrapperClassNew = 'width_M';
		else wrapperClassNew = 'width_L';
		if(wrapperClass != wrapperClassNew)
			$('.' + wrapperClass).removeClass(wrapperClass).addClass(wrapperClassNew);
	}
JQUERY;

  $cs = Yii::app()->getClientScript();
	$cs->registerCoreScript('jquery');
	$cs->registerScriptFile($baseUrl . '/js/bootstrap.min.js');
	$cs->registerScriptFile($baseUrl . '/js/admin.js');
	$cs->registerScript('size', $jquery);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="">
	<meta name="robots" content="index, follow" />
	<meta name="description" content="">
	<meta name="keywords" content="" />
	<link href="<?php echo $baseUrl ?>/favicon.ico" rel="shortcut icon" type="image/x-icon" />

	<link rel="stylesheet" href="<?php echo $baseUrl ?>/css/style.css" type="text/css" media="screen" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<?php
		$flashes = Yii::app()->user->getFlashes();
		if(!empty($flashes)) {
			$cs = \Yii::app()->getClientScript();
			$cs->registerScriptFile($baseUrl . '/js/jquery.gritter.js', \CClientScript::POS_HEAD);
			$cs->registerCssFile($baseUrl . '/css/jquery.gritter.css');
			foreach($flashes as $id => $flash) {
				$jquery = <<<JQUERY
				$.gritter.add({
					// class_name: 'gritter-light',
					title: '$flash->title',
					text: '$flash->text',
				});
JQUERY;
				$cs->registerScript('flash' . $id, $jquery);
			}
		}
	?>
	<?php // верхнее меню
		echo $this->renderPartial('app.views.layouts.menu');
	?>

	<?php echo $content; ?>
</body>
</html>