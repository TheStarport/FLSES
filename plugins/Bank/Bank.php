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
	
	/**
	* Create a new bank account
	*/
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
	
	/**
	* Delete a bank account
	*/
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
	
	/**
	* Add an user to the current bank account
	*/
	public function manageadd($that,$settings)
	{
		$id = htmlentities($_GET['id']);
		$account = $this->accounts[$id];
		if($account->status == "owner")
		{
			if($_GET['username'])
			{
				$sql = mysql_query("SELECT id FROM `".$settings['MySQL']['prefix']."users` WHERE `name`='".$_GET['username']."'");
				while($res = mysql_fetch_object($sql))
				{
					$add_id = $res->id;
				}
				$req = mysql_query("INSERT INTO `".$settings['MySQL']['prefix']."bankaccess` VALUES ('{$add_id}', '{$id}', 'user')");
				$that->redirect("Bank","manage","&id={$id}&message=User added !");
			}
		}
		
	}
	
	/**
	* Send money to a ingame char
	*/
	public function managepush($that,$settings)
	{
		global $flhook;
		$id = $_GET['id'];
		$account = $this->accounts[$id];
		$player = htmlentities($_GET['player']);
		if($account)
		{
			if($account->status == "owner")
			{
				$moneyleft = $account->money - $_GET['amount'];
				if($moneyleft >= 0 && $moneyleft <= $account->money) // We have to be sure that the user can do that
				{
					if($that->flhook->addcash($player,$_GET['amount'])) // If the operation's state is OK
					{
						if($flhook->isloggedin($_GET['player']))
						{
						$that->flhook->msg($player,"You just received ".$_GET['amount']."$ from ".$account->name);
						}
						
						$sql = "UPDATE `".$settings['MySQL']['prefix']."bankaccounts` SET `money` = '".$moneyleft."' WHERE `id` = {$id}";
						$res = mysql_query($sql);
						mysql_query("INSERT INTO `".$settings['MySQL']['prefix']."bankstatements` VALUES ('{$id}', '', '{$_GET['amount']} $ has been sent to player {$player} by user {$that->user->name}')") or die(mysql_error());
						$that->redirect("Bank","manage","&id=".$id."&message=Money has been sent !");
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
	
	/**
	* Pull money of a ingame char to an account.
	*/
	public function managepull($that,$settings)
	{
		global $flhook;
		$player = htmlentities($_GET['player']);
		$belong = FALSE;
		$id = htmlentities($_GET['id']);
		$account = $this->accounts[$id];
		foreach($that->chars as $char)
		{
			if($char[charname] == $player)
			{
				$belong = TRUE;
			}
		}
		if($belong) // The ingame char MUST belongs to the user
		{
			$cashleft = $flhook->getcash($player) - $_GET['amount'];
			$moneyleft = $account->money + $_GET['amount'];
			
			if($cashleft >= 0)
			{				
				$flhook->addcash($player,-$_GET['amount']);
				
				if($flhook->isloggedin($player))
				{
					$that->flhook->msg(html_entity_decode($_GET['player']),"FLSES Bank : ".$_GET['amount']."$ have been sent to ".$account->name);			 
				}
				
				$sql = "UPDATE `".$settings['MySQL']['prefix']."bankaccounts` SET `money` = '".$moneyleft."' WHERE `id` ={$id}";
				$res = mysql_query($sql);
				mysql_query("INSERT INTO `".$settings['MySQL']['prefix']."bankstatements` VALUES ('{$id}', '', '{$_GET['amount']} $ has been took from player {$player} by user {$that->user->name}')") or die(mysql_error());
				$that->redirect("Bank","manage","&id={$id}&message=More money in your wallet !");
			}
			else {
					$that->redirect("Bank","manage","&id=".$id."&error=You don't have enough cash on your Char!");
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
					$users .= '<a style="color: #FF6600" href="'.$settings['FLSES']['adress'].'?menu=Bank&submenu=managedel&id='.$account->id.'&usrid='.$row->userid.'">'.$result->name.'</a>';
					$users .= '</td>';
				} else {
					$users .= '<td width="33%">';
					$users .= '<a href="'.$settings['FLSES']['adress'].'?menu=Bank&submenu=managedel&id='.$account->id.'&usrid='.$row->userid.'">'.$result->name.'</a>';
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
	
	/**
	* Diplays a form to send money to a char
	*/
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
	
	/**
	* Diplays a form for getting money from a char
	*/
	public function getmoney($that,$settings)
	{
		global $flhook;

		$id = $_GET['id'];
		$title = "Bank - Manage Account - Get Money";
		$account = $this->accounts[$id];

		if($account)
		{
			$users .= '</tr>';
			$template = new Template("{$this->dir}/templates/managetake.html");
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
	
	public function statement($that, $settings)
	{
		$title = "Bank - Manage Account - Get Money";
		$id = $_GET['id'];
		$account = $this->accounts[$id];
		
		if($account)
		{
			if($account->status == "user" || $account->status == "owner")
			{
				$statements = "";
				$req = mysql_query("SELECT statement FROM `".$settings['MySQL']['prefix']."bankstatements` WHERE `id` = {$id}");
				while($res = mysql_fetch_object($req))
				{
					$statements .= $res->statement.'<br/>';
				}
				
				$template = new Template("{$this->dir}/templates/statements.html");
				$template->replace("statements",$statements);
				$template->replace("id",$account->id);
				$cont .= $template->output();
			
				$template = new Template("./templates/main.html");
				$template->replace("title",$title);
				$template->replace("message",$error);
				$template->replace("bigtitle","Account Statements");
				$template->replace("content",$cont);
				print $template->output();
			}
			else
			{
				$that->redirect("Bank", "overview", "&error=You can't do that !");
			}
		}
		else {
			$that->redirect("Bank","overview", "&error=Missing account ID");
		}
	}
	
	public function clearstatements($that,$settings)
	{
		$id = htmlentities($_GET['id']);
		if($id == '')
		{
			$that->redirect("Bank", "overview", "&error=Missing account ID");
		}
		$account = $this->accounts[$id];
		
		if($account->status == "owner")
		{
			mysql_query("DELETE FROM `".$settings['MySQL']['prefix']."bankstatements` WHERE `id` = {$id}");
			$that->redirect("Bank", "manage", "&id={$id}");
		}
		else
		{
			$that->redirect("Bank", "manage", "&id={$id}&error=You can't do that !");
		}
	}
}

?>
