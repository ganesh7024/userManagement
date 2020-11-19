<?php

// include database and object files
require_once ('../../config/dbHandler.php');


class UserManager{
  
    
    public $name;
    public $email;
    public $designation;
    public $organization;
    public $password;
   
	
	function createUser() {
		
		$uid = $this -> GUID();
		
		$createQuery = "insert into users (id, name, email, designation, organization, password) values ('$uid', '$this->name', '$this->email', '$this->designation','$this->organization', '$this->password')";
		
		$execQuery = pg_query(DBCONNECT, $createQuery);
		
		if ($execQuery) {
			
		   return true;
		}
		
		
		else {
			
			return false;
		}
		
	
	}
	
	function GUID()
		{
			if (function_exists('com_create_guid') === true)
			{
				return trim(com_create_guid(), '{}');
			}

			return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
		}
	
}

   

?>