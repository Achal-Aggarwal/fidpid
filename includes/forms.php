<?php

// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');

class Forms {

  	protected static $table_name="forms";
	protected static $db_fields=array('id','user_id' ,'question', 'ch_lowest', 'ch_low', 'ch_average','ch_high','ch_highest',
										'greet_msg','fc_qm','fc_ch','fc_ca','fc_bg','icon','logo','less_trigger','equal_trigger','greater_trigger');
	public $id;
	public $user_id;
	public $question;
	public $ch_lowest;
	public $ch_low;
	public $ch_average;
	public $ch_high;
	public $ch_highest;
	public $greet_msg;
	public $fc_qm;
	public $fc_ch;
	public $fc_ca;
	public $fc_bg;
	public $icon;
	public $logo;
	public $less_trigger;
	public $equal_trigger;
	public $greater_trigger;

  // "new" is a reserved word so we use "make" (or "build")
	public static function make($user_id,$question,$ch_lowest,$ch_low, $ch_average, $ch_high, $ch_highest, $greet_msg,
		$fc_qm, $fc_ch, $fc_ca, $fc_bg,$less_trigger="-1",$equal_trigger="-1",$greater_trigger="0") 
	{
	    if(!empty($user_id) && !empty($question) && !empty($ch_lowest) && !empty($ch_low) && !empty($ch_average) && !empty($ch_high) && !empty($ch_highest)
	    		&& !empty($greet_msg) && !empty($fc_qm) && !empty($fc_ch) && !empty($fc_ca) && !empty($fc_bg))
	    {
				$form = new Forms();
				$form->user_id = $user_id;
			    $form->question = $question;
			    $form->ch_lowest = $ch_lowest;
			    $form->ch_low = $ch_low;
			    $form->ch_average = $ch_average;
			    $form->ch_high = $ch_high;
			    $form->ch_highest = $ch_highest;
			    $form->greet_msg = $greet_msg;
			    $form->fc_qm = $fc_qm;
			    $form->fc_ch = $fc_ch;
			    $form->fc_ca = $fc_ca;
			    $form->fc_bg = $fc_bg;
			    $form->icon = $icon;
			    $form->logo = $logo;
			    $form->less_trigger = $less_trigger;
			    $form->equal_trigger = $equal_trigger;
			    $form->greater_trigger = $greater_trigger;
			    return $form;
		} 
		else {
				return false;
			}
	}
	
	// Common Database Methods
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM ".self::$table_name);
  }
  
  public static function find_by_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }

  public static function find_by_user_id($id=0) {
    $result_array = self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE user_id={$id} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public static function find_by_sql($sql="") {
    global $database;
    $result_set = $database->query($sql);
    $object_array = array();
    while ($row = $database->fetch_array($result_set)) {
      $object_array[] = self::instantiate($row);
    }
    return $object_array;
  }

	public static function count_all() {
	  global $database;
	  $sql = "SELECT COUNT(*) FROM ".self::$table_name;
    $result_set = $database->query($sql);
	  $row = $database->fetch_array($result_set);
    return array_shift($row);
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
    $object = new self;
		// Simple, long-form approach:
		// $object->id 				= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(self::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $database->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
	  $sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
	  $sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
	  if($database->query($sql)) {
	    $this->id = $database->insert_id();
	    return true;
	  } else {
	    return false;
	  }
	}

	public function update() {
	  global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE id=". $database->escape_value($this->id);
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
	  $sql = "DELETE FROM ".self::$table_name;
	  $sql .= " WHERE id=". $database->escape_value($this->id);
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}

}

?>