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

class Login
{	

	public function __construct($that,$settings,$dir)
	{
		new Language($dir);
		$this->dir = $dir;
	}
	
	/**
	* Log in a user.
	* Can be either with a name or with an ID
	*/
	public function dologin($that,$settings)
	{
		$_SESSION['userid'] = "";
		$_SESSION['passwd'] = "";
	
		$name = mysql_real_escape_string($_GET['name']);
		$id = $_GET['id'];
		$pass = urldecode($_GET['pass']);
		
		if($id)
		{
			$sql = "SELECT password FROM `".$settings['MySQL']['prefix']."users` WHERE `id`='{$id}'";
			$res = mysql_query($sql);
			if(mysql_num_rows($res) == 1)
			{
				$password = mysql_fetch_array($res);
				$password = $password['password'];
				if(md5($pass) == $password)
				{
					$_SESSION['userid'] = $id;
					$_SESSION['passwd'] = $pass;
					$that->redirect('Home','overview');
				} else {
					echo "Password wrong!";
				}
			} else {
				$password = md5($pass);
				$sql = "INSERT INTO `".$settings['MySQL']['prefix']."users` 
						(`id` ,`name` ,`password` ,`access`) 
						VALUES ('{$id}', '{$name}', '{$password}', '');";
				$res = mysql_query($sql);
				$_SESSION['userid'] = $id;
				$_SESSION['passwd'] = $pass;
				$that->redirect('Home','overview','&message=New FLSES Account created!');
			}
		} else if($name){

			$charname = htmlentities($_GET['name']);
			$password= md5(htmlentities($_GET['pass']));

			$sql = mysql_query("SELECT id, name, password FROM `".$settings['MySQL']['prefix']."users` WHERE `name`='{$charname}' AND `password`='{$password}'");
		
			if( mysql_num_rows($sql) == 1)
			{
				while( $res = mysql_fetch_object( $sql ))
				{
					if($password == $res->password)
					{
						$_SESSION['userid'] = $res->id;
						$_SESSION['passwd'] = htmlentities( $_GET['pass']);
						$that->redirect('Home','overview');
					}
					else
					{
						$that->redirect('Login', 'overview', '&error=Bad Login/Password');
					}
				}
			}
			else
			{
				$that->redirect('Login', 'overview', '&error=Bad Login/Password');
			}
			#TODO: Login with name - Not supported yet! And maybe won't be ever.
		}
	}
	
	/**
	* Disconnect a user
	*/
	public function dologout($that,$settings)
	{
		$_SESSION['userid'] = null;
		$_SESSION['passwd'] = null;
		session_destroy();
		$that->redirect('Login','overview','&message=You have been logged out');
	}
	
	/**
	* Default displayed Block. Called if no submenu
	* Display a login form to the user.
	*/
	public function overview($that, $settings)
	{
		$template = new Template("{$this->dir}templates/overview.html");
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title","FLSES - Login");
		$template->replace("bigtitle","Login");
		$template->replace("content",$content);
		print $template->output();
	}
	
	/**
	* Display a registering form to the user. Normally, called if the user clicks on the register link.
	*/
	public function register_form($that,$settings)
	{
		$template = new Template("{$this->dir}templates/register_form.html");
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title","FLSES - Register");
		$template->replace("bigtitle","Register");
		$template->replace("content",$content);
		print $template->output();
	}
	
	/**
	* Register a new user with datas given in a form.
	*/
	public function doregister($that,$settings)
	{
		$pass = md5(htmlentities($_GET['pass']));
		$pass_check = md5(htmlentities($_GET['pass_check']));
		$name = htmlentities($_GET['name']);
		
		$sql = mysql_query("SELECT name FROM `".$settings['MySQL']['prefix']."users` WHERE `name`='{$name}'");
		
		if($pass != $pass_check)
		{
			$that->redirect('Login', 'register_form', '&error=Passwords do not match !');
		}	
		else if(mysql_num_rows($sql) == 1)
		{
			$that->redirect('Login', 'register_form', '&error=Name already registered !');
		}
		else
		{
			$sql = "INSERT INTO `".$settings['MySQL']['prefix']."users` 
						(`id` ,`name` ,`password` ,`access`) 
						VALUES ('', '{$name}', '{$pass}', '');";
			mysql_query($sql);
			$that->redirect('Login', 'overview', '&message=You are now registered. Please log in !');
		}
	}
		
}
?>