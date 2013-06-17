<?php

namespace app\components\eauth;

class Twitter extends \TwitterOAuthService {


	protected function fetchAttributes() {
		$info = $this->makeSignedRequest('https://api.twitter.com/1/account/verify_credentials.json');
		
		$this->attributes['id'] = $info->id;
		$this->attributes['name'] = $info->name;
		$this->attributes['url'] = 'http://twitter.com/account/redirect_by_id?id='.$info->id_str;
		$this->attributes['friendsCount'] = $info->friends_count;


		$socialInfo = array();
		if(isset($info->profile_image_url)) $socialInfo['PROFILE_IMAGE'] = $info->profile_image_url;
		if(isset($info->screen_name)) $socialInfo['NICKNAME'] = $info->screen_name;
		if(isset($info->name)) $socialInfo['FULL_NAME'] = $info->name;
		if(isset($info->friends_count)) $socialInfo['FRIENDS'] = $info->friends_count;
		if(isset($info->followers_count)) $socialInfo['FOLLOWERS'] = $info->followers_count;
		if(isset($info->created_at)) $socialInfo['CREATED'] = $info->created_at;
		$this->attributes['socialInfo'] = $socialInfo;
	}


}