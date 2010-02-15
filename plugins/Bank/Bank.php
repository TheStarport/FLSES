<?php
##################################################################################
##                                                                              ##
##      This File is Part of the FLSES Project, released under GNU GPL v3       ##
##      Copyright © 2009 tai(agent00tai@yahoo.de)                               ##
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
##      You can find more information at http://the-starport.net                ##
##                                                                              ##
##      Plugins and Future Versions will be released at one of the above        ##
##      websites.                                                               ##
##                                                                              ##
##################################################################################

class Bank
{
	var $accounts;
	
	function __construct($that,$settings,$dir)
	{
		new Language($dir);
		$this->dir = $dir;
		$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."bankaccess` 
			WHERE `userid`='".$that->userid."'";
		$res = mysql_query($sql);
		while($row=mysql_fetch_object($res))
		{
			$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."bankaccounts` 
				WHERE `id`='".$row->accountid."'";
			$msql = mysql_query($sql);
			$result = mysql_fetch_object($msql);
			$result->status = $row->status;
			$this->accounts[$result->id] = $result;
		}
	}
	
	public function overview($that,$settings)
	{
		$title = "Bank - Account Overview";

		if($this->accounts)
		{
			foreach ($this->accounts as $account)
			{
				$content = new Template("{$this->dir}/templates/account.html");
				$content->replace("id",$account->id);
				$content->replace("name",$account->name);
				$content->replace("balance",$account->money);
				$content->replace("status",$account->status);
				$con .= $content->output();
			}
		} else {
			$content = new Template("{$this->dir}/templates/noaccount.html");
			$con .= $content->output();
		}
		$template = new Template("{$this->dir}/templates/overview.html");
		$template->replace("content",$con);
		$content = $template->output();
				
		$template = new Template("./templates/main.html");
		$template->replace("title",$title);
		$template->replace("bigtitle","OVERVIEW");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function create($that,$settings)
	{
		$name = $_GET['name'];
		if($name == "")
			$name = "New Account";
		$name = mysql_real_escape_string($name);
		$sql = "INSERT INTO `".$settings['MySQL']['prefix']."bankaccounts` (`name`,`money`) 
			VALUES ('{$name}','0');";
		$msql = mysql_query($sql);
		$sql = "INSERT INTO `".$settings['MySQL']['prefix']."bankaccess`
			(`userid`,`accountid`,`status`) 
			VALUES ('".$that->userid."',LAST_INSERT_ID() ,'owner')";
		$msql = mysql_query($sql);
		$that->redirect("Bank","overview","&message=Account created!");
	}
	
	public function managedel($that,$settings)
	{
		$id = $_GET['id'];
		$account = $this->accounts[$id];
		if($account->status == "owner")
		{
			if($_GET['usrid'])
			{
				$sql = "DELETE FROM `".$settings['MySQL']['prefix']."bankaccess` 
				WHERE `accountid`='".$account->id."' AND 
				`userid`='".$_GET['usrid']."'";
				$res = mysql_query($sql);
				$that->redirect("Bank","manage","&id=".$id.
						"&message=User deleted!");
			}
		}
		
	}
	
	public function manageadd($that,$settings)
	{
		$id = $_GET['id'];
		$account = $this->accounts[$id];
		if($account->status == "owner")
		{
			if($_GET['username'])
			{
				$sql = "INSERT (`accountid`) INTO `".$settings['MySQL']['prefix']."bankaccess` SELECT id FROM `users` WHERE 
					`name`='".$_GET['username']."'";
				$res = mysql_query($sql);
				$that->redirect("Bank","manage","&id=".$id."&message=User added!");
			}
		}
		
	}
	
	public function managepush($that,$settings)
	{
		$id = $_GET['id'];
		$account = $this->accounts[$id];
		if($account)
		{
			if($account->status == "owner")
			{
				$moneyleft = $account->money - $_GET['amount'];
				if($moneyleft >= 0 && $moneyleft <= $account->money)
				{
					if($that->flhook->addcash(html_entity_decode($_GET['player']),
								$_GET['amount']))
					{
						//TODO: Change that Message!
						$that->flhook->msg(html_entity_decode($_GET['player']),"You just recieved ".$_GET['amount']."$ from ".$that->user->name)
						
						//TODO: Add statement handling here!
						
						$sql = "UPDATE `".$settings['MySQL']['prefix']."bankaccounts` SET 
							`money` = '".$moneyleft."' WHERE `id` =14";
						$res = mysql_query($sql);
						$that->redirect("Bank","manage","&id=".$id."&message=Money is on it's way!");
					}
				} else {
					$that->redirect("Bank","manage","&id=".$id."&error=Not enough Money on Account!");
				}
			} else {
				$that->redirect("Bank","manage","&id=".$id."&error=You don't have access to do that!");
			}
		} else {
			$that->redirect("Bank","manage","&id=".$id."&error=You don't have access to do that!");
		}
	}
	
	public function managepull($that,$settings)
	{
		$player = html_entity_decode($_GET['player']);
		if(in_array($player,$that->chars))
		{
			if($flhook->isloggedin($player))
			{
				$cashleft = $flhook->getcash($player) - $_GET['amount'];
				$moneyleft = $account->money + $_GET['amount'];
				if($cashleft >= 0)
				{
					//TODO: Change that Message!
					$that->flhook->msg(html_entity_decode($_GET['player']),"You just recieved ".$_GET['amount']."$ from ".$that->user->name)
					
					$flhook->addcash($player,-$_GET['amount']);
					
					//TODO: Add statement handling here!
					
					$sql = "UPDATE `".$settings['MySQL']['prefix']."bankaccounts` SET `money` = '".$moneyleft."' WHERE `id` =14";
					$res = mysql_query($sql);
				} else {
					$that->redirect("Bank","manage","&id=".$id."&error=You don't have enough cash on your Char!");
				}
			} else {
				$that->redirect("Bank","manage","&id=".$id."&error=Please login first!");
			}
		} else {
			$that->redirect("Bank","manage","&id=".$id."&error=That Char is not connected to your Account!");
		}
	}
	
	public function manage($that,$settings)
	{
		global $flhook;
		
		$id = $_GET['id'];
		$title = "Bank - Manage Account";
		$account = $this->accounts[$id];
		
		if($account)
		{
			$sql = "SELECT * FROM `".$settings['MySQL']['prefix']."bankaccess` WHERE 
				`accountid`='".$account->id."' 
				ORDER BY `status`";
			$res = mysql_query($sql);
			$users = '<tr>';
			$i = 0;
			while($row=mysql_fetch_object($res))
			{
				if($i++ == 3)
				{
					$users .= '</tr><tr>';
					$i = 0;
				}
				$sql = "SELECT name FROM `".$settings['MySQL']['prefix']."users` WHERE 
					`id`='".$row->userid."'";
				$msql = mysql_query($sql);
				$result = mysql_fetch_object($msql);
				if($row->status == "owner")
				{
					$users .= '<td width="33%">';
					$users .= '<a style="color: #FF6600" href="'.cFlsesAdress.'?menue=Bank&submenue=managedel&id='.$account->id.'&usrid='.$row->userid.'">'.$result->name.'</a>';
					$users .= '</td>';
				} else {
					$users .= '<td width="33%">';
					$users .= '<a href="'.cFlsesAdress.'?menue=Bank&submenue=managedel&id='.$account->id.'&usrid='.$row->userid.'">'.$result->name.'</a>';
					$users .= '</td>';
				}
			}
			$users .= '</tr>';
			$template = new Template("{$this->dir}/templates/manage.html");
			$template->replace("name",$account->name);
			$template->replace("balance",$account->money);
			$template->replace("id",$account->id);
			$template->replace("users",$users);
			$cont .= $template->output();
			
		} else {
			$that->redirect("Bank","overview");
		}
				
		$template = new Template("./templates/main.html");
		$template->replace("title",$title);
		$template->replace("message",$error);
		$template->replace("bigtitle","MANAGE ACCOUNT");
		$template->replace("content",$cont);
		print $template->output();
	}
	
	public function sendmoney($that,$settings)
	{
		global $flhook;

		$id = $_GET['id'];
		$title = "Bank - Manage Account - Send Money";
		$account = $this->accounts[$id];

		if($account)
		{
			$users .= '</tr>';
			$template = new Template("{$this->dir}/templates/managesend.html");
			$template->replace("name",$account->name);
			$template->replace("balance",$account->money);
			$template->replace("id",$account->id);
			$template->replace("users",$users);
			$cont .= $template->output();

		} else {
			$that->redirect("Bank","overview");
		}

		$template = new Template("./templates/main.html");
		$template->replace("title",$title);
		$template->replace("message",$error);
		$template->replace("bigtitle","MANAGE ACCOUNT");
		$template->replace("content",$cont);
		print $template->output();
	}
}

?>
