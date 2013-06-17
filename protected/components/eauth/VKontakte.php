<?php

namespace app\components\eauth;

class VKontakte extends \VKontakteOAuthService {


	protected function fetchAttributes() {
		$info = (array)$this->makeSignedRequest('https://api.vk.com/method/users.get.json', array(
			'query' => array(
				'uids' => $this->uid,
				'fields' => 'counters', // uid, first_name and last_name is always available
				//'fields' => 'nickname, sex, bdate, city, country, timezone, photo, photo_medium, photo_big, photo_rec',
			),
		));

		$info = $info['response'][0];

		$this->attributes['id'] = $info->uid;
		$this->attributes['name'] = $info->first_name.' '.$info->last_name;
		$this->attributes['url'] = 'http://vk.com/id'.$info->uid;
		$this->attributes['friendsCount'] = $info->counters->friends;


		$socialInfo = array();
		if(isset($info->photo)) $socialInfo['PROFILE_IMAGE'] = $info->photo;
		if(isset($info->nickname)) $socialInfo['NICKNAME'] = $info->nickname;
		if(isset($info->first_name)) $socialInfo['FIRST_NAME'] = $info->first_name;
		if(isset($info->last_name)) $socialInfo['LAST_NAME'] = $info->last_name;
		if(isset($info->sex)) $socialInfo['GENDER'] = $info->sex;
		if(isset($info->country)) $socialInfo['COUNTRY'] = $info->country;
		if(isset($info->city)) $socialInfo['CITY'] = $info->city;
		$this->attributes['socialInfo'] = $socialInfo;
	}


}