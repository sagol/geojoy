<?php

class m130130_112112_add_column_disabled_in_table_objects extends CDbMigration {


	public function up() {
		$this->addColumn('objects', 'disabled', 'smallint DEFAULT 0');
	}


	public function down() {
		$this->dropColumn('objects', 'disabled');
	}


}
