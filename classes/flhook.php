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

include_once "./classes/pluginSystem.php";
include_once "./classes/telnet.class.php";

class FLHook extends Pluggable
{
	public $conn;
	
	function connect($settings)
	{
		$this->conn = new telnet($settings['FLHook']['host'],$settings['FLHook']['port']);
		
		if ( $this->conn->sock ) {
			$this->conn->sethtml(true);
			$this->conn->read_till('Welcome to FLHack, please authenticate');
			$this->conn->write("pass ".$settings['FLHook']['password']."\r\n");
			$this->conn->read_till('OK');
			$this->setClass('flhook');
			return true;
		} else {
			exit("FLHook or Server down!");
		}
	}
}

?>