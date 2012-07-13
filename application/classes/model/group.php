<?php

class Model_Group extends ORM {
   protected $_table_name = 'groups';
   protected $_primary_key = 'groupID';
   protected $_has_many = array(
      // auth
      'groupmembers' => array('foreign_key' => 'groupID'),
      'subgroups' => array('foreign_key' => 'groupID'),
      );
}

