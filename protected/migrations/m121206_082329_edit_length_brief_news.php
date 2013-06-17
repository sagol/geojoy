<?php

class m121206_082329_edit_length_brief_news extends CDbMigration {


	public function up() {
		$sql = 'ALTER TABLE news ALTER COLUMN brief TYPE character varying;';
		$this->execute($sql);
	}


	public function down() {
		$sql = 'ALTER TABLE news ALTER COLUMN brief TYPE character varying(250);';
		$this->execute($sql);
	}


}