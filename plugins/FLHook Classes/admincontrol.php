<?php
##################################################################################
##										##	
##	This File is Part of the FLSES Project, released under GNU GPL v3	##
##	Copyright © 2009 tai(agent00tai@yahoo.de)				##
##										##
##	This program is free software: you can redistribute it and/or modify	##
##	it under the terms of the GNU General Public License as published by	##
##	the Free Software Foundation, either version 3 of the License, or	##
##	(at your option) any later version.					##
##										##
##	This program is distributed in the hope that it will be useful,		##
##	but WITHOUT ANY WARRANTY; without even the implied warranty of		##
##	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the		##
##	GNU General Public License for more details.				##
##										##
##	You should have received a copy of the GNU General Public License	##
##	along with this program.  If not, see <http://www.gnu.org/licenses/>.	##
##										##
##################################################################################

class admincontrol
{
	function setadmin($that,$settings,$player,$level,$type="player")
	{#setadmin <charname> <rights>
		if($type == "player")
		{
			$that->conn->write("setadmin ".$player." ".$level."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("setadmin$ ".$player." ".$level."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		return $result;
	}
	function getadmin($that,$settings,$player,$type="player")
	{#getadmin <charname>
		if($type == "player")
		{
			$that->conn->write("getadmin ".$player."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("getadmin$ ".$player."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		$result = str_replace("<br>","",$result);
		parse_str($result);
		return $rights;
	}
	function deladmin($that,$settings,$player,$type="player")
	{#deladmin <charname>
		if($type == "player")
		{
			$that->conn->write("setadmin ".$player." ".$level."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("setadmin$ ".$player." ".$level."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		return $result;
	}
}
?>
