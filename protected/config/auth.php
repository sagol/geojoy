<?php

return array(

	'editOrDeleteObject' => array (
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Изменение объекта',
		'bizRule' => NULL,
		'data' => NULL,
	),
	'editOrDeleteObjectUser' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'editOrDeleteObject',
		),
		'bizRule' => 'return $params["multiUser"] == $params["curUser"]->multiUser;',
		'data' => NULL,
	),
	'editOrDeleteObjectCompanyUser' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'editOrDeleteObject',
		),
		'bizRule' => 'return $params["idusers"] == $params["curUser"]->id;',
		'data' => NULL,
	),
	'editOrDeleteObjectCompany' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'editOrDeleteObject',
		),
		'bizRule' => 'return $params["multiUser"] == $params["curUser"]->multiUser;',
		'data' => NULL,
	),



	'markSpamObjectAndAddBookmarks' => array (
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Пометить объект как спам',
		'bizRule' => 'return $params["multiUser"] != $params["curUser"]->multiUser;',
		'data' => NULL,
	),



	'multiAccountWithdraw' => array (
		'type' => CAuthItem::TYPE_OPERATION,
		'description' => 'Отсоединить акк от мульти акка',
		'bizRule' => NULL,
		'data' => NULL,
	),
	'multiAccountWithdrawUser' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'multiAccountWithdraw',
		),
		'bizRule' => 'return $params["multiUser"] == $params["curUser"]->multiUser;',
		'data' => NULL,
	),
	'multiAccountWithdrawCompanyUser' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'multiAccountWithdraw',
		),
		'bizRule' => 'return $params["idusers"] == $params["curUser"]->id;',
		'data' => NULL,
	),
	'multiAccountWithdrawCompany' => array (
		'type' => CAuthItem::TYPE_TASK,
		'description' => 'Изменение объекта',
		'children' => array(
			'multiAccountWithdraw',
		),
		'bizRule' => 'return $params["multiUser"] == $params["curUser"]->multiUser;',
		'data' => NULL,
	),







	'guest' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Guest',
		'bizRule' => null,
		'data' => null,
	),

	'authorized' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Authorized',
		'children' => array(
			'guest',
			'markSpamObjectAndAddBookmarks',
		),
		'bizRule' => null,
		'data' => null,
	),

	'user' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'User',
		'children' => array(
			'authorized',
			'editOrDeleteObjectUser',
			'multiAccountWithdrawUser',
		),
		'bizRule' => null,
		'data' => null,
	),

	'companyUser' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Company User',
		'children' => array(
			'authorized',
			'editOrDeleteObjectCompanyUser',
			'multiAccountWithdrawCompanyUser',
		),
		'bizRule' => null,
		'data' => null,
	),

	'company' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Company',
		'children' => array(
			'companyUser',
			'editOrDeleteObjectCompany',
			'multiAccountWithdrawCompany',
		),
		'bizRule' => null,
		'data' => null,
	),

	'moder' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Moderator',
		'children' => array(
			'authorized',
			'editOrDeleteObject',
		),
		'bizRule' => null,
		'data' => null,
	),

	'admin' => array(
		'type' => CAuthItem::TYPE_ROLE,
		'description' => 'Administrator',
		'children' => array(
			'moder',
		),
		'bizRule' => null,
		'data' => null,
	),



);