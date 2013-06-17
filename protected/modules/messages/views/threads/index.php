<?php

	$this->breadcrumbs += array(Yii::t('nav', 'NAV_PRIVATE_CABINET'));
	/*табы*/ $this->renderPartial('app.views.layouts.userTabs');
?>
<?php /*табы*/ $this->renderPartial('app.views.layouts.userMessagesTabs', array('filter' => $filter)); ?>
<?php $this->widget('\app\components\core\GridView', array(
	'id' => 'app-models-object--fields-grid',
	'dataProvider' => $model->search($filter),
// 	'filter' => $model,
	'cssFile'  =>  false,
	'itemsCssClass'  =>  'admin table table-striped',
	'rowCssClassExpression' => '($row % 2 ? "odd" : "even") . ($data["notread"] ? " new" : "");',
	'columns' => array(
		array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_INTERLOCUTOR'),
			'name' => 'interlocutor',
			'id' => 'table_td',
		),
		array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_OBJECT'),
			'name' => 'object',
			'id' => 'table_td3',
			'type' => 'html',
			'value' => array('\app\modules\messages\models\Test', 'object')
		),
		array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_WRITER'),
			'name' => 'writer_name',
			'id' => 'table_td3',
		),
		array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_TEXT'),
			'name' => 'text',
			'id' => 'table_td2',
		),
		/*array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_NOT_READ'),
			'name' => 'notread',
			'id' => 'table_td2',
		),*/
		array(
			'header' => \Yii::t('app\modules\messages\MessagesModule.messages', 'COLUMN_DATE'),
			'name' => 'date',
			'id' => 'table_td2',
			'class' => '\app\components\DateTimeColumn',
		),
		array(
			'class' => '\app\components\ButtonColumn',
			// 'deleteConfirmation' => \Yii::t('admin', 'CONFIRM_DELETE_FIELD'),
			// 'template' => '{delete} {update} {view} {values}',
			'template' => '{delete}{print} {view}',
			'buttons' => array(
				/*'values' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("admin/object/fieldsValues/index", array("id" => $data->primaryKey))',
					'visible' => '$data->type >= 8 && $data->type <= 11',
					'options' => array('class' => 'value', 'title' => \Yii::t('admin', 'VALUES')),
				),*/
				'print' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("/messages/threads/print", array("id" => $data["id"]))',
					'options' => array('class' => 'print', 'title' => \Yii::t('admin', 'PRINT'), 'target' => '_blank'),
				),
				'view' => array(
					'filter' => $filter,
					'label' => '',
					'url' => array('\app\modules\messages\models\Test', 'viewUrl'),
					'options' => array('class' => 'view', 'title' => \Yii::t('admin', 'VIEW'), 'target' => '_blank'),
				),
				'delete' => array(
					'label' => '',
					'url' => 'Yii::app()->controller->createUrl("/messages/threads/del", array("threads" => $data["id"]))',
					'options' => array('class' => 'delete', 'title' => \Yii::t('admin', 'DELETE'), 'target' => '_blank'),
				),
			),
		),
	),
));