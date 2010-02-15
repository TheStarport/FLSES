<?php
class Telnet { 
   // thies@thieso.net 2001 
   // patched by Niwo 2003 (niwo@freelancerserver.de)
   var $sock = NULL; 
//   var $readbuf = ''; 
//   var $idxreadbuf = -1; 
   var $htmloutput = false; 

   function telnet($host,$port) { 
    $this->sock = fsockopen($host,$port,$errno,$errstr,5); 
      if($this->sock)
      {
      	socket_set_timeout($this->sock,2,0);
      }
   }    
    function close() { 
        if ($this->sock) 
            fclose($this->sock); 
        $this->sock = NULL; 
   } 
    function write($buffer) { 
      $buffer = str_replace(chr(255),chr(255).chr(255),$buffer); 
        fwrite($this->sock,$buffer); 
   } 
   function getc() { 
//      if ( $this->idxreadbuf != strlen($this->readbuf) ) { 
//         $this->readbuf = fread($this->sock, 1); 
//         $this->idxreadbuf = 0; 
//      } 
//      return substr($this->readbuf,$this->idxreadbuf,1); 
      return fgetc($this->sock); 
   } 
    function read_till($what)  { 
        $buf = ''; 
      while (1) { 
         $IAC  = chr(255); 

         $DONT = chr(254); 
         $DO   = chr(253); 

         $WONT = chr(252); 
         $WILL = chr(251); 

         $theNULL = chr(0); 

         $c = $this->getc(); 

         if ($c === false) 
           if ( $this->htmloutput ) 
              return str_replace("\n",'<br>',$buf); 
           else 
              return $buf; 

         if ($c == $theNULL) { 
            continue; 
         } 

         if ($c == "\021") { 
            continue; 
         } 

         if ($c != $IAC) { 
            $buf .= $c; 

            if ($what == (substr($buf,strlen($buf)-strlen($what)))) { 
               if ( $this->htmloutput ) 
                  return str_replace(chr(13).chr(10),'<br>',substr($buf,0,strlen($buf)-strlen($what))); 
               else 
                  return substr($buf,0,strlen($buf)-strlen($what)); 
            } else { 
               continue; 
            } 
         } 

         $c = $this->getc(); 

         if ($c == $IAC) { 
            $buf .= $c; 
         } else if (($c == $DO) || ($c == $DONT)) { 
            $opt = $this->getc(); 
         //   echo "we wont ".ord($opt)."\n"; 
            fwrite($this->sock,$IAC.$WONT.$opt); 
         } elseif (($c == $WILL) || ($c == $WONT)) { 
            $opt = $this->getc(); 
         //   echo "we dont ".ord($opt)."\n"; 
            fwrite($this->sock,$IAC.$DONT.$opt); 
         } else { 
         //   echo "where are we? c=".ord($c)."\n"; 
         } 
      } 

   } 
   function sethtml($bool) { 
      $this->htmloutput = $bool; 
   } 
}
?>