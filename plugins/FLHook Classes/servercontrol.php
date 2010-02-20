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

class servercontrol
{
	function getplayers($that,$settings)
	{#getplayers
		$that->conn->write("getplayers\r\n"); 
		$playerinfos = $that->conn->read_till('OK');
		$arrPlayerinfo = explode("<br>",$playerinfos);
		$result = array();
		foreach($arrPlayerinfo as $playerinfo)
		{
			if(substr($playerinfo,0,8) == "charname")
			{
				$playerinfo = str_replace(" ","&",$playerinfo);
				parse_str($playerinfo);
				array_push($result, array("name" => $charname, "ip" => $ip, "host" => $host, "system" => $system, "base" => $base, "hookid" => $clientid, "ping" => $ping, "loss" => $loss));
			}
		}
		return $result;
	}
	
	function getplayersinselect($that,$settings,$status=null)
	{
		foreach($that->getplayers() as $user)
		{
			if($status)
			{
				if($status == "base")
				{
					if($base != "")
					{
						$users .= "<option value=\"".$user["name"].";&;".htmlentities(stripslashes($user["name"]))."\">".htmlentities(stripslashes($user["name"]))."</option>";
					}
				} else {
					if($system != "")
					{
						$users .= "<option value=\"".htmlentities(stripslashes($user["name"]))."\">".htmlentities(stripslashes($user["name"]))."</option>";
					}
				}
			} else {
				$users .= "<option value=\"".htmlentities(stripslashes($user["name"]))."\">".htmlentities(stripslashes($user["name"]))."</option>";
			}
		}
		return $users;
	}
	
	function getplayerids($that,$settings)
	{#getplayerids
		$that->conn->write("getplayerids\r\n"); 
		$result = $that->conn->read_till('OK');
		$result = explode(" | ",str_replace("<br>","",$result));
		$ids = array();
		foreach($result as $id)
		{
			$arr = explode("=",$id);
			$ids[$arr[0]] = $arr[1];
		}
		return $ids;
	}
	#function moneyfixlist($that)					#	???????
	#{#moneyfixlist						#	???????
	#	$that->conn->write("$cmd\r\n"); 		#	???????
	#	$result = $that->conn->read_till('OK'); 	#	???????
	#}
	function serverinfo($that,$settings)
	{#serverinfo
		$that->conn->write("serverinfo\r\n"); 
		$result = $that->conn->read_till('OK');
		$result = str_replace("<br>","",$result);
		$result = str_replace(" ","&",$result);
		parse_str($result);
		$returns = array("serverload" => $serverload,
					"npcspawn" => $npcspawn,
					"uptime" => $uptime);
		return $returns;
	}
	#function readcharfile($that)
	#{#readcharfile <charname>
	#	$that->conn->write("$cmd\r\n"); 
	#	$result = $that->conn->read_till('OK'); 
	#}
	#function readfromfile($that)
	#{
	#	$that->conn->write("$cmd\r\n"); 
	#	$result = $that->conn->read_till('OK'); 
	#}
}
?>
