<?php

namespace app\components\eauth;

class Google extends \GoogleOAuthService {


	protected function fetchAttributes() {
		$info = (object)$this->makeSignedRequest('https://www.googleapis.com/oauth2/v1/userinfo');
				
		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		
		if (!empty($info->link))
			$this->attributes['url'] = $info->link;
		else $this->attributes['url'] = null;

		$this->attributes['friendsCount'] = false;


		$socialInfo = array();
		if(isset($info->link)) $socialInfo['LINK'] = $info->link;
		if(isset($info->name)) $socialInfo['FULL_NAME'] = $info->name;
		if(isset($info->given_name)) $socialInfo['FIRST_NAME'] = $info->given_name;
		if(isset($info->family_name)) $socialInfo['LAST_NAME'] = $info->family_name;
		if(isset($info->gender)) $socialInfo['GENDER'] = $info->gender;
		$this->attributes['socialInfo'] = $socialInfo;
	}


}