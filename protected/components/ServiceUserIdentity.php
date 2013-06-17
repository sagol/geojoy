<?php

namespace app\components;

/**
 * Аутентификация пользователей через соцсеть
 */
class ServiceUserIdentity extends UserIdentity {


	/**
	 * Ошибка аутентификации
	 */
	const ERROR_NOT_AUTHENTICATED = 3;

	/**
	 * Соцсеть выполняющая аутентификацию пользователя
	 * @var string 
	 */
	protected $service;


	/**
	 * Инициализация параметров
	 * @param string $service 
	 */
	public function __construct($service) {
		$this->service = $service;
	}


	/**
	 * Аутентификация
	 * @return boolean 
	 */
	public function authenticate() {
		if($this->service->isAuthenticated) {
			$this->username = $this->service->getAttribute('name');
			$this->setState('__id', $this->service->getAttribute('id'));
			$this->setState('__name', $this->username);
			$this->setState('__service', $this->service->getServiceName());
			$this->setState('__friendsCount', $this->service->getAttribute('friendsCount'));
			$this->setState('__urlSocial', $this->service->getAttribute('url'));
			$this->setState('__socialInfo', $this->service->getAttribute('socialInfo'));
			$this->setState('__role', \app\models\users\User::ROLE_GUEST);
			$this->errorCode = self::ERROR_NONE;
		}
		else {
			$this->errorCode = self::ERROR_NOT_AUTHENTICATED;
		}


		return !$this->errorCode;
	}


}