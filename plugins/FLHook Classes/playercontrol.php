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

class playercontrol
{
#	function beam($that,$player,$base,$type="player")
#	{#beam <charname> <basename>
#		if($type == "player")
#		{
#			$that->conn->write("beam ".$player." ".$base."\r\n"); 
#			$result = $that->conn->read_till('OK');
#			return $result;
#		} else {
#			$that->conn->write("beam$ ".$player." ".$base."\r\n"); 
#			$result = $that->conn->read_till('OK'); 
#			return $result;
#		}
#	}
	function setrep($that,$player,$faction,$value,$type="player")
	{
		if($type == "player")
			$that->conn->write("setrep ".$player." ".$faction." ".$value."\r\n");
		else
			$that->conn->write("setrep$ ".$player."\r\n");
		$result = $that->conn->read_till('OK');
		echo $result;
		return true;
	}
	
	function savechar($that,$player,$type="player")
	{#savechar <charname>
		if($type == "player")
			$that->conn->write("savechar ".$player."\r\n");
		else
			$that->conn->write("savechar$ ".$player."\r\n");
		$result = $that->conn->read_till('OK');
		return true;
	}
	function rename($that,$player,$newname)
	{#rename <oldcharname> <newcharname>
		$that->conn->write("rename ".$player." ".$newname."\r\n"); 
		$result = $that->conn->read_till('OK'); 
		return $result;
	}
	function getclientid($that,$player)
	{#getclientid <charname>
		$that->conn->write("getclientid ".$player."\r\n"); 
		$result = $that->conn->read_till('OK');
		$result = str_replace("<br>","",$result);
		if(substr($result,1,3) == "ERR")
		{
			return false;
		} else {
			return substr($result,9);
		}
	}
	function getplayerinfo($that,$player,$type="player")
	{#getplayerinfo <charname>
		if($type == "player")
			$that->conn->write("getplayerinfo ".$player."\r\n");
		else
			$that->conn->write("getplayerinfo$ ".$player."\r\n");
		$result = $that->conn->read_till('OK');
		$result = str_replace("<br>","",$result);
		if(substr($result,0,8) == "charname")
		{
			$playerinfo = str_replace(" ","&",$result);
			parse_str($playerinfo);
			$returns = array("name" => $charname, "ip" => $ip, "host" => $host, "system" => $system, "base" => $base, "hookid" => $clientid, "ping" => $ping, "loss" => $loss);
		}
		return $returns;
	}
#	function getaccountdirname($that)
#	{#
#		$that->conn->write("$cmd\r\n"); 
#		$result = $that->conn->read_till('OK'); 
#	}
#	function getcharfilename($that)
#	{#getcharfilename <charname>
#		$that->conn->write("$cmd\r\n"); 
#		$result = $that->conn->read_till('OK'); 
#	}
	function isloggedin($that,$player)
	{#isloggedin <charname>
		$that->conn->write("isloggedin ".$player."\r\n"); 
		$result = $that->conn->read_till('OK');
		$result = str_replace("<br>","",$result);
		parse_str($result);
		if($loggedin == "no")
			return false;
		else
			return true;
	}
	function isonserver($that,$player)
	{#isonserver <charname>
		$that->conn->write("isonserver ".$player."\r\n"); 
		$result = $that->conn->read_till('OK');
		$result = str_replace("<br>","",$result);
		parse_str($result);
		if($onserver == "no")
			return false;
		else
			return true;
	}
}
?>