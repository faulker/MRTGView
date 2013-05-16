<?php

class MRTGParser
{
	// Search an array using regex.
	private function preg_array_search($find, $in_array, $keys_found=Array()) 
	{ 
		if( is_array( $in_array ) ) 
		{ 
			foreach( $in_array as $key=> $val ) 
			{ 
				if( is_array( $val ) ) $this->preg_array_search( $find, $val, $keys_found ); 
				else 
				{ 
						if( preg_match( '/'. $find .'/', $val ) ) $keys_found[] = $key; 
				} 
			} 
			return $keys_found; 
		} 
		return false; 
	}
	
	// Go through an array and trim white space from each value.
	private function trim_array($array)
	{
		foreach($array as $i)
		{
			$new_array[] = trim($i);
		}
		return $new_array;
	}
	
	
	/*
	This function has three options
		0 - returns only directorys in the $dir
		1 - returns only files in the $dir
		2 - returns both directorys and files
	*****/
	private function list_dir_contents($dir, $opt)
	{
		$list = array();
		if (is_dir($dir))
		{
			if($dh = opendir($dir))
			{
				while (($file = readdir($dh)) !== false)
				{
					if($file != "." && $file != "..")
					{
						switch ($opt)
						{
							case 0:
								if (is_dir($dir. "/" . $file)) { $list[] = $file; }
								break;
							case 1:
								if (!is_dir($dir . "/". $file)) { $list[] = $file; }
								break;
							case 2:
								$list[] = $file;
								break;
						}
					}
				}
				closedir($dh);
			}		
		}
		return $list;
	}

	// Using a regular expresion, search an array to find a key based on the value.
	private function preg_value_to_key($array, $reg)
	{
		foreach($array as $i)
		{
			$obj = preg_split($reg, $i,  0, PREG_SPLIT_NO_EMPTY);
			$key = trim($obj[0]);
			
			if(count($obj) > 1)
			{
				$value = trim($obj[1]);
				$new_array[$key] = $value;
			}
			else
			{
				$new_array[$obj[0]] = "";
			}
		}
		return $new_array;
	}
	
	// Parses an MRTG html file to pull useful information out of it
	private function parse_mrtg_html($file)
	{
		$parsed_array = null;
		if(file_exists($file))
		{
			$handler = fopen($file, "r");
			if($handler)
			{
				$full_buffer = "";
				while(($buffer = fgetss($handler, 4096)) !== false)
				{
					$buffer = trim($buffer);
					if($buffer != '')
					{
						$full_buffer[] = $buffer;
					}
				}
				if(!feof($handler))
				{
					echo "Error: unexpected fgetss() fail\n";
				}
				fclose($handler);
				
				/*
				 * Get interface descriptive information
				 */
				$key = $this->preg_array_search('Description:', $full_buffer);
				$raw_array = preg_split('/\s{2,}/', $full_buffer[$key[0]], 0, PREG_SPLIT_NO_EMPTY);
				$trimed_array = $this->trim_array($raw_array);
				$parsed_array = $this->preg_value_to_key($trimed_array, '/:$|: |--/');
			}
		}
		return $parsed_array;
	}
	
	
	/*
		OUTPUT:
		Array(
			datetime => Y-m-d H:i:s,
			updated => bool
		)
	*/
	private function check_graph_updated($img_path)
	{
		$updated = array();
		$img_date = date("Ymd", filectime($img_path));
		$img_time = (date("Hi", filectime($img_path))+$this->warning_time);
		$updated['datetime'] = date("Y-m-d H:i:s", filectime($img_path));

		if(date("Ymd") > $img_date || date("Hi") > $img_time)
		{
			$updated['updated'] = false;
		}
		else
		{
			$updated['updated'] = true;
		}
		
		return $updated;
	}
	
	/***************************************************************
	 *
	 * PUBLIC Functions
	 */
	
	public $mrtg_path = "./mrtg/"; // Path to the mrtg folder
	
	/*
	When a graph is loaded it will check to see if it has been updated in this many minutes,
	if not than it adds a warning to the graph.
	*/
	public $warning_time = 30;
	
	
	/*
		Outputs a list of all devices that have a folder in the MRTG folder.
		
		Output:
			Array
			(
				[device name] => Array
				(
					[name] => text
					[ip] => text IP
					[path] => path to device folder
				)
			)
	*/
	public function get_device_list()
	{
		$dir_contents = $this->list_dir_contents($this->mrtg_path, 0);
		$device_list = array();
		
		// This for loops though all dir returned from $dir
		foreach($dir_contents as $file)
		{
			if ($file !== '.' && $file !== '..' && strpos($file, '_'))
			{
				list($ip, $name) = explode("_", $file); // Splits the DIR name into two sections IP address and Name
				$device_list[$name]['name'] = $name;
				$device_list[$name]['ip'] = $ip;
				$device_list[$name]['path'] = $this->mrtg_path . "/" . $file;
			}
		}
		return $device_list;
	}
	
