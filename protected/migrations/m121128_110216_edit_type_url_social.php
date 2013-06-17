<?php

class m121128_110216_edit_type_url_social extends CDbMigration {


	public function up() {
		$sql = 'ALTER TABLE services ALTER COLUMN url_social TYPE character varying;';
		$this->execute($sql);
	}


	public function down() {
		$sql = 'ALTER TABLE services ALTER COLUMN url_social TYPE character varying(300);';
		$this->execute($sql);
	}


}