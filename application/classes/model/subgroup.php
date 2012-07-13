<?php

class Model_Subgroup extends ORM {
   protected $_table_name = 'subgroups';
   protected $_primary_key = 'subGroupID';
   protected $_has_many = array(
      // auth
      'groupmembers' => array('foreign_key' => 'subGroupID'),
      );
}

