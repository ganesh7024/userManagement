<?php

require_once ('././config/dbHandler.php');

class analysisManager
{

 public $u_file;
 public $layer_name;
   
 function uploadData()
    {

    
   
	$filename = substr($this->u_file['name'], 0, -4);

    $path = ROOTDIR . "/analysis_tools/uploads/";

    $uni_name = $filename . '_' . uniqid();
    $uni_name = strtolower($uni_name);
    $uni_name = str_replace(' ', '_', $uni_name);

    mkdir($path . $uni_name, 0777);

    $move_location = $path . $uni_name . "/" . $uni_name . '.zip';

    move_uploaded_file($this->u_file['tmp_name'], $move_location);

    $this->unzip_file($move_location, $path . $uni_name . "/");

    $folderPath = $path . $uni_name . "/";

    $shp_location = glob($folderPath . "*.shp");

    $shx_location = glob($folderPath . "*.shx");
    $dbf_location = glob($folderPath . "*.dbf");
    $prj_location = glob($folderPath . "*.prj");

    if ($shp_location && $shx_location && $dbf_location && $prj_location)
    {

        $cmd = '"C:\Program Files\PostgreSQL\10\bin\shp2pgsql" -s 32643 -c "' . $shp_location[0] . '" ' . $uni_name;

        $queries = shell_exec($cmd);

        $insert_to_postgis = pg_query(DBCONNECT, $queries);
        
		$this->addLayerIndex($uni_name, $this->layer_name);
		
	     return array(
			    "status" => true,
                "upload_message" => "$filename is valid GIS Data and upload sucessfull",
                "u_name" => $uni_name
            );
    }
	
	else {
		
		  return array(
			    "status" => false,
                "upload_message" => "Invalid Data- add all necessary files "
                
            );
		
	}

    }
	
	function unzip_file($file, $destination){
		$zip = new ZipArchive() ;
		if ($zip->open($file) !== TRUE) {
			return false;
		}
		$zip->extractTo($destination);
     	$zip->close();
        return true;
	}
	
	
	function addLayerIndex($tableName, $layerName){
		 $uid = $this->GUID();
		$addLayer = pg_query(DBCONNECT, "insert into layers (id, layer_name, table_name) values ('$uid', '$layerName', '$tableName')");
	}
	
    function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid() , '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535) , mt_rand(0, 65535) , mt_rand(0, 65535) , mt_rand(16384, 20479) , mt_rand(32768, 49151) , mt_rand(0, 65535) , mt_rand(0, 65535) , mt_rand(0, 65535));
    }

    
}

?>
