<?php

class m121126_112422_add_order_column_ties_groups extends CDbMigration {


	public function up() {
		$sql = <<<SQL
CREATE SEQUENCE obj_ties_groups_orders_seq
    START WITH 10
    INCREMENT BY 10
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
SQL;

		$this->execute($sql);
		$this->addColumn('obj_ties_groups', 'orders', "integer DEFAULT nextval('obj_ties_groups_orders_seq'::regclass) NOT NULL");
	}


	public function down() {
		$this->dropColumn('obj_ties_groups', 'orders');

		$sql = <<<SQL
DROP SEQUENCE IF EXISTS obj_ties_groups_orders_seq
SQL;

		$this->execute($sql);
	}


}