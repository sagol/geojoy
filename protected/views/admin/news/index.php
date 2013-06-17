<?php

$this->menu = array(
	array('label' => \Yii::t('admin', 'CREATE'), 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('news-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'NEWS_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php 
	$this->widget('\app\components\core\GridView', array(
		'id' => 'news-grid',
		'dataProvider' => $model->search(),
		'filter' => $model,
		'cssFile'  =>  false,
		'itemsCssClass'  =>  'admin table table-striped',
		'columns' => array(
			array('name' => 'idnews', 'id' => 'table_id'),
			array('name' => 'title', 'id' => 'table_td', 'value' => array('\app\models\news\News', 'gridValue')),
			array('name' => 'brief', 'type' => 'html', 'id' => 'table_td2', 'value' => array('\app\models\news\News', 'gridValue')),
			// array('name' => 'news', 'type' => 'html', 'id' => 'table_td2', 'value' => array('\app\models\news\News', 'gridValue')),
			array(
				'name' => 'status',
				'id' => 'table_td2',
				'value' => function($data, $row, $column) {
					$value = $data->attributes[$column->name];
					$values = array(\Yii::t('admin', 'NEWS_DRAFT'), \Yii::t('admin', 'NEWS_PUBLISHED'), \Yii::t('admin', 'NEWS_PROCESSED'));
					return isset($values[$value]) ? $values[$value] : '';
				}
			),
			array(
				'name' => 'type',
				'id' => 'table_td2',
				'value' => function($data, $row, $column) {
					$value = $data->attributes[$column->name];
					$values = array(\Yii::t('admin', 'NEWS_SITE'), \Yii::t('admin', 'NEWS_USERS_INFO'), \Yii::t('admin', 'NEWS_EMAIL_INFO'));
					return isset($values[$value]) ? $values[$value] : '';
				}
			),
			array('name' => 'create', 'id' => 'table_td2', 'class' => '\app\components\DateColumn'),
			array('name' => 'publish', 'id' => 'table_td2', 'class' => '\app\components\DateColumn'),
			array(
				'class' => '\app\components\ButtonColumn',
				'deleteConfirmation' => \Yii::t('admin', 'CONFIRM_DELETE_NEWS'),
			),
		),
	));
?>