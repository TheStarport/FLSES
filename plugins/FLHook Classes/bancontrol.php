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

class bancontrol
{
	function kick($that,$settings,$player,$reason,$type="player")
	{#kick <charname> <reason>
		if($type == "player")
		{
			$that->conn->write("kick ".$player." ".$reason."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("kick$ ".$player." ".$reason."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		return $result;
	}
	function ban($that,$settings,$player,$type="player")
	{#ban <charname>
		if($type == "player")
		{
			$that->conn->write("ban ".$player."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("ban$ ".$player."\r\n"); 
			$result = $that->conn->read_till('OK');
		}
		return $result;
	}
	function kickban($that,$settings,$player,$reason,$type="player")
	{#kickban <charname> <reason>
		if($type == "player")
		{
			$that->conn->write("kickban ".$player." ".$reason."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("kickban$ ".$player." ".$reason."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		return $result;
	}
	function unban($that,$settings,$name)
	{#unban <charname>
		$that->conn->write("unban ".$name."\r\n"); 
		$result = $that->conn->read_till('OK'); 
		return $result;
	}
}
?>
