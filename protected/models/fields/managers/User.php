<?php

namespace app\managers;

class User extends Manager {


	public function init() {
		parent::init();

		$this->setMainTable('users');
		$this->setMainTableAlias('u');
		$this->setMainTablePrimaryKey('idusers');
		$this->setMainTableSequence('users_idusers_seq');
	}


}