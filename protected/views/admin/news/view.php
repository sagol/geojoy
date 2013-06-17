<?php

$this->breadcrumbs += array(
	$model->idnews,
);

$this->menu = array(
	array('label' => \Yii::t('admin', 'LIST'), 'url' => array('index')),
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
	array('label' => \Yii::t('admin', 'EDIT'), 'url' => array('update', 'id' => $model->idnews)),
	array('label' => \Yii::t('admin', 'DELETE'), 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->idnews), 'confirm' => \Yii::t('admin', 'CONFIRM_DELETE_NEWS'))),

);
?>

<h1><?php echo \Yii::t('admin', 'NEWS_ID', array('{id}' => $model->idnews)); ?></h1>

<?php
	$status = array(\Yii::t('admin', 'NEWS_DRAFT'), \Yii::t('admin', 'NEWS_PUBLISHED'));
	$type = array(\Yii::t('admin', 'NEWS_SITE'), \Yii::t('admin', 'NEWS_USERS_INFO'), \Yii::t('admin', 'NEWS_EMAIL_INFO'));
	$this->widget('zii.widgets.CDetailView', array(
		'data' => $model,
		'htmlOptions' => array('class' => 'admin table table-striped'),
		'attributes' => array(
			'idnews',
			array(
				'label' => \Yii::t('admin', 'NEWS_FIELD_TITLE'),
				'value' => \app\models\news\News::value($model->attributes, 'title'),
			),
			array(
				'label' => \Yii::t('admin', 'NEWS_FIELD_BRIEF'),
				'value' => \app\models\news\News::value($model->attributes, 'brief'),
			),
			array(
				'label' => \Yii::t('admin', 'NEWS_FIELD_NEWS'),
				'value' => \app\models\news\News::value($model->attributes, 'news'),
			),
			array(
				'label' => \Yii::t('admin', 'NEWS_FIELD_STATUS'),
				'value' => $status[$model->status],
			),
			array(
				'label' => \Yii::t('admin', 'NEWS_FIELD_TYPE'),
				'value' => $type[$model->type],
			),
			'create',
			'publish',
		),
	)); 
?>