	/*
		$get_by_name:
			false/null = Get the files inside the provided $dir
			true = Search the $mrtg_path for a directory that hase the $device_name in it and get the files inside it.
		
		$device
			If $get_by_name is true then this should be a device name.
			If $get_by_name is false then this should be the path to a device folder.
	 
		Output:
			[device name] => Array
			(
				[name] => text
				[ip] => text ip
				[1] => Array
				(
					[graphs] => Array
					(
						[day] => Array
						[month] => Array
						[week] => Array
						[year] => Array
						(
							[path] => graph path
							[info] => Array
								(
									[datetime] => date/time
									[updated] => bool
								)

						)
					)

					[url] => ./mrtg//10.1.2.5_NET430/10.1.2.5_1.html
					[intName] => Gi0/0
					[description] => GigabitEthernet0/0 $ETH-LAN$
					[type] => ethernetCsmacd (6)
					[maxSpeed] => 1000.0 Mbits/s
					[intIP] => 10.1.2.5
				)
			)
	 */
	public function get_device_info($get_by_name, $device)
	{
		$device_info = array();
		
		// Search the MRTG folder for the device name and the data from the first folder that matches.
		if($get_by_name)
		{
			$contents = $this->list_dir_contents($this->mrtg_path, 0);
			foreach($contents as $folder)
			{
				if(stripos($folder, $device))
				{
					$path = $this->mrtg_path . "/" . $folder;
					$dir_contents = $this->list_dir_contents($path, 1);
					$device_ip = preg_split("/_/", $folder);
					$device_ip = $device_ip[0];
					$device_name = $device;
				}
			}
		}
		// Get the data from the folder that matches the path.
		elseif(!$get_by_name)
		{
			$path = $device;
			$dir_contents = $this->list_dir_contents($path, 1);
			
			$path_array = preg_split("/\//", $path);
			$path_size = count($path_array);
			$dir_array = preg_split("/_/", $path_array[$path_size-1]);
			$device_name = $dir_array[1];
			$device_ip = $dir_array[0];
		}
		
		if(isset($dir_contents))
		{
			$device_info[$device_name]['name'] = $device_name;
			$device_info[$device_name]['ip'] = $device_ip;
			
			foreach($dir_contents as $file)
			{
				$dev_path = $path . "/" . $file;
				if(stripos($file, ".html"))
				{
					$int_id = preg_split("/_/", preg_replace("/\.html/", "", $file));
					$int_id = $int_id[1];
					$int_info = $this->parse_mrtg_html($dev_path);
					
					$device_info[$device_name][$int_id]['url'] = $path."/".$file;
					
					if(array_key_exists("ifName", $int_info))
					{
						$device_info[$device_name][$int_id]['intName'] = $int_info['ifName'];
					}
					if(array_key_exists("Description", $int_info))
					{
						$device_info[$device_name][$int_id]['description'] = $int_info['Description'];
					}
					if(array_key_exists("ifType", $int_info))
					{
						$device_info[$device_name][$int_id]['type'] = $int_info['ifType'];
					}
					if(array_key_exists("Max Speed", $int_info))
					{
						$device_info[$device_name][$int_id]['maxSpeed'] = $int_info['Max Speed'];
					}
					if(array_key_exists("Ip", $int_info))
					{
						$dev_ip = preg_split("/ /", $int_info['Ip']);
						$device_info[$device_name][$int_id]['intIP'] = $dev_ip[0];
						
					}
				}
				elseif(stripos($file, ".png"))
				{
					$int_id = preg_split("/_/", preg_replace("/-(day|week|month|year)\.png/", "", $file));
					$int_id = $int_id[1];
					if(stripos($file, "-day"))
					{
						$device_info[$device_name][$int_id]['graphs']['day']['path'] = $dev_path;
						$device_info[$device_name][$int_id]['graphs']['day']['info'] = $this->check_graph_updated($dev_path);
					}
					elseif(stripos($file, "-week"))
					{
						$device_info[$device_name][$int_id]['graphs']['week']['path'] = $dev_path;
						$device_info[$device_name][$int_id]['graphs']['week']['info'] = $this->check_graph_updated($dev_path);
					}
					elseif(stripos($file, "-month"))
					{
						$device_info[$device_name][$int_id]['graphs']['month']['path'] = $dev_path;
						$device_info[$device_name][$int_id]['graphs']['month']['info'] = $this->check_graph_updated($dev_path);
					}
					elseif(stripos($file, "-year"))
					{
						$device_info[$device_name][$int_id]['graphs']['year']['path'] = $dev_path;
						$device_info[$device_name][$int_id]['graphs']['year']['info'] = $this->check_graph_updated($dev_path);
					}
				}
			}
		}
		else
		{
			echo "Error, no data returned\n";
		}
		
		return $device_info;
	}
	
	
	public function all_devices_info()
	{
		$devices = array();
		$device_list = $this->get_device_list();
		foreach($device_list as $dev)
		{
			$dev_info = $this->get_device_info(false, $dev['path']);
			$devices = array_merge($devices, $dev_info);
		}
		
		return $devices;
	}
}
?>