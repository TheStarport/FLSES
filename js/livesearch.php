<?php
$settings = parse_ini_file("../config.ini",true);

mysql_connect($settings['MySQL']['server'],$settings['MySQL']['user'],$settings['MySQL']['password']);
mysql_select_db($settings['MySQL']['database']);

$sql = "";
$q = "";
switch($_GET['x'])
{
  case "users":
    $sql = "SELECT * FROM `".$settings['MySQL']['prefix']."users` 
          WHERE `name` LIKE '%";
    break;
  case "chars":
    $sql = "SELECT * FROM `".$settings['MySQL']['prefix']."chars` 
          WHERE `name` LIKE '%";
    break;
  default:
    $sql = "SELECT * FROM `".$settings['MySQL']['prefix']."chars` 
          WHERE `name` LIKE '%";
    break;
}
$q = mysql_real_escape_string($_GET['q']);
$sql .= "{$q}%' LIMIT 20";
$hint = "";
$result = mysql_query($sql);
if($result)
{
        while($char = mysql_fetch_array($result))
        {
                $hint .= '<option>'.$char['name']."</option>";
        }
}

// Set output to "no suggestion" if no hint were found
// or to the correct values
if ($hint == "")
  {
  $response="no suggestion";
  }
else
  {
  $response=$hint;
  }

//output the response
echo $response;
?> 