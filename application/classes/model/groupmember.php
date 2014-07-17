<?php

class Model_Groupmember extends ORM {
   protected $_table_name = 'groupmembers';
   protected $_primary_key = 'id';
   
   protected $_belongs_to = array ( 'group' => array ( 'foreign_key' => 'groupID' ), 'chainmap' => array('foreign_key' => 'chainmap_id') ); 

			

   public function rules()
   {
      return array(
         'eveID' => array(
            array('not_empty'),
            array('numeric'),
         ),
         'accessName' => array(
            array('not_empty'),
            array('max_length', array(':value', 255)),
         )
      ); 
   }			
}

