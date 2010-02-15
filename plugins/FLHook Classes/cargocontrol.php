<?php
#TODO: Add Header here!

class cargocontrol
{
	function enumcargo($that,$settings,$player,$type="player")
	{#enumcargo <charname>
		if($type == "player")
		{
			$that->conn->write("enumcargo ".$player."\r\n");
		} else {
			$that->conn->write("enumcargo$ ".$player."\r\n"); 
		}
		$result = $that->conn->read_till('OK');
		$arrCargoTmp = explode("<br>",$result);
		$arrCargo = array();
		$maxholdsize = 0;
		foreach($arrCargoTmp as $cargo)
		{
			$cargo = str_replace(" ","&",$cargo);
			parse_str($cargo);
			if($remainingholdsize) { $maxholdsize = $remainingholdsize; print "hold<br>";}
			if($id)
			{
				$cargo = explode(" ",$cargo);
				$sql = "SELECT * FROM cargoDB WHERE id='".$archid."'";
				$res = mysql_query($sql);
				$more = mysql_fetch_object($res);
				array_push($arrCargo,array("hookid" => $id,
								"archid" => $archid,
								"name" => $more->name,
								"type" => $more->type,
								"count" => $count,
								"mission" => $mission));
			}
			$remainingholdsize = null;
			$id = null;
			$archid = null;
			$count = null;
			$mission = null;
		}
		return $arrCargo;
	}
	function addcargo($that,$settings,$player,$good,$count=1,$mission=0,$type="player")
	{#addcargo <charname> <good> <count> <mission>
		if($type == "player")
		{
			$that->conn->write("addcargo ".$player." ".$good." ".$count." ".$mission."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return true;
		} else {
			$that->conn->write("addcargo$ ".$player." ".$good." ".$count." ".$mission."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			return true;
		}
	}
	function removecargo($that,$settings,$player,$id,$count=1,$type="player")
	{#removecargo <charname> <id> <count>
		if($type == "player")
		{
			$that->conn->write("removecargo ".$player." ".$id." ".$count."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			if($result){return true;}
		} else {
			$that->conn->write("addcargo$ ".$player." ".$good." ".$count."\r\n"); 
			$result = $that->conn->read_till('OK'); 
			if($result){return true;}
		}
	}
}
?>