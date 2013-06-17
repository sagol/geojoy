<?php

namespace app\components\eauth;

class Facebook extends \FacebookOAuthService {


	protected function fetchAttributes() {
		$info = (object)$this->makeSignedRequest('https://graph.facebook.com/me');

		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		$this->attributes['url'] = $info->link;

		$friends = (object) $this->makeSignedRequest('https://graph.facebook.com/me/friends');
		$this->attributes['friendsCount'] = count($friends->data);


		$socialInfo = array();
		if(isset($info->link)) $socialInfo['LINK'] = $info->link;
		if(isset($info->username)) $socialInfo['NICKNAME'] = $info->username;
		if(isset($info->name)) $socialInfo['FULL_NAME'] = $info->name;
		if(isset($info->first_name)) $socialInfo['FIRST_NAME'] = $info->first_name;
		if(isset($info->last_name)) $socialInfo['LAST_NAME'] = $info->last_name;
		if(isset($info->gender)) $socialInfo['GENDER'] = $info->gender;
		$this->attributes['socialInfo'] = $socialInfo;
	}


}