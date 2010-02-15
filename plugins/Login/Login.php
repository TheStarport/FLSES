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
		} else {
			echo "hiho";
			#TODO: Login with name - Not supported yet! And maybe won't be ever.
		}
	}
	
	public function dologout($that,$settings)
	{
		$_SESSION['userid'] = null;
		$_SESSION['passwd'] = null;
		#$that->redirect("","","",cForumAdress);
	}
}
?>