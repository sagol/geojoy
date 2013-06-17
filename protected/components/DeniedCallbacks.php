<?php

namespace app\components;

/**
 * 
 */
class DeniedCallbacks {


	static function objects($rule) {
		$app = \Yii::app();
		$isAjaxRequest = $app->getRequest()->getIsAjaxRequest();

		$user = $app->user;
		if($user->getIsGuest()) {
			$route = $app->getCurrentRoute();
			if($isAjaxRequest) {
				if($route == 'site/objects/objectUp' || $route == 'site/objects/toSpam' ||
					$route == 'site/objects/notSpam' || $route == 'site/objects/moderateOk')
					echo \Yii::t('nav', 'NEED_LOGIN');

				\Yii::app()->end();
			}
			else $user->loginRequired();
		}
		else throw new CHttpException(403, \Yii::t('yii', 'You are not authorized to perform this action.'));
	}


}