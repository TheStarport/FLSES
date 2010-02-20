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

class messagingcontrol
{
	function msg($that,$settings,$player,$text,$type="player")
	{
		if($type == "player")
		{
			$that->conn->write("msg ".$player." ".$text."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return $result;
		} else {
			$that->conn->write("msg$ ".$player." ".$text."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return $result;
		}
	}
	function fmsg($that,$settings,$player,$text,$type="player")
	{
		if($type == "player")
		{
			$that->conn->write("fmsg ".$player." ".$text."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return $result;
		} else {
			$that->conn->write("fmsg$ ".$player." ".$text."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return $result;
		}
	}
	function msgu($that,$settings,$text)
	{
		$that->conn->write("msgu ".$text."\r\n"); 
		$result = $that->conn->read_till('OK');
		return $result;
	}
	function fmsgu($that,$settings,$text)
	{
		$that->conn->write("fmsgu ".$text."\r\n"); 
		$result = $that->conn->read_till('OK'); 
		return $result;
	}
}
?>
