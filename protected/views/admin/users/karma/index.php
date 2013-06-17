<?php

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('users-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo \Yii::t('admin', 'KARMA_LIST'); ?></h1>

<?php echo CHtml::link(\Yii::t('admin', 'FIND_ADVANCED'), '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php 
	if(Yii::app()->request->enableCsrfValidation) {
		$csrfTokenName = Yii::app()->request->csrfTokenName;
		$csrfToken = Yii::app()->request->csrfToken;
		$csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
	}
	else $csrf = '';

$this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-users--karma-grid',
	'dataProvider' => $model->search(),
	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'columns' => array(
		array('name' => 'username', 'id' => 'table_td2'),
		array('name' => 'votedname', 'id' => 'table_td2'),
		array('name' => 'comment', 'id' => 'table_td'),
		array('name' => 'points', 'id' => 'table_td1'),
		array(
			'class' => '\app\components\ButtonColumn',
			'template' => '{delete} {view} {deflect} {approve}',
			'buttons' => array(
				'approve' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("admin/users/karma/approve", array("id" => $data->primaryKey))',
					'options' => array('class' => 'approve', 'title' => \Yii::t('admin', 'APPROVE')),
					'click' =>  "function() {
						var th=this;
						$.fn.yiiGridView.update('app-models-users--karma-grid', {
							type:'POST',
							url:$(this).attr('href'),$csrf
							success:function(data) {
								$.fn.yiiGridView.update('app-models-users--karma-grid');
							},
						});
						return false;
					}",
				),
				'deflect' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("admin/users/karma/deflect", array("id" => $data->primaryKey))',
					'options' => array('class' => 'deflect', 'title' => \Yii::t('admin', 'DEFLECT')),
					'click' =>  "function() {
						var th=this;
						$.fn.yiiGridView.update('app-models-users--karma-grid', {
							type:'POST',
							url:$(this).attr('href'),$csrf
							success:function(data) {
								$.fn.yiiGridView.update('app-models-users--karma-grid');
							},
						});
						return false;
					}",
				),
			),
		),
	),
)); ?>
