<?php
##################################################################################
##                                                                              ##
##      This File is Part of the FLSES Project, released under GNU GPL v3       ##
##      Copyright © 2009 tai(tai@freelancer-reborn.de)                          ##
##                                                                              ##
##      This program is free software: you can redistribute it and/or modify    ##
##      it under the terms of the GNU General Public License as published by    ##
##      the Free Software Foundation, either version 3 of the License, or       ##
##      (at your option) any later version.                                     ##
##                                                                              ##
##      This program is distributed in the hope that it will be useful,         ##
##      but WITHOUT ANY WARRANTY; without even the implied warranty of          ##
##      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           ##
##      GNU General Public License for more details.                            ##
##                                                                              ##
##      You should have received a copy of the GNU General Public License       ##
##      along with this program.  If not, see <http://www.gnu.org/licenses/>.   ##
##                                                                              ##
##################################################################################
##                                                                              ##
##      FLSES is a Project that aims to help keeping the FL Community alive     ##
##      You can find more information at <http://the-starport.net>              ##
##                                                                              ##
##      Plugins and Future Versions will be released the above website.         ##
##                                                                              ##
##################################################################################

class Pluggable
{
	private $imported;
	private $imported_functions;

	public function setClass($class)
	{
		global $settings;
		$class = mysql_real_escape_string($class);
		$sql = "SELECT url,classname FROM ".$settings['MySQL']['prefix']."plugins WHERE
			class='{$class}' AND active='true'";
		$res = mysql_query($sql) or die(mysql_error());
		if($res)
		{
			while($more = mysql_fetch_object($res))
			{
				$this->imports($more->classname,$more->url);
			}
		} else {
			$this->imported_functions = array();
		}
	}
	
	protected function imports($object,$url)
	{
		global $settings;
		include_once $url;
		if(!$this->imported)
		{
			$this->imported  = array();
			$this->imported_functions = array();
		}
		$dirs = explode("/",$url);
		$dir = "";
		for($i = 0; $i < count($dirs)-1; $i++)
		{
			$dir .= $dirs[$i];
			$dir .= "/";
		}
		$new_import = new $object($this,$settings,$dir);
		$import_name = get_class($new_import);
		$import_functions = get_class_methods($new_import);


		array_push($this->imported, array($import_name, $new_import));

		foreach($import_functions as $key => $function_name)
		{
			$this->imported_functions[$function_name] = &$new_import;
		}
	}

	public function __call($method, $args)
	{
		global $settings;
		if(!$this->imported)
		{
			$this->imported  = array();
			$this->imported_functions = array();
		}
		// make sure the function exists
		if(array_key_exists($method, $this->imported_functions))
		{
			$arguments[] = $this;
			$arguments[] = $settings;
			if($args)
			{
				foreach($args as $arg)
				{
					$arguments[] = $arg;
				}
			}
			// invoke the function
			return call_user_func_array(array($this->imported_functions[$method], $method), $arguments);
		}

		return false;#throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}
?>
