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

class cashcontrol
{
	function getcash($that,$player,$type="player") #done
	{#getcash <charname>
		if($type == "player")
		{
			$that->conn->write("getcash ".$player."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("getcash$ ".$player."\r\n"); 
			$result = $that->conn->read_till('OK');
		}
		$result = str_replace("<br>","",$result);
		$result = str_replace(" ","&",$result);
		parse_str($result);
		return $cash;
	}
	function setcash($that,$player,$amount,$type="player") #done
	{#setcash <charname> <amount>
		if($type == "player")
		{
			$that->conn->write("setcash ".$player." ".$amount."\r\n"); 
			$result = $that->conn->read_till('OK');
		} else {
			$that->conn->write("setcash$ ".$player." ".$amount."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		$result = str_replace("<br>","",$result);
		$result = str_replace(" ","&",$result);
		parse_str($result);
		if($cash == $amount) { return true; } 
		else { return false; }
	}
	function addcash($that,$player,$amount,$type="player") #done
	{#addcash <charname> <amount>
		if($type == "player")
		{
			$that->conn->write("addcash ".$player." ".$amount."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		} else {
			$that->conn->write("addcash$ ".$player." ".$amount."\r\n"); 
			$result = $that->conn->read_till('OK'); 
		}
		$result = str_replace("<br>","",$result);
		$result = str_replace(" ","&",$result);
		parse_str($result);
		return $cash;
	}
}
?>