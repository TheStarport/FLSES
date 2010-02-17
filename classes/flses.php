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
##      Plugins and Future Versions will be released the above websites.        ##
##                                                                              ##
##################################################################################

class FLSES extends Pluggable
{
	public $chars = array();
	public $userid = 0;
	
	public function __construct($hook,$settings)
	{
		if( isset( $_GET['message']) )
		{
			$this->generatemessage($_GET['message'],"green");
		}
		if( isset($_GET['error']) )
		{
			$this->generatemessage($_GET['error'],"red");
		}
		$this->checkuser($hook,$settings);
	}
	
	public function checkuser($hook,$settings)
	{
		if( isset($_SESSION['userid']) )
		{
			if( $_SESSION['userid'] != 0 && $_SESSION['passwd'] != "")
			{
				$id = $_SESSION['userid'];
				$pass = md5($_SESSION['passwd']);
				$sql = "SELECT id FROM `".$settings['MySQL']['prefix']."users` WHERE `id`='{$id}' AND `password`='{$pass}';";
				$res = mysql_query($sql);
				if($res)
				{
					$result = mysql_fetch_array($res);
					if($result['id'] == $_SESSION['userid'])
					{
						$this->userid = $result['id'];
						$this->user = $this->parseuserdata($this->userid,$settings);
						$this->chars = $this->getuserchars($this->userid,$settings);
						$this->active_plugins = parse_ini_file("./plugins/activated.ini",false);
						$GLOBALS['menu'] = $this->generatemenu($_GET['menu'],$_GET['submenu'],$settings);
						$this->flhook = $hook;
						$this->flhook->connect($settings);
					} else {
						if(!$_GET['menu'] == "Login")
						{
							$this->redirect("","","",$settings['FLSES']['forumadress']);
							exit;
						}
					}
				} else {
					if(!$_GET['menu'] == "Login")
					{
						$this->redirect("","","",$settings['FLSES']['forumadress']);
						exit;
					}
				}
			}
		} else {
			if(!$_GET['menu'] == "Login")
			{
				$this->redirect("","","",$settings['FLSES']['forumadress']);
				exit;
			}
		}
	}
	
	public function generatemenu($main,$sub,$settings)
	{
		if($main == "")
			$main = "Home";
		if($sub == "")
			$sub = "overview";
		$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."menu` ORDER BY `order`;";
		$res = mysql_query($sql);
		if($res)
		{
			while($item = mysql_fetch_array($res))
			{
				$access = explode(";",$item['access']);
				$grant_access = true;
				foreach($access as $access_item)
				{
					if($access_item != "")
					{
						$arr = explode("=",$access_item);
						if($arr[0] != "" && $arr[1] != "")
						{
							if($this->user->access[$arr[0]] < $arr[1])
							{
								$grant_access = false;
							}
						}
					}
				}
				if($grant_access)
				{
					if($item['parent'])
					{
						$subs[] = $item;
					} else {
						$mains[] = $item;
					}
				}
			}
			if($mains)
			{
				foreach($mains as $item)
				{
					if($item['name'] == $main)
					{
						$template = new Template("./templates/menuitem.html");
						$template->replace("menu",$item['class']);
						$template->replace("submenu",$item['function']);
						$template->replace("img",$item['img_on']);
						$template->replace("height","60");
						$main_html .= $template->output();
					} else {
						$template = new Template("./templates/menuitem.html");
						$template->replace("menu",$item['class']);
						$template->replace("submenu",$item['function']);
						$template->replace("img",$item['img']);
						$template->replace("height","60");
						$main_html .= $template->output();
					}
				}
			}
			if($subs)
			{
				foreach($subs as $item)
				{
					if($item['parent'] == $main)
					{
						if($item['function'] == $sub)
						{
							$template = new Template("./templates/menuitem.html");
							$template->replace("menu",$item['class']);
							$template->replace("submenu",$item['function']);
							$template->replace("img",$item['img_on']);
							$template->replace("height","40");
							$sub_html .= $template->output();
						} else {
							$template = new Template("./templates/menuitem.html");
							$template->replace("menu",$item['class']);
							$template->replace("submenu",$item['function']);
							$template->replace("img",$item['img']);
							$template->replace("height","40");
							$sub_html .= $template->output();
						}
					}
				}
			}
			
			$template = new Template("./templates/menu.html");
			$template->replace("mainmenu",$main_html);
			$template->replace("submenu",$sub_html);
			return $template->output();
		}
	}
	
