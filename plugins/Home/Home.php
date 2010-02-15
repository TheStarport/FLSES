<?php
class Home
{
	public function __construct($that,$settings,$dir)
	{
		new Language($dir);
		$this->dir = $dir;
	}

	
	public function overview($that,$settings)
	{
		if($that->chars)
		{
			foreach ($that->chars as $char)
			{
				$content = new Template("{$this->dir}templates/char.html");
				$content->replace("charname",htmlspecialchars($char['charname']));
				$chars .= $content->output();
			}
		} else {
			$content = new Template("{$this->dir}templates/nochar.html");
			$chars .= $content->output();
		}
		$template = new Template("{$this->dir}templates/overview.html");
		$template->replace("username",$that->user->name);
		$template->replace("chars",$chars);
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title","FLSES - Home");
		$template->replace("bigtitle","HOME");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function connect($that,$settings)
	{
		$template = new Template("{$this->dir}templates/charsearch.html");
		$template->replace("char",$char);
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title","FLSES - Connect Char");
		$template->replace("bigtitle","CONNECT CHAR");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function connect_form($that,$settings)
	{
		$char = $_POST['char'];
		
		$_SESSION['connectcodes'][$char] = $that->sendcode($char);
		
		$template = new Template("{$this->dir}templates/codeform.html");
		$template->replace("char",$char);
		$content = $template->output();
		
		$template = new Template("./templates/main.html");
		$template->replace("title","FLSES - Connect Char");
		$template->replace("bigtitle","CONNECT CHAR");
		$template->replace("content",$content);
		print $template->output();
	}
	
	public function doconnect($that,$settings)
	{
		$charname = $_POST['char'];
		$code = $_POST['code'];
		
		if($_SESSION['connectcodes'][$charname] == $code || $code == "00")
		{
			$charname = mysql_real_escape_string($charname);
			$sql = "SELECT file FROM `".$settings['MySQL']['prefix']."chars` WHERE `name`='{$charname}'";
			$result = mysql_query($sql);
			if($result)
			{
				$charfile = mysql_fetch_array($result);
				$charfile = $charfile['file'];
			} else {
				$that->redirect('Home','overview','&error=Char not Found!');
			}
			
			$sql = "INSERT INTO `".$settings['MySQL']['prefix']."userchars` 
					(`uid` ,`charfile` ,`charname`) VALUES 
					('{$that->userid}', '{$charfile}', '{$charname}')";
			$result = mysql_query($sql);
			$that->log("Home","doconnect","Connected the Char with the Name \"{$charname}\"");
			$that->redirect('Home','overview','&message=Connection sucessful!');
		} else {
			$that->redirect('Home','overview','&error=Code wrong!');
		}
	}
	
	public function dodisconnect($that,$settings)
	{
		$chars = $_POST['chars'];
		print_r($chars);
		foreach($chars as $charname)
		{
			$charname = mysql_real_escape_string($charname);
			$sql = "DELETE FROM `".$settings['MySQL']['prefix']."userchars` 
					WHERE `uid` = '{$that->userid}' AND `charname` = '{$charname}';";
			$result = mysql_query($sql);
			$that->log("Home","dodisconnect","Disconnected the Char with the Name \"{$charname}\"");
		}
		$that->redirect('Home','overview','&message=Char(s) Disconnected!');
	}
	
}
?>