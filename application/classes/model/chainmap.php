<?php

class Model_Chainmap extends ORM {
	protected $_table_name = 'chainmaps';
	protected $_primary_key = 'chainmap_id';
	protected $_has_many = array(
		'groupmembers' => array('foreign_key' => 'chainmap_id','through'=>'chainmaps_access'),
	);
}

