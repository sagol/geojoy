<div class="form">
<?php // табы
	$route = $this->getRoute();
	if(substr($route, 0, 9) == 'messages/') $messages = true;
	else $messages = false;

	if(substr($route, 0, 15) == 'site/bookmarks/') $bookmarks = true;
	else $bookmarks = false;

	if(substr($route, 0, 22) == 'site/user/multiAccount') $multiAccount = true;
	else $multiAccount = false;

	if(substr($route, 0, 15) == 'passField/edit/') $password = true;
	else $password = false;

	$tabs = array(
		array('label' => \Yii::t('nav', 'YOU_OBJECTS'), 'url' => array('/site/user/objects')),
		array('label' => \Yii::t('nav', 'YOU_MESSAGES'), 'url' => array('/messages/threads/index'), 'active' => $messages),
		array('label' => \Yii::t('nav', 'YOU_BOOKMARKS'), 'url' => array('/site/bookmarks/index'), 'active' => $bookmarks),
		array('label' => \Yii::t('nav', 'USER_PROFILE'), 'url' => array('/site/user/profile')),
		array('label' => \Yii::t('nav', 'USER_PROFILE_EDIT'), 'url' => array('/site/user/edit')),
		array('label' => \Yii::t('nav', 'USER_PROFILE_PASSWORD'), 'url' => array('/passField/edit/index'), 'active' => $password, 'visible' => Yii::app()->user->account != \app\models\users\User::ACCOUNT_SOCIAL),
		array('label' => \Yii::t('nav', 'USER_MULTI_ACCOUNT'), 'url' => array('/site/user/multiAccount'), 'active' => $multiAccount),
		array('label' => \Yii::t('nav', 'USER_SETTINGS'), 'url' => array('/site/user/settings')),
	);
	$this->widget('\app\components\widgets\Menu', array(
		'htmlOptions' => array('class' => 'nav nav-tabs'),
		'translate' => 'nav',
		'items' => $tabs,
	));
?>
</div>