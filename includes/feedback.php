<?php

// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(LIB_PATH.DS.'database.php');

class Response {

  	protected static $table_name="feedbacks";
	protected static $db_fields=array('id', 'form_id','bill_no', 'name', 'email', 'response_one','response_two','response_three',
										'response_four','response_five','time','comment','total_avg','viewed','notified');
	public $id;
	public $form_id;
	public $bill_no;
	public $name;
	public $email;
	public $response_one;
	public $response_two;
	public $response_three;
	public $response_four;
	public $response_five;
	public $time;
	public $comment;
	public $time_string;
	public $total_avg;
	public $viewed;
	public $notified;

  // "new" is a reserved word so we use "make" (or "build")
	public static function make($bill_no,$form_id,$name,$email, $response_one, $response_two, $response_three, $response_four,
		$response_five, $total_avg, $comment="") 
	{
	    if(!empty($bill_no) && !empty($form_id) && !empty($name) && !empty($email) && !empty($response_one) && !empty($response_two) 
	    		&& !empty($response_three) && !empty($response_four) && !empty($response_five) && !empty($total_avg))
	    {
				$reponse = new Response();
			    $reponse->form_id = (int)$form_id;
			    $reponse->bill_no = $bill_no;
			    $reponse->time = time();
			    $reponse->name = $name;
			    $reponse->email = $email;
			    $reponse->response_one = $response_one;
			    $reponse->response_two = $response_two;
			    $reponse->response_three = $response_three;
			    $reponse->response_four = $response_four;
			    $reponse->response_five = $response_five;
			    $reponse->comment = $comment;
			    $reponse->total_avg = $total_avg;
			    return $reponse;
		} 
		else {
				return false;
			}
	}
	
	// Common Database Methods
	public static function find_all($id) {
		return self::find_by_sql("SELECT * FROM ".self::$table_name." WHERE form_id={$id}");
  	}
	public static function find_by_id($id,$form_id) {
		$s = "SELECT * FROM ".self::$table_name." WHERE id={$id} AND form_id={$form_id}  LIMIT 1";
	$result_array = self::find_by_sql($s,false);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	public static function find_next($time,$form_id) {
		$s = "SELECT * FROM ".self::$table_name." WHERE time<{$time} AND form_id={$form_id} ORDER BY time DESC LIMIT 1";
	$result_array = self::find_by_sql($s,false);
		return !empty($result_array) ? array_shift($result_array) : false;
	}


	//For feedback page

	public static function find_all_by_formid($id=0, $less_trigger="-1", $equal_trigger="-1", $greater_trigger="26") {
			$s = "SELECT * FROM ".self::$table_name." WHERE form_id={$id} AND ( total_avg < {$less_trigger} OR  total_avg = {$equal_trigger} OR  total_avg > {$greater_trigger})";
			return self::find_by_sql($s);
	}

	public static function count_all_by_formid($id=0, $less_trigger="-1", $equal_trigger="-1", $greater_trigger="26",$startTime=0, $endTime=0, $extracondition="") {
			global $database;
			$sql = "SELECT COUNT(*) FROM ".self::$table_name." WHERE form_id={$id}";
			$sql .= " AND ( total_avg < {$less_trigger} OR  total_avg = {$equal_trigger} OR  total_avg > {$greater_trigger})";
			if($extracondition!="")
				$sql .= " AND ".$extracondition;
			if($startTime!=0 || $endTime!=0)
				$sql .= " AND time >= {$startTime} AND time <= {$endTime}";
			$result_set = $database->query($sql);
			$row = $database->fetch_array($result_set);
		    return array_shift($row);
	}

	public static function find_all_by_formid_paginated($id=0, $less_trigger="-1", $equal_trigger="-1", $greater_trigger="26",$limit=-1, $offset=-1,$startTime=0, $endTime=0, $extracondition="") {
			$sql = "SELECT * FROM ".self::$table_name." WHERE form_id={$id} AND ( total_avg < {$less_trigger} OR  total_avg = {$equal_trigger} OR  total_avg > {$greater_trigger}) ";
			if($startTime!=0 || $endTime!=0)
				$sql .= " AND time >= {$startTime} AND time <= {$endTime}";
			if($extracondition!="")
				$sql .= " AND ".$extracondition;
			$sql .= " ORDER BY time DESC";
			if($limit!=-1 && $offset!= -1)$sql .= " LIMIT {$limit} OFFSET {$offset}";
			return self::find_by_sql($sql,false);
	}
	public static function count_all_by_quest_option($id,$quest,$option,$startTime=0,$endTime=0){
		if($quest==1)
				return self::count_all_by_formid($id,26,0,0,$startTime,$endTime,"response_one={$option}");
			else if($quest==2)
				return self::count_all_by_formid($id,26,0,0,$startTime,$endTime,"response_two={$option}");
			else if($quest==3)
				return self::count_all_by_formid($id,26,0,0,$startTime,$endTime,"response_three={$option}");
			else if($quest==4)
				return self::count_all_by_formid($id,26,0,0,$startTime,$endTime,"response_four={$option}");
			else if($quest==5)
				return self::count_all_by_formid($id,26,0,0,$startTime,$endTime,"response_five={$option}");
	}

	public static function find_all_by_quest_option_paginated($id,$quest,$option,$limit=-1, $offset=-1,$startTime=0,$endTime=0){
			if($quest==1)
				return self::find_all_by_formid_paginated($id,26,0,0,$limit,$offset,$startTime,$endTime,"response_one={$option}");
			else if($quest==2)
				return self::find_all_by_formid_paginated($id,26,0,0,$limit,$offset,$startTime,$endTime,"response_two={$option}");
			else if($quest==3)
				return self::find_all_by_formid_paginated($id,26,0,0,$limit,$offset,$startTime,$endTime,"response_three={$option}");
			else if($quest==4)
				return self::find_all_by_formid_paginated($id,26,0,0,$limit,$offset,$startTime,$endTime,"response_four={$option}");
			else if($quest==5)
				return self::find_all_by_formid_paginated($id,26,0,0,$limit,$offset,$startTime,$endTime,"response_five={$option}");
	}

  public static function find_all_N($N="10") {
    	return self::find_by_sql("SELECT * FROM ".self::$table_name." LIMIT ".$N);
  }
  
  public static function find_by_sql($sql="",$do_ordering=true) {
    global $database;

    if($do_ordering)$sql=$sql." ORDER BY time DESC";

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
		//$object->time_string = 
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