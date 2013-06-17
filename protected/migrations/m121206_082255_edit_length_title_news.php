<?php

class m121206_082255_edit_length_title_news extends CDbMigration {


	public function up() {
		$sql = 'ALTER TABLE news ALTER COLUMN title TYPE character varying;';
		$this->execute($sql);
	}


	public function down() {
		$sql = 'ALTER TABLE news ALTER COLUMN title TYPE character varying(200);';
		$this->execute($sql);
	}


}