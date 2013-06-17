<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru" dir="ltr">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="generator" content="">
	<meta name="robots" content="index, follow" />
	<meta name="description" content="">
	<meta name="keywords" content="" />
	<?php $this->renderHeaderMeta(); ?>
	<link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link rel="stylesheet" href="<?php echo Yii::app()->request->getBaseUrl() ?>/css/style.css" type="text/css" media="screen" />
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>
	<?php echo $content; ?>
</body>
</html>