	public function generatemessage($message,$colour)
	{
		$template = new Template("./templates/message.html");
		$template->replace("message",$message);
		if($colour == "green")
		{
			$template->replace("colour","green");
			#$template->replace("colour2","#FF0000");
		} elseif($colour == "red") {
			$template->replace("colour","red");
			#$template->replace("colour2","#FF0000");
		}
		$GLOBALS['message'] = $template->output();
	}
	
	public function parseuserdata($id,$settings)
	{
		$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."users` WHERE `id`='{$id}';";
		$res = mysql_query($sql);
		if($res)
		{
			$user = mysql_fetch_object($res);
			$access = explode(";",$user->access);
			$user->access = array();
			foreach($access as $item)
			{
				$arr = explode("=",$item);
				if($arr[0] != "" && $arr[1] != "")
					$user->access[$arr[0]] = $arr[1];
			}
			return $user;
		}
	}
	
	public function getuserchars($userid,$settings)
	{
		$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."userchars` WHERE `uid`='{$userid}';";
		$res = mysql_query($sql);
		if($res)
		{
			while($char = mysql_fetch_array($res))
			{
				$chars[$char['charname']] = $char;
			}
		} else {
			return $chars = array();
		}
		return $chars;
	}
	
	## Return the users access for an specific area
	public function access($area)
	{
		return $this->user->access[$area];
	}
	
	public function setaccess($area,$val,$uid="")
	{
		if(!$uid)
		{	
			$uid = $this->userid;
			$user = $this->user;
		}
		else
		{
			$user = $this->parseuserdata($uid);
		}
		$length = count($user->access);
		$i = 0;
		foreach($user->access as $key => $value)
		{
			$i++;
			if($key == $area)
			{
				$string .= $key;
				$string .= "=";
				$string .= $val;
				if($i < $length)
					$string .= ";";
				$done = true;
			} else {
				$string .= $key;
				$string .= "=";
				$string .= $value;
				if($i < $length)
					$string .= ";";
			}
		}
		if(!$done)
		{
			$string .= ";";
			$string .= $arg;
			$string .= "=";
			$string .= $val;
		}
		
		$sql = "UPDATE `".$settings['MySQL']['prefix']."users` 
			SET `access` = '{$string}' 
			WHERE `id` = '{$uid}';";
		mysql_query($sql);
	}
	
	## Global Function Area ##
	
	public function log($plugin,$action,$message)
	{
		global $settings;
		$user = $this->userid;
		$sql = "INSERT INTO `".$settings['MySQL']['prefix']."logs`
			(`user` ,`plugin` ,`action` ,`message`) VALUES ";
		$sql .= "('{$user}','{$plugin}','{$action}','{$message}');";
		print $sql;
		mysql_query($sql);
	}
	
	public function sendCode($player)
	{
		global $flhook;
		$code = "";
		$arrDataPool = array("0","1","2","3","4","5","6","7","8","9","A","B",
					"C","D","E","F","G","H","I","J","K","L","M",
					"N","O","P","Q","R","S","T","U","V","W","X",
					"Y","Z");
		for($i=0; $i<5; $i++)
		{
			$rand = rand(0,35);
			$code .= $arrDataPool[$rand];
		}
		$flhook->fmsg($player,"player",
				"<TRA data='0x16B20E01' mask='-1'/><TEXT>
				FLSES Access Code: </TEXT><TRA data='0xDFD58001' 
				mask='-1'/><TEXT>".$code."</TEXT>");
		$flhook->fmsg($player,"player",
				"<TRA data='0x16B20E00' mask='-1'/><TEXT>
				Enter this code into the 
				form in the Web interface!</TEXT>");
		$flhook->fmsg($player,"player",
				"<TRA data='0x16B20E00' mask='-1'/><TEXT>
				Do not give this code to other
		 		players as it will lead to abuse</TEXT>");
		$flhook->fmsg($player,"player",
				"<TRA data='0x16B20E00' mask='-1'/><TEXT>
				This code was generated by </TEXT>
				<TRA data='0x08A4FF00' mask='-1'/><TEXT>
				".$_SESSION["flses_name"]."</TEXT>");

		return($code);
	}
	
	function redirect($menu="",$submenu="",$string="",$url="")
	{
		global $settings;
		if(!$url)
		{
			$url = $settings['FLSES']['adress'].'?menu='.$menu.'&submenu='.$submenu.$string;
		}
		echo '	<html>
			<head>
			<meta http-equiv="refresh" content="0; URL='.$url.'">
			</head>
			</html>';
	}
}

?>