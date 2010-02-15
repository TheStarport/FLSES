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

class Template
{
	var $page;
	var $out;
	var $args;
	
	function __construct($file=null,$arguments=null)
	{
		if($file)
			$this->loadTemplate($file);
		if($arguments)
			if(is_array($arguments))
				$this->args = $arguments;
	}
	
	function __toString() {
		return "";
	}
	
	function loadTemplate($template)
	{
		if (file_exists($template))
			$this->page = join("", file($template));
	}
	
	function replace($tag, $data)
	{
		$this->args[$tag] = $data;
	}
	
	function replaceTags()
	{
		global $language,$lang,$settings;
		
		$this->out = "";
		$array = explode('#',$this->page);
		foreach($array as $item)
		{
			switch($item[0])
			{
				case '&':
					$tpl = new Template(substr($item, 1),$this->args);
					$this->out .= $tpl->output();
					break;
				case '$':
					try{
						$this->out .= $this->args[substr($item,1)];
						break;
					} catch (Exception $e) {
						break;
					}
				case '@':
					$this->out .= $GLOBALS[substr($item,1)];
					break;
				case '%':
					$arr = explode("-",substr($item,1));
					$this->out .= $settings[$arr[0]][$arr[1]];  
					break;
				case '!':
					$arr = explode("-",substr($item,1));
					$this->out .= $lang[$language][$arr[0]][$arr[1]][$arr[2]];
					break;
				case '\'':
					$this->out .= substr($item,1);
					break;
				default:
					$this->out .= $item;
					break;
			}
		}
	}
	
	function output()
	{
		$this->replaceTags();
		return $this->out;
	}
}
?>