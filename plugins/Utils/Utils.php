<?php

class Utils
{
	public function write_ini_file($that,$settings,$assoc_arr, $path, $has_sections=TRUE)
	{
		$content = "";
		
		if ($has_sections) 
		{
			foreach ($assoc_arr as $key=>$elem)
			{
				$content .= "[".$key."]\n";
				foreach ($elem as $key2=>$elem2)
				{
					if(is_array($elem2))
					{
						for($i=0;$i<count($elem2);$i++)
						{
							$content .= $key2."[] = \"".$elem2[$i]."\"\n";
						}
					}
					else if($elem2=="") $content .= $key2." = \n";
					else $content .= $key2." = \"".$elem2."\"\n";
				}
			}
		} else {
			foreach ($assoc_arr as $key=>$elem)
			{
				if(is_array($elem))
				{
					for($i=0;$i<count($elem);$i++)
					{
						$content .= $key."[] = \"".$elem[$i]."\"\n";
					}
				}
				else if($elem=="") $content .= $key." = \n";
				else $content .= $key." = \"".$elem."\"\n";
			}
		}
		
		if (!$handle = fopen($path, 'w')) {
			return false;
		}
		if (!fwrite($handle, $content)) {
			return false;
		}
		fclose($handle);
		return true;
	}
	
	public function execMySQLFile($that,$settings,$file,$replacements=null,$plugin_dir=null) 
	{
		global $settings;
		#Replace "flses_" and Plugin Specific Expressions in the MySQL
		#Querys with the propper Variables
		$newfile = "";
		foreach( file($file) as $line)
		{
			$newline = str_replace("flses_",$settings['MySQL']['prefix'],$line);
			if($plugin_dir)
				$newline = str_replace("./",$plugin_dir,$newline);
			if($replacements)
			{
				foreach($replacements as $replacement)
				{
					$replace = explode(" - ", $replacement);
					$newline = str_replace($replace[0],eval("return $replace[1];"),$newline);
				}
			}
			$newfile[] = $newline;
		}
		$file = $newfile;
		// import file line by line 
		// and filter (remove) those lines, beginning with an sql comment token 
		$file = array_filter($file, 
							 create_function('$line', 
											 'return strpos(ltrim($line), "--") !== 0;')); 
		// this is a list of SQL commands, which are allowed to follow a semicolon 
		$keywords = array('ALTER', 'CREATE', 'DELETE', 'DROP', 'INSERT', 'REPLACE', 'SELECT', 'SET', 
						  'TRUNCATE', 'UPDATE', 'USE'); 
		// create the regular expression 
		$regexp = sprintf('/\s*;\s*(?=(%s)\b)/s', implode('|', $keywords)); 
		// split there 
		$splitter = preg_split($regexp, implode("\r\n", $file)); 
		// remove trailing semicolon or whitespaces 
		$splitter = array_map(create_function('$line', 
											  'return preg_replace("/[\s;]*$/", "", $line);'), 
							  $splitter); 
		// remove empty lines 
		$queries = array_filter($splitter, create_function('$line', 'return !empty($line);')); 

		$i = 0;
		foreach($queries as $query)
		{
			$i++;
			echo $query;
			mysql_query($query);
		}
		return $i;
	}
	
	public function file_list($that,$settings,$d,$x=""){
		foreach(array_diff(scandir($d),array('.','..')) as $f)if(is_file($d.'/'.$f)&&(($x)?ereg($x.'$',$f):1))$l[]=$f;
		return $l;
	}
	
	public function dir_list($that,$settings,$d)
	{
		foreach(array_diff(scandir($d),array('.','..')) as $f)
			if(is_dir($d.'/'.$f))$l[]=$f;
		return $l;
	}
}
?>