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

header('Content-Type: text/html; charset=utf-8');  
#ini_set('display_errors','Off'); 
#error_reporting(0);
session_start();

$settings = parse_ini_file("./config.ini",true);

include_once "./classes/templates.php";
include_once "./classes/pluginSystem.php";
include_once "./classes/language.php";
include_once "./classes/flses.php";
include_once "./classes/flhook.php";
mysql_connect($settings['MySQL']['server'],$settings['MySQL']['user'],$settings['MySQL']['password']);
mysql_select_db($settings['MySQL']['database']);

$language = "en";
$flhook = new FLHook();
$flses = new FLSES($flhook,$settings);

if($_GET["menue"])
{
	$main = $_GET["menue"];
	$sub = $_GET["submenue"];
	if(!$sub)
		$sub = "overview";
	$flses->setClass($main);
	$flses->$sub();
} else {
	$flses->setClass("Home");
	$flses->overview();
}
?>
