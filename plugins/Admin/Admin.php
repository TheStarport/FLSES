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
##      You can find more information at  http://the-starport.net               ##
##                                                                              ##
##      Plugins and Future Versions will be released at the above website.      ##
##                                                                              ##
##################################################################################

class Admin extends Pluggable
{
	function __construct($that,$settings,$dir)
	{
		new Language($dir);
		$this->dir = $dir;
		if($that->user->access['admin'] < 1)
		{
			$that->redirect("Home","overview");
		}
		#Import our Utils for File Actions
		$this->setClass("Utils");
	}

	public function overview($that,$settings)
	{
		$template = new Template("{$this->dir}templates/overview.html");
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title",$title);
		$template->replace("bigtitle","OVERVIEW");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function plugins($that,$settings)
	{
		#The Access Level has to be greater than 10 to access Plugin Management.
		if($that->user->access['admin'] < 10)
		{
			$that->redirect("Home","overview");
		}
		
		foreach($this->dir_list("./plugins") as $dir)
		{
			$files = $this->file_list("./plugins/".$dir);
			if(in_array("plugin.ini",$files))
			{
				$plugindirs[$dir] = $files;
				$plugins[$dir] = parse_ini_file("./plugins/{$dir}/plugin.ini",true);
			}
		}
		foreach($plugins as $plugin)
		{
			$requires = "";
			foreach($plugin['Requirements'] as $requirement => $version)
			{
				if($that->active_plugins[$requirement] >= $version)
				{
					continue;
				} else {
					$requires .= "This Plugin Requires {$requirement} in Version {$version}!";
				}
			}
			$name = str_replace(" ","-",$plugin['Plugin']['name']);
			$out .= "<tr align=\"center\">";
			$out .= "<td align=\"left\"><u>".$plugin['Plugin']['name']."</u><br/>".$plugin['Plugin']['desc'];
			if($requires != "")
				$out .= "<br/><span style=\"color:red;\">".$requires."</span>";
			$out .= "</td>";
			$out .= "<td>".$plugin['Plugin']['version']."</td>";
			$out .= "<td><a href=\"".$plugin['Plugin']['website']."\">".$plugin['Plugin']['author']."</a></td>";
			if(isset($that->active_plugins[$name]))
			{
				$out .= "<td style=\"color:green;\"> activated </td>";
				$out .= "<td><a href=\"?menue=Admin&submenue=dounplug&plugin=".$plugin['Plugin']['name']."\"> Deactivate! </a></td>";
			} elseif($requires != "") {
				$out .= "<td colspan=\"2\" style=\"color:red;\"> Requirements not fulfilled! </td>";
			} else {
				$out .= "<td style=\"color:red;\"> not activated </td>";
				$out .= "<td><a href=\"?menue=Admin&submenue=doplug&plugin=".$plugin['Plugin']['name']."\"> Activate! </a></td>";
			}
			$out .= "</tr>";
			$out .= "<tr>";
			$out .= "<td colspan=\"5\"><hr></td>";
			$out .= "</tr>";
		}
		
		$template = new Template("{$this->dir}templates/plugins.html");
		$template->replace("plugins",$out);
		$content = $template->output();

		$template = new Template("./templates/main.html");
		$template->replace("title",$title);
		$template->replace("bigtitle","OVERVIEW");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function doplug($that,$settings)
	{
		#The Access Level has to be greater than 10 to access Plugin Activation.
		if($that->user->access['admin'] < 10)
		{
			$that->redirect("Home","overview");
		}
		
		$plugin = $_GET['plugin'];
		$plugin_fixed = str_replace(" ","-",$plugin);
		$activated = $that->active_plugins;
		$plugin_found = false;
		foreach($this->dir_list("./plugins") as $dir)
		{
			$files = $this->file_list("./plugins/".$dir);
			if(in_array("plugin.ini",$files))
			{
				$data = parse_ini_file("./plugins/{$dir}/plugin.ini",true);
				if($data['Plugin']['name'] == $plugin)
				{
					$plugin_dir = "./plugins/{$dir}/";
					$plugin_found = true;
					break;
				} else {
					$plugin_found = false;
				}
			}
		}
		if($plugin_found)
		{
			#Insert the Database Settings from an SQL File
			if($data['DB']['on-activation'])
			{
				$activ_file = str_replace("./",$plugin_dir,$data['DB']['on-activation']);
				$this->execMySQLFile($activ_file,$data['DB']['replacements'],$plugin_dir);
			}
			#Set the Plugin as activated
			$activated[$plugin_fixed] = $data['Plugin']['version'];
			$this->write_ini_file($activated,"./plugins/activated.ini",FALSE);
			#Bring the Admin back to Plugin Overview
			$that->log("Admin","doplug","Activated the \"{$plugin}\" Plugin");
			#$that->redirect("Admin","plugins");
		}
	}
	
	public function dounplug($that,$settings)
	{
		#The Access Level has to be greater than 10 to access Plugin Deactivation.
		if($that->user->access['admin'] < 10)
		{
			$that->redirect("Home","overview");
		}
		$plugin = $_GET['plugin'];
		$plugin_fixed = str_replace(" ","-",$plugin);
		$activated = $that->active_plugins;
		$plugin_found = false;
		foreach($this->dir_list("./plugins") as $dir)
		{
			$files = $this->file_list("./plugins/".$dir);
			if(in_array("plugin.ini",$files))
			{
				$data = parse_ini_file("./plugins/{$dir}/plugin.ini",true);
				if($data['Plugin']['name'] == $plugin)
				{
					$plugin_dir = "./plugins/{$dir}/";
					$plugin_found = true;
					break;
				} else {
					$plugin_found = false;
				}
			}
		}
		if($plugin_found)
		{
			#Insert the Database Settings from an SQL File
			if($data['DB']['on-deactivation'])
			{
				$deactiv_file = str_replace("./",$plugin_dir,$data['DB']['on-deactivation']);
				$this->execMySQLFile($deactiv_file,$data['DB']['replacements'],$plugin_dir);
			}
			#Set the Plugin as not activated
			unset($activated[$plugin_fixed]);
			$this->write_ini_file($activated,"./plugins/activated.ini",FALSE);
			#Bring the Admin back to Plugin Overview
			$that->log("Admin","dounplug","Deactivated the \"{$plugin}\" Plugin");
			#$that->redirect("Admin","plugins");
		}
	}
}
?>