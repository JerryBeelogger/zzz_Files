<?php
/*
 * (C) 2019 Jeremias Bruker, Thorsten Gurzan, Rudolf Schick - beelogger.de
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses>.
*/
// beelogger.de - Arduino Datenlogger für Imker
// Erläuterungen dieses Programmcodes unter https://beelogger.de

$Konfigversion = "M.15"; //vom 24.11.2020 - beelogger_config.php
if (file_exists("../beelogger_sprachfiles/Konfig_Sprache_".$Sprache.".php")) include ("../beelogger_sprachfiles/Konfig_Sprache_".$Sprache.".php");
if (file_exists("../beelogger_sprachfiles/Hilfe_Sprache_".$Sprache.".php")) include ("../beelogger_sprachfiles/Hilfe_Sprache_".$Sprache.".php");


$Farben = array($KAs[209],$KAs[210],$KAs[211],$KAs[212],$KAs[213],$KAs[214],$KAs[215],$KAs[216],$KAs[217],$KAs[218],$KAs[219],$KAs[220],$KAs[221],$KAs[222],$KAs[223],$KAs[224],$KAs[225],$KAs[226],$KAs[227]);

$HelpBackColor = "#F2F2F2";
$HelpColor = "blue";

echo '<div id="KonfigAnzeige"> <!------- FORM für Neue beelogger_ini.php -------->';
echo "<form action='beelogger_show.php' name='ConfigMainForm' method='post'>";

if ($MultiType >= 1)
  {
  echo'<table><td style="font-size:20px; color:blue;">'.$KAs[0].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
    '.$HAs[0].'.</td></tr><tr><td>
    '.$HAs[1].'.</td></tr>
  </table>
</details></td></table>'; 

  echo "<table border='4'><tr><td><img src='../beelogger_icons/info.png' width='50'<br><br><b>&nbsp;".$KAs[1]."&nbsp;&nbsp;</b></td>";
  echo'<td><form method="POST" action=""><p><textarea rows="6" name="neuinfo" cols="120" font size =6>'.html_entity_decode($Info).'</textarea></p></form></td></tr>';

  if ($beeloggerSketchID != ""AND $beeloggerSketchID != "EE_")
    { 
    $AktuelleServerSketchVersion = file_get_contents('https://www.community.beelogger.de/AktuelleSketchVersion.php');
    $AktuelleServerSketchVersionNummer = strstr($AktuelleServerSketchVersion,substr($beeloggerSketchID,0,-6));
    $AktuelleServerSketchVersionNummer = substr(strstr($AktuelleServerSketchVersionNummer,"+",true),-6);
    $AktuelleBeeloggerSketchVersionNummer = substr($beeloggerSketchID,-6);

    echo "<tr><td>".$KAs[2].":&nbsp;</td>";
    if ($AktuelleServerSketchVersionNummer > $AktuelleBeeloggerSketchVersionNummer) 
      {   
      echo "<td><input type='text' name = 'beeloggersketchid' value ='".$beeloggerSketchID."' size ='30' maxlength ='30'>";
      echo '<a href="https://beelogger.de/?page_id=197356" target="_blank" rel="noopener"> ...'.$KAs[3].' ('.substr($beeloggerSketchID,0,-6).$AktuelleServerSketchVersionNummer.")</a>";
      }
    else   echo "<td><input type='text' name = 'beeloggersketchid' value ='".$beeloggerSketchID."' size ='30' maxlength ='30'>";

    }
  echo "</td></tr>";
  echo '</table><br>';
  }
else 
  {

  //Ermittlung des Links für den MultiHauptbeelogger
  $actual_linkpos = strripos(strstr((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "https") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'/beelogger_show',TRUE),"/");
  $NeubeeloggerURL= substr(strstr((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "https") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'/beelogger_show',TRUE), 0,$actual_linkpos);
  $NeubeeloggerURL .= '/'.$MultiTypeName.$ServerMultiNumber.'/beelogger_show.php';
  
  //Hinweis Info
echo'<table><td style="font-size:20px; color:blue;">'.$KAs[0].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
    '.$HAs[0].'.</td></tr><tr><td>
    '.$HAs[1].'.</td></tr>
  </table>
</details></td></table>';
  echo "<table border='2' style='color:black; background-color:white'><td><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';></td><td>&nbsp;".$KAs[241]."&nbsp;</td>";

  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";

  include ($WechselIniName);
  echo "<tr>";
  echo "<td><img src='../beelogger_icons/info.png' width='18' height='18' style='margin-bottom:-4px';></td>";
  echo'<td colspan=2 style="color:black; background-color:white"><form method="POST" action=""><p><textarea disabled="disabled" rows="6" name="nix" cols="80" font size =6>'.html_entity_decode($Info).'</textarea></p></form></td>';
  echo "</td></tr>";
  echo "</table><br>"; 
  include ("beelogger_ini.php");  
  }  


//Sprache einstellen
echo'<table><td style="font-size:20px; color:blue;">'.$KAs[256].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
    '.$HAs[2].'.</td></tr><tr><td>
    '.$HAs[3].'.</td></tr>
  </table>
</details></td></table>';


echo "<table border='4'>";
echo "<td>&nbsp;".$KAs[144].": <select name='neusprache'>";

echo "<option value='1'";
if ($Sprache == "1") echo " selected";
echo ">🇩🇪".$KAs[145]."</option>";

echo "<option value='2'";
if ($Sprache == "2") echo " selected";
echo ">🇬🇧".$KAs[146]."</option>";

echo "<option value='3'";
if ($Sprache == "3") echo " selected";
echo ">🇫🇷".$KAs[147]."</option>";

echo"</select>&nbsp;</td>";
echo "</table><br>";


if ($MultiType < 1) echo '<table><td style="font-size:20px; color:blue;">'.$KAs[4].'</td>';
else echo '<table><td style="font-size:20px; color:blue;">'.$KAs[5].'</td>';

echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
    '.$HAs[4].'.</td>';

if ($MultiType > 0) echo '<td>'.$HAs[5].': ! * ‘ ( ) ; : @ & = + $ , / ? % # [ ]'.$HAs[6].'.</td>';
else echo "<td></td>";

echo' </tr>
<tr><td colspan=2>'.$HAs[7].'.</td></tr>
  </table>
</details></td></table>';


echo "<table border='4' style='color:black; background-color: #f8f9f9;'><tr>";
//echo"<td>&nbsp;".$KAs[6].":&nbsp;</td>";

if ($BeeloggerShowPasswort == "Show") echo "<td style='color:black; background-color:#e7846f;'>";
else echo "<td style='color:black; background-color:#a9dfbf;'>";
echo "<b>&nbsp;beelogger_show - ".$KAs[7].": <input type='text' name = 'neubeeloggershowpasswort' value ='".$BeeloggerShowPasswort."' size ='15' maxlength ='20'>&nbsp;</td>";

if ($MultiType > 0)
  {
  if ($BeeloggerLogPasswort == "Log") echo "<td style='color:black; background-color:#e7846f'>";
  else echo "<td style='color:black; background-color:#a9dfbf;'>"; 
  echo '<b>&nbsp;beelogger_log - '.$KAs[7].': <input type="text" name = "neubeeloggerlogpasswort" ';
  echo 'value ="'.$BeeloggerLogPasswort.'" size ="8" maxlength ="8">&nbsp;</td>';
  }

$files = scandir("../");
$S=0;
foreach ($files as $file)
  {
  if ( is_dir("../".$file) and $file != "." and $file != "..")
    {  $SortArray[$S] = $file;
      $S++;
    }
  }


echo'<table><td style="font-size:20px; color:blue;">'.$KAs[8].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
  '.$HAs[8].'.</td><td>
'.$HAs[9].'.</td></tr>
  </table>
</details></td></table>';

echo "<table border='4' style='color:black; background-color:#e5e7e9 ;'><td rowspan = 2>&nbsp;<img src='../beelogger_icons/bw_plus.png' height=35 width=35 style='margin-bottom:-4px';>&nbsp;</td><td>&nbsp;".$KAs[9].":&nbsp;";
echo "<select name='beeloggeranlage'>";
echo "<option value='' selected >".$KAs[10]."</option>";
echo "<option value='N'>beelogger (Single)</option>";
echo "<option value='2'>Duo (2 ".$KAs[11].")</option>";
echo "<option value='3'>Triple (3 ".$KAs[11].")</option>";
echo "<option value='4'>Quad (4 ".$KAs[11].")</option>";
echo "<option value='5'>Penta (5 ".$KAs[11].")</option>";
echo "<option value='6'>Hexa (6 ".$KAs[11].")</option>";
echo "<option value='7'>Sept (7 ".$KAs[11].")</option>";

echo "</select>&nbsp;";
echo "</td>";

echo '<td rowspan=2>&nbsp;<img src="../beelogger_icons/bw_minus.png" height=35 width=35 style="margin-bottom:-4px";>&nbsp;</td><td>&nbsp;'.$KAs[12].':&nbsp;';
echo "<select name='beeloggerloeschen'>";
echo "<option value='' selected >".$KAs[10]."</option>";
 

$files = scandir("../");
sort($files);
foreach ($files as $file)
  {
  if ( is_dir("../".$file) and $file != "." and $file != ".." and $file != "mobile")
    {  
    if (strlen($file) < 12 AND strpos($file, "beelogger") === 0)
      { 
      $beeloggerNumber = intval(str_replace("beelogger", "", $file));
      if ($beeloggerNumber == 1) echo "<option value='' style='color:gray';>beelogger1 (".$KAs[13].")</option>";
      elseif (intval(str_replace("beelogger", "", $beelogger)) == $beeloggerNumber) echo "<option value='' style='color:gray';>beelogger".$beeloggerNumber." (".$KAs[13].")</option>";
      else echo "<option value='N".$beeloggerNumber."'>beelogger".$beeloggerNumber."</option>";  
      }

    if (strpos($file, "Duo") === 0  OR strpos($file, "Triple") === 0 OR strpos($file, "Quad") === 0 OR strpos($file, "Penta") === 0 OR strpos($file, "Hexa") === 0 OR strpos($file, "Sept") === 0)
      { 
      $beeloggerTypeNow = preg_replace('![^a-zA-Z]!', '', $file); 
      $beeloggerNumberNow =  intval(preg_replace('![^0-9]!', '', $file));  
      $beeloggerSignNow =   substr($beeloggerTypeNow,0,1);

      if ($file == $beelogger OR (strpos($beelogger, ("beelogger".$beeloggerSignNow.$beeloggerNumberNow)) === 0)) echo "<option value='' style='color:gray';>".$file." (".$KAs[14].")</option>";
      else echo "<option value='M".$file."'>".$file." ".$KAs[90]." : beelogger".$beeloggerSignNow.$beeloggerNumberNow."_1 ".$KAs[37]." ";
      $i=2;
      while (file_exists("../beelogger".$beeloggerSignNow.$beeloggerNumberNow."_".$i)) 
        { 
        $i++;
        }
      echo "beelogger".$beeloggerSignNow.$beeloggerNumberNow."_".($i-1)."</option>";  
      }
    } 
  } 

echo "</select>&nbsp;</td>";
echo "<tr><td>";
echo $KAs[259];
echo "<input type='text' name='beeloggeranlagepasswort'>";
echo "</td></tr>";
echo "</table>";
echo '<br>';


echo'<table><td style="font-size:20px; color:blue;">'.$KAs[15].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
  '.$HAs[10].'.
</td></tr></table>
</details></td></table>';

echo "<table border='4' style='color:black; background-color:#d7dbdd ;'><td>&nbsp;".$KAs[16].":&nbsp;</td>";
echo "<td>";
echo"&nbsp;".$KAs[17]."&nbsp;";
echo "<input type='text' style='display:inline;' name = 'archivdateiname' value = '".$KAs[18]."' size ='16' maxlength ='20'>&nbsp;";
echo ".csv - <br>&nbsp ".$KAs[19]." ";
if (file_exists('beelogger.csv')) 
  {//array füllen mit aktueller csv-datei
  $input = 'beelogger.csv';  
  $array = file($input);
  $what = trim($array[0]);
  $x = explode( ",", $what );
  $ErstesDatum = $x[0];
  $i = sizeof($array);
  while ($i--) 
    {
    $what = trim($array[$i]);
    $x = explode( ",", $what );
    $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
    if ($x[$s-1] !='') 
      {   //letzte Spalte = Zeitstempel abzufragen
      $Aktualisierung=$x[$s-1];
      break;
      }
    }
  $ZeitFuerNeueNote = $Aktualisierung; //Hilfe für neue Note bei neuer Zeitintervalleingabe
  $Aktualisierung = date("d.m.Y H:i:s",$Aktualisierung);
  } // if exists 

echo "<select name='erstessplitdatum'>";
for ($d=0; $d < $i; $d = $d+1+abs($i/50))
  {
  $what = trim($array[$d]);
  $x = explode( ",", $what );
  $s = sizeof($x);
  echo "<option value=";
  echo $x[$s-1];
  echo ">".$x[0]."</option>";
  }
$what = trim($array[$i]); //letzter Wert extra
$x = explode( ",", $what );
$s = sizeof($x);
echo "<option value="; //letzter Wert
echo $x[$s-1];
echo ">".$x[0]."</option>";

echo'</select>';
echo "&nbsp;".$KAs[20]."&nbsp;";
echo "<select name='letztessplitdatum'>";
$karray = array_reverse($array);
for ($d=0; $d < $i; $d = $d + 1+ abs($i/50))
  {
  $what = trim($karray[$d]);
  $x = explode( ",", $what );
  $s = sizeof($x);
  echo "<option value=";
  echo $x[$s-1];
  echo ">".$x[0]."</option>";
  } 
$what = trim($karray[$i]); //letzter Wert extra
$x = explode( ",", $what );
$s = sizeof($x);
echo "<option value="; //letzter Wert
echo $x[$s-1];
echo ">".$x[0]."</option>";                      
echo'</select>';

echo "&nbsp&nbsp;</td>";
echo "<td></td>";
echo "<td>";
echo "&nbsp;&nbsp;".$KAs[21]." &nbsp;<br> &nbsp;&nbsp;&nbsp; ".$KAs[19]."&nbsp;";

echo "<select name='aktuellessplitdatum'>";
for ($d=0; $d < $i; $d = $d +  1+ abs($i/50))
  {
  $what = trim($array[$d]);
  $x = explode( ",", $what );
  $s = sizeof($x);
  echo "<option value=";
  echo $x[$s-1];
  echo ">".$x[0]."</option>";
  }
$what = trim($array[$i]); //letzter Wert extra
$x = explode( ",", $what );
$s = sizeof($x);
echo "<option value="; //letzter Wert
echo $x[$s-1];
echo ">".$x[0]."</option>";
echo'</select>';

echo "&nbsp;".$KAs[22]."&nbsp;";
echo "</td>";

echo "<td>";
echo "&nbsp;&nbsp;&nbsp;</td>";
echo "<td>";
echo "&nbsp;".$KAs[23]."&nbsp;&nbsp;";

echo '<select name="splittensichern">';
echo "<option value='0' selected >".$KAs[10]."</option>";
echo "<option value='1' >".$KAs[24]."</option>";
echo "&nbsp;&nbsp;";
echo "</select></td>"; 
echo "</table>";


//Dateien Download
echo "<table border='4' style='color:black; background-color:#d7dbdd ;'><td><img src='../beelogger_icons/download.png' width='30' height='30' style='margin-bottom:-4px';</td><td>&nbsp;".$KAs[25].":&nbsp;</td>";
foreach (glob("*.csv") as $filename)
  {
  if (file_exists($filename)) 
    {
    echo "<td>";
    if ($filename ==  "notes.csv") echo $KAs[26].": ";
    else echo $KAs[27].": ";
    echo "<a href='".$filename."' target='_blank'>".$filename."</a>&nbsp;&nbsp;</td>";
    }
  }

if ($MultiType < 2) 
  {//Daten für Forschungsprojekt vorbereiten
  echo "<td>".$KAs[273].": ";
  $ForscherArray = file('beelogger.csv');
  $ForscherDateiName = basename(__DIR__).".csv";
  $ForscherFirstLine = "Datum,";
  for ($i=0; $i < ($AnzahlSensoren-1); $i++) 
    { 
    $ForscherFirstLine .= html_entity_decode($Sensoren[$i*5]).",";
    }
  $ForscherFirstLine .= html_entity_decode($Sensoren[$i*5]).',Timestamp'."\r\n";


  $aktion = fOpen($ForscherDateiName,"w");
  fputs($aktion,$ForscherFirstLine);
  foreach($ForscherArray as $values){ fputs($aktion, $values);}
  fClose($aktion);

  echo "<a href='".$ForscherDateiName."' target='_blank'>".$ForscherDateiName."</a>&nbsp;&nbsp;</td>";
  } 


echo "<td><img src='../beelogger_icons/loeschen.png' width='30' height='30' style='margin-bottom:-4px';</td><td><b>\n&nbsp;".$KAs[28].": ";
echo "<select name='csvloeschdatei'>";
echo "<option value='' selected >".$KAs[29]."</option>";

foreach (glob("*.csv") as $filename)
  {
  if ($filename != "notes.csv" AND $filename != "beelogger.csv" AND $filename != "month.csv" AND $filename != "week.csv") 
    {
    echo "<option value='".$filename;
    echo "'>".substr($filename,0,-4)."</option>"; 
    }  
  }

  
echo "</select>&nbsp;</td>"; 

echo "</table>";
echo '<br>';

//Server beelogger Steuerung
if ($MultiType >= 1)
  {
  echo'<table><td style="font-size:20px; color:blue;">'.$KAs[30].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[11];
 
if ($beeloggerSketchID == "" OR strpos($beeloggerSketchID,"GSM") !== FALSE OR strpos($beeloggerSketchID,"LTE") !== FALSE) echo '<br>'.$HAs[12];
else echo '<br>'.$HAs[13];

echo'</td></tr></table>
</details></td></table>';  

  // Intervallsteuerung ------------------------
  echo "<table border='4' style='color:black; background-color: #bdc3c7;'><td rowspan=2><img src='../beelogger_icons/time.png' width='30' height='30' style='margin-bottom:-4px';</td><td rowspan=2>".$KAs[31].":&nbsp;</td>";
  echo "<td rowspan=2><b>".$KAs[32]."&nbsp;</b>";
  echo "<select name='neuintervallsendesteuerung'>";

  echo "<option value='deaktiviert'";
  if ($IntervallSendeSteuerung == "deaktiviert") echo " selected";
  echo ">".$KAs[33]."</option>";

  echo "<option value='zeitgesteuert'";
  if ($IntervallSendeSteuerung == "zeitgesteuert") echo " selected";
  echo ">".$KAs[34]."</option>";

  echo "<option value='lichtgesteuert'";
  if ($IntervallSendeSteuerung == "lichtgesteuert") echo " selected";
  echo ">".$KAs[36]."</option>";

 echo "<option value='solarspannungsgesteuert'";
  if ($IntervallSendeSteuerung == "solarspannungsgesteuert") echo " selected";
  echo ">".$KAs[35]."</option>";

  echo "</select></td>";

  $monate = array(1=>$KAs[38],2=>$KAs[39],3=>$KAs[40],4=>$KAs[41],5=>$KAs[42],6=>$KAs[43],7=>$KAs[44],8=>$KAs[45],9=>$KAs[46],10=>$KAs[47],11=>$KAs[48],12=>$KAs[49]);

  echo"<td colspan=2><b>".$KAs[50].":</b>";
  echo "<select name='neusommerbeginn'>";
  
  for ($t=1 ; $t <= 6; $t++ ) 
    {
    echo "<option value='".$t."'";

    if ($SommerBeginn == $t) echo " selected";
    echo ">1 ".$monate[$t]."</option>";
    $halb = $t+0.5;
    echo "<option value='".$halb."'";

    if ($SommerBeginn == $halb ) echo " selected";
    echo ">15 ".$monate[$t]."</option>";
    }
  echo "</select></td>";

  echo"<td><b>".$KAs[51].":\n";
  echo "<select name='neuwinterbeginn'>";

  echo "<option value='deaktiviert'";
  if ($WinterBeginn == "deaktiviert") echo " selected";
  echo ">".$KAs[52]."</option>";

  for ($t=7 ; $t <= 12; $t++ ) 
    {
    echo "<option value='".$t."'";

    if ($WinterBeginn == $t) echo " selected";
    echo ">1 ".$monate[$t]."</option>";

    $halb = $t+0.5;
    echo "<option value='".$halb."'";
    if ($WinterBeginn == $halb ) echo " selected";
    echo ">15 ".$monate[$t]."</option>";
    }

     
  echo "</select></td>";
  echo"<tr>";

  echo "\n<td><b>&nbsp".$KAs[53];
  echo "<select style='display:inline;'name='neusommertagzeit'>";
  for ($t=4 ; $t <= 10; $t++ ) 
    {
    echo "<option value='".$t."'";
    if ($SommerTagZeit == $t) echo " selected";
    echo ">".$t."&nbsp;".$KAs[54]."</option>";
    }

  echo "</select>";

  echo  "&nbsp;".$KAs[106]."&nbsp; <input type='number' style='width:40px;' style='display:inline;' name ='neusommersendeintervalltag' value ='".$SommerSendeIntervallTag."' min='5' max='240' step='1'>&nbsp;".$KAs[55]."</b></td>";

  echo "\n<td><b>".$KAs[56];
  echo "<select style='display:inline;'name='neusommernachtzeit'>";
  for ($t=17 ; $t <= 23; $t++ ) 
    {
    echo "<option value='".$t."'";
    if ($SommerNachtZeit == $t) echo " selected";
    echo ">".$t."&nbsp;".$KAs[54]."</option>";
    }

  echo "</select>";
  echo "&nbsp;".$KAs[106]."&nbsp; <input type='number' style='width:40px;' style='display:inline;' name = 'neusommersendeintervallnacht' value ='".$SommerSendeIntervallNacht."' min='5' max='240' step='1'>&nbsp;".$KAs[55]."</b></td>";
    echo "\n<td><b>".$KAs[57]."&nbsp;<input type='number' style='width:40px;' style='display:inline;' name = 'neuwintersendeintervall' value ='".$WinterSendeIntervall."' min='5' max='240' step='1'>&nbsp;".$KAs[55]."</b></td></tr>";

  echo "</tr></table>";

  //nächstes Sendeintervall
  if (file_exists('beelogger_map.php')) include('beelogger_map.php');

  echo "<table border='4' style='color:black; background-color: #bdc3c7;'><td rowspan=3><img src='../beelogger_icons/time.png' width='30' height='30' style='margin-bottom:-4px';</td><td rowspan=3>".$KAs[31].":&nbsp;</td>";

  echo "<td>&nbsp".$KAs[289]."</td>";
  echo "<td>&nbsp".$KAs[290]."</td>";

  echo "<tr><td>&nbsp".$KAs[58]."</td>";
  if (strpos($beeloggerSketchID,"EE") != FALSE) echo "<td colspan = 3style='background-color: #a9dfbf;'>";
  else echo "<td colspan=3>";
  echo"<b>\n";
  echo "<select name='neueesendeintervall'>";

    
  echo "<option value='A'";
  if ($EESendeIntervall == "A") echo " selected";
  echo ">".$KAs[59]." > 30min)</option>";

  echo "<option value='B'";
  if ($EESendeIntervall == "B") echo " selected";
  echo ">".$KAs[60]." > 30min)</option>";

    echo "<option value='C'";
  if ($EESendeIntervall == "C") echo " selected";
  echo ">".$KAs[61]." > 30min)</option>";

    echo "<option value='D'";
  if ($EESendeIntervall == "D") echo " selected";
  echo ">".$KAs[62]." > 30min)</option>";
  echo "</select>";
  
  echo"</td></tr>";
  

  // nächster Service
  echo "<tr><td>";
  echo"<b>\n&nbsp".$KAs[63].":&nbsp;</td>";
  if (file_exists("loc.php")) echo "<td colspan = 1>"; // Locationangabe folgt
  else echo "<td colspan = 3>";

  echo "<select name='neunextservice'>";

  echo "<option value=''";
  if ($NextService == "") echo " selected";
  echo ">".$KAs[64]."</option>";


  echo "<option value='P'";
  if ($NextService == "P") echo " selected";
  echo ">".$KAs[65]."</option>";

  if ($beeloggerSketchID == "" OR strpos($beeloggerSketchID,"GSM") !== FALSE OR strpos($beeloggerSketchID,"LTE") !== FALSE)
    {
    echo "<option value='V'";
    if ($NextService == "V") echo " selected";
    echo ">".$KAs[66]."</option>";

    if ($beeloggerSketchID == "" OR strpos($beeloggerSketchID,"LTE") !== FALSE)
      {
      echo "<option value='L'";
      if ($NextService == "L") echo " selected";
      echo ">".$KAs[67]."</option>";
      }

    echo "</select>";
    echo"</td>";

    if (file_exists("loc.php"))
      {
      include("loc.php");
      echo "<td style='color:black; background-color: #a9dfbf;'>";
      echo $KAs[68].' = '.round($lat,2).' '.$KAs[69].' = '.round($lon,2).' '.$KAs[70].' <a href="https://nominatim.openstreetmap.org/reverse.php?format=html&lat='.$lat.'&lon='.$lon.'&z=16" target="_blank" rel="noopener">OpenStreetMap</a>';
        echo ' '.$KAs[71].' <a href="https://www.google.de/maps/@'.$lat.','.$lon.',15z" target="_blank" rel="noopener">GoogleMaps</a>'.$KAs[72];
      echo "</td>";
      echo "<td style='color:black; background-color: #a9dfbf;'>&nbsp;".$KAs[73]."&nbsp;&nbsp;";

      echo '<select name="standortloeschen">';
      echo "<option value='0' selected >".$KAs[10]."</option>";
      echo "<option value='1' >".$KAs[24]."</option>";
      echo "&nbsp;&nbsp;";
      echo "</select></td></tr>"; 
      } 
    }
  echo "</table>".$KAs[288]."<br><br>";


  // Temperaturkompensation
  if ($MultiType > 1) //für Multi
    {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[74].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><td>'.$HAs[14].'.</td>
  </table>
</details></td></table>';  

    echo "<table border='4' style='color:black; background-color: #a6acaf 
    ;'><tr><td><b>".$KAs[75]."&nbsp;</b></td>";
    for ($i=1; $i <= $MultiType; $i++) 
      {
      if ($KorrekturwertArray[$i-1] != "" AND $KorrekturwertArray[$i-1] != "0") echo "<td style='color:black; background-color: #a9dfbf;'>";
      else echo "<td>";
      echo "<b>&nbsp".$KAs[76].$i.":<br> <input type='text' style='display:inline;' name = 'neukorrekturwert".$i."' value ='".$KorrekturwertArray[$i-1]."' size ='5' maxlength ='6'>&nbsp;".$GewichtsEinheit." / ".$KAs[77]."</b></td>";
      }

    if ($KalibrierTemperatur != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo "<b>&nbsp".$KAs[78]."&nbsp; <input type='text' style='display:inline;' name = 'neukalibriertemperatur' value ='".$KalibrierTemperatur."' size ='4' maxlength ='5'>&nbsp;".$KAs[77]."&nbsp;</b></td>";
    echo "<tr><td> ".$KAs[79]."</td>";
    echo "<td align=center>HX711-1 ".$KAs[80].":A</td><td align=center>HX711-1 ".$KAs[80].":B</td>"; 
    if ($MultiType > 2) echo "<td align=center>HX711-2 ".$KAs[80].":A</td>"; 
    if ($MultiType > 3) echo "<td align=center>HX711-2 ".$KAs[80].":B</td>";
    if ($MultiType > 4) echo "<td align=center>HX711-3 ".$KAs[80].":A</td>";
    if ($MultiType > 5) echo "<td align=center>HX711-3 ".$KAs[80].":B</td>";
    if ($MultiType > 6) echo "<td align=center>HX711-4 ".$KAs[80].":A</td>";
    echo "<td></td></tr></table><br>";
    }
  else //für normale beelogger
    {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[81].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><td>'.$HAs[14].'.</td>
  </table>
</details></td></table>';    
    echo "<table border='4'style='color:black; background-color: #a6acaf 
    ;'><td style='background-color: white;'><img src='../beelogger_icons/weight.png' height=25 width=25>";

    if ($Korrekturwert != "" AND $Korrekturwert != "0") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo"<td>";
    echo "<b>".$KAs[82]."<input type='text' style='display:inline;' name = 'neukorrekturwert' value ='".$Korrekturwert."' size ='5' maxlength ='6'>&nbsp;".$GewichtsEinheit." / ".$KAs[77]."</b></td>";
    echo "<td style='background-color: white;'><img src='../beelogger_icons/bw_temp.png' height=25 width=25>";
    if ($KalibrierTemperatur != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo "<b>&nbsp".$KAs[78]."&nbsp; <input type='text' style='display:inline;' name = 'neukalibriertemperatur' value ='".$KalibrierTemperatur."' size ='4' maxlength ='5'>&nbsp;".$KAs[77]."&nbsp;</b></td></table><br>";
    }
  } //if ($MultiType >= 1)
else // für Unterbeelogger Hinweis anzeigen
  {  
  //Hinweis Fernsteuerung 
  echo'<span style="font-size:20px; color:blue;">'.$KAs[30].'</span>'; 
  echo "<table border='2' style='color:black; background-color:white'><td><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';></td><td>&nbsp;".$KAs[241]."&nbsp;</td>";

  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";

  include ($WechselIniName);
  echo "<tr>";
  echo "<td><img src='../beelogger_icons/time.png' width='18' height='18' style='margin-bottom:-4px';></td>";
  echo "<td colspan=3 align=middle style='color:black; ";
  if ($IntervallSendeSteuerung != "deaktiviert") echo "background-color: #a9dfbf;'>";
  else echo "background-color:#626567;'>";
  echo $KAs[237].": ";
  if ($IntervallSendeSteuerung == "deaktiviert") echo $KAs[33];
  if ($IntervallSendeSteuerung == "zeitgesteuert") echo $KAs[34];
  if ($IntervallSendeSteuerung == "solarspannungsgesteuert") echo $KAs[35];
  if ($IntervallSendeSteuerung == "lichtgesteuert") echo $KAs[36];
  echo "</td></tr>";
  echo "</table><br>"; 
  include ("beelogger_ini.php");




  //Hinweis Tempkomp
  echo'<span style="font-size:20px; color:blue;">'.$KAs[74].'</span>'; 
  echo "<table border='2' style='color:black; background-color:white'><td><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';></td><td>&nbsp;".$KAs[241]."&nbsp;</td>";

  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";

  include ($WechselIniName);
  echo "<tr >";
  echo "<td><img src='../beelogger_icons/off_weight.png' width='18' height='18' style='margin-bottom:-4px';></td>";
  echo "<td colspan=3 align=middle style='color:black; ";
  if ($KorrekturwertArray[$ServerMultiUnterordnerNummer-1] != "") echo "background-color: #a9dfbf;'>";
  else echo "background-color:#a6acaf;'>";
  echo $KAs[244];
  if ($KorrekturwertArray[$ServerMultiUnterordnerNummer-1] != "") echo"&nbsp;".$KAs[245].":&nbsp;".$KorrekturwertArray[$ServerMultiUnterordnerNummer-1]." ".$GewichtsEinheit."/".$KAs[77]."&nbsp;".$KAs[246]."&nbsp;".$KalibrierTemperatur."&nbsp;".$KAs[77];
  else echo "&nbsp;".$KAs[247].".";
  echo "</td></tr>";
  echo "</table><br>"; 
  include ("beelogger_ini.php");
  }


// Autoanmerkungen  ----------------------------------

if ($MultiType < 2) // für alle Einzelansichten
  { 
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[83].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[15].'.</td></tr>
  </table>
</details></td></table>';   

  echo "<table border='4'  style='color:black; background-color:#909497;'>";

  if ($AutoAnmerkungenErzeugen == "aktiviert") echo "<td style='color:black; background-color:#a9dfbf;''>";
  else echo "<td>";

  echo"<b>\n&nbsp".$KAs[85].":&nbsp;";
  echo "<select name='neuautoanmerkungenerzeugen'>";
  echo "<option value='aktiviert'";

  if ($AutoAnmerkungenErzeugen == "aktiviert") echo " selected";
  echo ">".$KAs[89]."</option>";
   
  echo "<option value='deaktiviert'";
  if ($AutoAnmerkungenErzeugen == "deaktiviert") echo " selected";
  echo ">".$KAs[33]."</option>";
  echo "</select>&nbsp;</td>";

  if ($AutoAnmerkungenErzeugen == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";
  echo "\n<b>&nbsp".$KAs[86].": <input type='text' style='display:inline;' name = 'neuanmerkunggewichtsdifferenz' value ='".$AnmerkungGewichtsDifferenz."' size ='3' maxlength ='5'>&nbsp;".$GewichtsEinheit."&nbsp;</td>";

  if ($AutoAnmerkungenErzeugen == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;' colspan=2>";
  else echo "<td colspan=2>";
  echo "\n<b>&nbsp".$KAs[87].":&nbsp;<input type='text' style='display:inline;' name = 'neuanmerkungzeitdifferenz' value ='".$AnmerkungZeitDifferenz."' size ='3' maxlength ='3'>&nbsp;min&nbsp;</td>";
  
  if ($MultiType <= 1)
    {
    echo '<tr><td colspan=3> <img src="../beelogger_icons/n_Durchsicht.png" height=20 width=20 style="margin-bottom:-3px";> '.$KAs[260].' ?';
    if ($AutoServiceAnmerkung == "true") echo "<td style='color:black ; background-color: #a9dfbf;'>";
    else echo "<td style='color:black ; background-color: #e7846f;' colspan=2>";

    echo"&nbsp<input type='checkbox' value='true' name='neuautoserviceanmerkung'";
    if ($AutoServiceAnmerkung == "true") echo " checked";
    echo">&nbsp</td>";
    echo "<td>".$KAs[261]." </td>";
    if ($AutoServiceAnmerkung == "true") echo "<td style='color:black; background-color: #a9dfbf;' >";
    else echo "<td>";
    if ($AutoServiceAnmerkungZeit == "") $AutoServiceAnmerkungZeit = 5;
    echo "\n<b>&nbsp<input type='number' style='width:40px;' style='display:inline;' name ='neuautoserviceanmerkungzeit' value ='".$AutoServiceAnmerkungZeit."' min='3' max='90' step='1'>&nbsp;min&nbsp;</td></tr>"; 
    }
  echo "</table><br>";
  }

  if ($MultiType > 1)
    {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[83].'</td>'; 
    echo'<td><details>
    <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[35].'.</td></tr>
  </table>
</details></td></table>';   

  echo "<table border='4'  style='color:black; background-color:#909497;'>";
    echo '<tr><td colspan=2> <img src="../beelogger_icons/n_Durchsicht.png" height=20 width=20 style="margin-bottom:-3px";> '.$KAs[260].'?';
    if ($AutoServiceAnmerkung == "true") echo "<td style='color:black ; background-color: #a9dfbf;'>";
    else echo "<td style='color:black ; background-color: #e7846f;' colspan=2>";

    echo"&nbsp<input type='checkbox' value='true' name='neuautoserviceanmerkung'";
    if ($AutoServiceAnmerkung == "true") echo " checked";
    echo">&nbsp</td>";
    echo "<td> ".$KAs[261]." </td>";
    if ($AutoServiceAnmerkung == "true") echo "<td style='color:black; background-color: #a9dfbf;' >";
    else echo "<td>";
    if ($AutoServiceAnmerkungZeit == "") $AutoServiceAnmerkungZeit = 5;
    echo "\n<b>&nbsp<input type='number' style='width:40px;' style='display:inline;' name ='neuautoserviceanmerkungzeit' value ='".$AutoServiceAnmerkungZeit."' min='3' max='90' step='1'>&nbsp;min&nbsp;</td></tr>"; 
    }
  echo "</table><br>";




// Schwarmalarm ----------------------------------

if ($MultiType >= 1) 
  { 
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[88].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[16].'</td></tr><tr><td>'.$HAs[36].'<img src="../beelogger_icons/h_Triggeralarm.png" height=51 style="margin-bottom:-6px";><br>'.$HAs[37].'<img src="../beelogger_icons/n_Triggeralarm.png" height=50 style="margin-bottom:-12px";>'.$HAs[38].'</td></tr>
  </table>
</details></td></table>';  
}

if ($MultiType > 1) 
  { 
  echo "<table border='4' style='color:black; background-color: #797d7f;'>";
  for ($iS=1; $iS <= $MultiType; $iS++) 
    {
     
    echo "<tr><td style='background-color: white;' rowspan=2><img src='../beelogger_icons/n_Schwarmabgang.png' height=40 width=40 style='margin-bottom:-3px';></td><td><b>&nbsp".$KAs[91].": ";
    if ($MultiType > 1)echo" beelogger".$MultiSign.$ServerMultiNumber."_".$iS."&nbsp;";
    echo"</b></td>";

    if ($EmailSchwarmAlarmArray[$iS-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'";
    else echo "<td";
    
    echo " rowspan=2><b>\n&nbsp".$KAs[92].": ";

    echo "<input type='checkbox' name='neuemailschwarmalarm".$iS."' value='aktiviert'";
     if ($EmailSchwarmAlarmArray[$iS-1] == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

    if ($PushSchwarmAlarmArray[$iS-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'";
    else echo "<td";
    
    echo " rowspan=2><b>\n&nbsp".$KAs[93].": ";
    echo "<input type='checkbox' name='neupushschwarmalarm".$iS."' value='aktiviert'";
     if ($PushSchwarmAlarmArray[$iS-1] == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

    
    if (($EmailSchwarmAlarmArray[$iS-1] == "aktiviert") OR ($PushSchwarmAlarmArray[$iS-1] == "aktiviert")) echo "<td style='color:black; background-color: #a9dfbf;'";
    else echo "<td";
    
    echo "\n rowspan=2><b>&nbsp".$KAs[94]."_".$iS;
    echo": <input type='text' style='display:inline;' name = 'neureferenzzeit".$iS."' value ='".$ReferenzZeitArray[$iS-1]."' size ='3' maxlength ='3'>&nbsp;".$KAs[55]."&nbsp;</td>";

    if (($EmailSchwarmAlarmArray[$iS-1] == "aktiviert") OR ($PushSchwarmAlarmArray[$iS-1] == "aktiviert")) echo "<td style='color:black; background-color: #a9dfbf;'";
    else echo "<td";
    
    echo "\n rowspan=2><b>&nbsp".$KAs[95]."_".$iS;
    echo ": <input type='text' style='display:inline;' name = 'neudifferenzgewicht".$iS."' value ='".$DifferenzGewichtArray[$iS-1]."' size ='4' maxlength ='4'>&nbsp;".$GewichtsEinheit."&nbsp;&nbsp;&nbsp;</td>";


    if ($SchwarmAlarmMessageArray[$iS] != '-')  
      { 
      echo "<td style='color:black; background-color: #a9dfbf;' rowspan=2>";
      echo "<b>\n".$SchwarmAlarmMessageArray[$iS]."&nbsp;-&nbsp;".$KAs[96].": ";
      echo '<input type="checkbox" name="schwarmalarmloeschen'.$iS.'" value="1">';  
      } 
    echo "</tr>";
    echo "<tr>";
    if ($EmailSchwarmAlarmArray[$iS-1] == "aktiviert" OR $PushSchwarmAlarmArray[$iS-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>&nbsp".$KAs[89];
    else echo "<td>&nbsp".$KAs[33];
    echo "</td></tr>";

    }      
    echo "</table>";

  }

elseif ($MultiType == 1)
  {
  echo "<table border='4'  style='color:black; background-color: #797d7f;'><td style='background-color: white;' rowspan=2><img src='../beelogger_icons/n_Schwarmabgang.png' height=40 width=40 style='margin-bottom:-3px';></td><td><b>&nbsp".$KAs[91].": &nbsp;</b></td>";

  if ($EmailSchwarmAlarm == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  
  echo " rowspan=2><b>\n&nbsp".$KAs[92].":&nbsp;";

  echo "<input type='checkbox' name='neuemailschwarmalarm' value='aktiviert'";
     if ($EmailSchwarmAlarm == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

  if ($PushSchwarmAlarm == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  
  echo " rowspan=2><b>\n&nbsp".$KAs[93].":&nbsp;";

  echo "<input type='checkbox' name='neupushschwarmalarm' value='aktiviert'";
     if ($PushSchwarmAlarm == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

  
  if (($EmailSchwarmAlarm == "aktiviert") OR ($PushSchwarmAlarm == "aktiviert")) echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  
  echo "\n rowspan=2><b>&nbsp".$KAs[94].": <input type='text' style='display:inline;' name = 'neureferenzzeit' value ='".$ReferenzZeit."' size ='3' maxlength ='3'>&nbsp;".$KAs[55]."&nbsp;</td>";

  if (($EmailSchwarmAlarm == "aktiviert") OR ($PushSchwarmAlarm == "aktiviert")) echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  
  echo "\n rowspan=2><b>&nbsp".$KAs[95].": <input type='text' style='display:inline;' name = 'neudifferenzgewicht' value ='".$DifferenzGewicht."' size ='4' maxlength ='4'>&nbsp;".$GewichtsEinheit."&nbsp;&nbsp;</td>";

  if ($SchwarmAlarmMessageArray[0] != '-')  
    { 
    echo "<td style='color:black; background-color: #a9dfbf;' rowspan=2>";
    echo "<b>\n".$SchwarmAlarmMessageArray[0]."&nbsp;-&nbsp;".$KAs[96].": ";
    echo "<select name='schwarmalarmloeschen'>";
    echo "<option value='0' selected >".$KAs[10]."</option>";
    echo "<option value='1' >".$KAs[24]."</option>";
    echo "&nbsp;&nbsp;";
    echo "</select></td>";    
    }

  echo "</tr>";
  echo "<tr>";
  if ($EmailSchwarmAlarm == "aktiviert" OR $PushSchwarmAlarm == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>&nbspAktiviert";
  else echo "<td>&nbspDeaktiviert";
  echo "</td></tr>";
  
  echo"</table>";
  }

else 
  {  //beeloggerD,T oder Q-Typ
  //Hinweis Schwarmalarm
  echo'<span style="font-size:20px; color:blue;">'.$KAs[88].'</span>';  
  echo "<table border='2' style='color:black; background-color: white'><td align=middle><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';</td><td>&nbsp;".$KAs[241]."&nbsp;</td>";
  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";
  include ($WechselIniName);
  echo "<tr>";
  echo "<td><img src='../beelogger_icons/off_bienen_out.png' width='18' height='18' style='margin-bottom:-4px';</td>";
  echo "<td colspan=3 align=middle style='color:black; ";

  $Aktive = 0;
  for ($i=1; $i < 5; $i++) 
    { 
    if ($TriggerAlarmArray[$i][1] == "aktiviert" OR $TriggerAlarmArray[$i][2] == "aktiviert") $Aktive ++;
    }

  if ($EmailSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert" OR $PushSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert" OR $Aktive > 0) echo "background-color: #a9dfbf;'>";
  else echo "background-color:#797d7f;'>";
  echo $KAs[248]."&nbsp;";
  if ($EmailSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo $KAs[92];
  if ($EmailSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert" AND $PushSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo " und ";
  if ($PushSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo $KAs[93];
  if ($EmailSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert" OR $PushSchwarmAlarmArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo "&nbsp;".$KAs[274].": ".$ReferenzZeitArray[$ServerMultiUnterordnerNummer-1]."&nbsp;".$KAs[275].":".$DifferenzGewichtArray[$ServerMultiUnterordnerNummer-1]." ".$GewichtsEinheit."&nbsp;".$KAs[89].".";
  else echo $KAs[33].".";

  $Aktive = 0;
  for ($i=1; $i < 5; $i++) 
    { 
    if ($TriggerAlarmArray[$i][1] == "aktiviert" OR $TriggerAlarmArray[$i][2] == "aktiviert") 
      {  
      if ($Aktive > 0) echo " + Triggeralarm".$i;
      else echo " Triggeralarm".$i;
      $Aktive ++;
      } 
    }
  if ($Aktive == 1) echo " ist aktiviert. ";
  elseif ($Aktive > 1) echo " sind aktiviert. ";
  else echo " - Alle Triggeralarme sind deaktiviert.";

  echo "</td></tr>";
  echo "</table><br>"; 
  include ("beelogger_ini.php");
  }


if ($MultiType > 0)
  {// Triggeralarmeinstellungen 
  echo "<table border='4'  style='color:black; background-color: #797d7f;'>";
  $t=1;
  while ($t<5)     
    {
    if ($t==1 OR $TriggerAlarmArray[$t-1][1] == "aktiviert" OR $TriggerAlarmArray[($t-1)][2] == "aktiviert" OR $TriggerAlarmArray[($t-1)][5] != "" OR $TriggerAlarmArray[$t][1] == "aktiviert" OR $TriggerAlarmArray[$t][2] == "aktiviert" OR $TriggerAlarmArray[$t][5] != "")
      { 

      if (($TriggerAlarmArray[$t][1] == "aktiviert" OR $TriggerAlarmArray[$t][2] == "aktiviert") AND $TriggerGesendetArray[$t] != "deaktiviert") echo "<tr style='color:black; background-color: #a9dfbf;'>";
      else echo"<tr>";
      echo "<td style='background-color: white;' rowspan=2><img src='../beelogger_icons/n_Triggeralarm.png' height=40 width=40 style='margin-bottom:-3px';></td>";

      echo "<td style='color:black; background-color: #797d7f;'><b>&nbsp".$KAs[263].$t.": &nbsp;</b></td>";

      if ($TriggerAlarmArray[$t][1] != "aktiviert" AND $TriggerGesendetArray[$t] != "deaktiviert") echo "<td style='color:black; background-color: #797d7f;' ";else echo "<td";

      echo " rowspan=2><b>\n&nbsp".$KAs[92].":"."&nbsp;";

      echo "<input type='checkbox' name='neutriggeralarmemail".$t."' value='aktiviert'";
         if ($TriggerAlarmArray[$t][1] == "aktiviert" AND $TriggerGesendetArray[$t] != "deaktiviert") echo " checked";
         echo ">&nbsp;</td>";

      if ($TriggerAlarmArray[$t][2] != "aktiviert" AND $TriggerGesendetArray[$t] != "deaktiviert") echo "<td style='color:black; background-color: #797d7f;' rowspan=2>";else echo "<td rowspan=2>";
      echo "<b>\n&nbsp"."per Push:"."&nbsp;";
      echo "<input type='checkbox' name='neutriggeralarmpush".$t."' value='aktiviert'";
      if ($TriggerAlarmArray[$t][2] == "aktiviert" AND $TriggerGesendetArray[$t] != "deaktiviert") echo " checked";
      echo ">&nbsp;</td>";

      echo "\n<td rowspan=2><b>&nbsp"."Sensor: "."<select style='display:inline;' name = 'neutriggeralarmsensor".$t."'>";
      for ($i=0; $i < $AnzahlSensoren; $i++) 
        {
          if (!is_numeric($Sensoren[($i)*5]))
          {  
          echo "<option value='".($i)."'";
          if ($TriggerAlarmArray[$t][3] == $i) echo " selected";
          echo ">".$Sensoren[($i)*5]."</option>";
          }
        }
      echo "</select></td>";

      echo "\n<td rowspan=2>&nbsp;<select style='display:inline;' name = 'neutriggeralarmzeichen".$t."' >&nbsp;";
      echo "<option value='<' ";if ($TriggerAlarmArray[$t][4] == "<") echo " selected";
      echo ">".$KAs[276]."</option>";
      echo "<option value='>' ";if ($TriggerAlarmArray[$t][4] == ">") echo " selected";
      echo ">".$KAs[277]."</option>";
      echo "</select>&nbsp;</td>";

      echo "\n<td rowspan=2>&nbsp;<input type='text' style='display:inline;' name = 'neutriggeralarmwert".$t."' value ='".$TriggerAlarmArray[$t][5]."' size ='5' maxlength ='5'>&nbsp;</td>";

      echo "\n<td rowspan=2>&nbsp;".$KAs[278].": <select style='display:inline;' name = 'neutriggeralarmpause".$t."' >&nbsp;";

      echo "<option value='5' ";if ($TriggerAlarmArray[$t][6] == "5") echo " selected";
      echo ">"."5 ".$KAs[55]." (Pushover)"."</option>";
      echo "<option value='30' ";if ($TriggerAlarmArray[$t][6] == "30") echo " selected";
      echo ">"."30 ".$KAs[55]." (Pushover)"."</option>";
      echo "<option value='60' ";if ($TriggerAlarmArray[$t][6] == "60") echo " selected";
      echo ">"."1 ".$KAs[281]." (Pushover)"."</option>";
      echo "<option value='720' ";if ($TriggerAlarmArray[$t][6] == "720") echo " selected";
      echo ">"."12 ".$KAs[282]." (Pushover)"."</option>";
      echo "<option value='1440' ";if ($TriggerAlarmArray[$t][6] == "1440") echo " selected";
      echo ">"."24 ".$KAs[282]." (Pushover & Mail)"."</option>";
      echo "<option value='9999' ";if ($TriggerAlarmArray[$t][6] == "9999") echo " selected";
      echo ">".$KAs[283]."</option>";
      echo "</select>&nbsp;</td>";

      
      if ($TriggerAlarmArray[$t][7] != "aktiviert") echo "<td color:black; background-color: #797d7f;'>";
      else echo "<td>";
      echo "\n&nbsp".$KAs[284]."&nbsp;";
      echo "<input style='display:inline;' type='checkbox' name='neutriggeralarmanmerkung".$t."' value='aktiviert'";
      if ($TriggerAlarmArray[$t][7] == "aktiviert") echo " checked";
      echo ">&nbsp;</td>";
      echo "</tr>";

  //zeile 2
      if (($TriggerAlarmArray[$t][1] == "aktiviert" OR $TriggerAlarmArray[$t][2] == "aktiviert") AND $TriggerGesendetArray[$t] != "deaktiviert") echo "<tr style='color:black; background-color: #a9dfbf;'><td><b>\n&nbsp"."Aktiviert"."&nbsp;</td>";
      else echo"<tr><td><b>\n&nbsp;".$KAs[33]."&nbsp;</td>";

      if ($TriggerGesendetArray[$t] != "" AND $TriggerGesendetArray[$t] != "deaktiviert") echo "<td>".$KAs[286].": ".date("d.m H:i",$TriggerGesendetArray[$t])." Uhr";
      elseif ($TriggerGesendetArray[$t] == "deaktiviert") echo "<td>&nbsp;".$KAs[285];
      else echo "<td>&nbsp;".$KAs[287];
      echo "</td>";
      echo "</tr>";
      }
    $t++;
    }
    echo "</table>";
        

  if  ($CommunityUser && $TestAccountUser) echo "<br>".$KAs[97]; //Info für Testaccountuser

  echo "<table border='4'  style='color:black; background-color: #797d7f;'><td rowspan=2><b>&nbsp;".$KAs[98]."&nbsp;&nbsp;</b></td>";
  echo "<td rowspan=2>&nbsp;&nbsp;E-Mail:&nbsp;&nbsp;</td>";


  if ($Empfaenger_Email != "empfaenger@meineDomain.de") echo "<td style='color:black; background-color:#a9dfbf;'";
  else echo "<td";
  echo " align=right>\n<b>&nbsp".$KAs[99].": <input type='text' style='display:inline;' name = 'neuempfaenger_email' value ='".$Empfaenger_Email."' size ='25' maxlength ='100'>&nbsp;&nbsp;</td>";

  echo "<td rowspan=2>&nbsp;&nbsp;Pushover:&nbsp;&nbsp;</td>";

    if ($PushUser != "meinUser" AND  $PushUser != "" AND  $PushUser != "PushUser") echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";

  echo " align=right>\n<b>&nbspPush-User-Key: <input type='text' style='display:inline;' name = 'neupushuser' value ='".$PushUser."' size ='12' maxlength ='50'>&nbsp;&nbsp;&nbsp;&nbsp;</td>";


  echo "</tr><tr>";


  if ($Absender_Email != "absender@meineDomain.de") echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  echo " align=right>\n<b>&nbsp".$KAs[100].": <input type='text' style='display:inline;' name = 'neuabsender_email' value ='".$Absender_Email."' size ='25' maxlength ='100'>&nbsp;&nbsp;</td>";


  if ($PushToken != "meinToken" AND  $PushToken != "" AND  $PushToken != "PushToken") echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  echo " align=right>\n<b>&nbspPush-Application-Token: <input type='text' style='display:inline;' name = 'neupushtoken' value ='".$PushToken."' size ='12' maxlength ='50'>&nbsp;&nbsp;&nbsp;</td>";

  echo "</table><br>";
  }


//Watchdog
  echo'<table><td style="font-size:20px; color:blue;">'.$KAs[132].'</td>'; 
  echo'<td><details><summary><img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary><table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[35].'.</td></tr></table></details></td></table>';    

  echo "<table border='4' style='color:lightgray; background-color: #212f3c
;'><td><b>&nbsp;".$KAs[133].":&nbsp;</b></td>";

  if ($Watchdog == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";

  echo"<b>\n&nbspWatchdog:&nbsp;";
  echo "<select name='neuwatchdog'>";
  echo "<option value='aktiviert'";

  if ($Watchdog == "aktiviert") echo " selected";
  echo ">".$KAs[89]."</option>";
  echo "<option value='deaktiviert'";
  if ($Watchdog == "deaktiviert") echo " selected";
  echo ">".$KAs[33]."</option>";
  echo "</select>&nbsp;</td>";


  if (($GeneralAutowatch == "p") OR ($GeneralAutowatch == "m") OR ($GeneralAutowatch == "pm"))echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";

  echo"<b>\n&nbspGeneralAutoWatch:&nbsp;";
    if (($GeneralAutowatch == "p" OR $GeneralAutowatch == "pm") AND ($PushToken == "meinToken" OR $PushToken == "" OR $PushToken == "PushToken") AND ($PushUser == "meinUser" OR $PushUser == "" OR $PushUser == "PushUser")) echo $KAs[134];
    echo "<select name='neugeneralautowatch'>";
     
     echo "<option value='p'";
     if ($GeneralAutowatch == "p") echo " selected";
     echo ">Push (Push-".$KAs[242];
     if ($MultiType == 0) echo "<a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a>";
     echo ")</option>";

      echo "<option value='m'";
     if ($GeneralAutowatch == "m") echo " selected";
     echo ">Mail (Mail-".$KAs[242];
     if ($MultiType == 0) echo "<a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a>";
     echo ")</option>";

      echo "<option value='pm'";
     if ($GeneralAutowatch == "pm") echo " selected";
     echo ">Push & Mail (".$KAs[242];
     if ($MultiType == 0) echo "<a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a>";
     echo ")</option>";

     echo "<option value='deaktiviert'";
     if ($GeneralAutowatch == "deaktiviert") echo " selected";
     echo ">".$KAs[33]."</option>";

     echo "</select>";

    echo"<b>\n&nbsp".$KAs[136].":&nbsp;";
    echo "<select name='neugeneralautowatchtime'>";
     
     echo "<option value='9'";
     if ($GeneralAutowatchTime == "9") echo " selected";
     echo ">9 ".$KAs[137]."</option>";

      echo "<option value='15'";
     if ($GeneralAutowatchTime == "15") echo " selected";
     echo ">15 ".$KAs[137]."</option>";

      echo "<option value='21'";
     if ($GeneralAutowatchTime == "21") echo " selected";
     echo ">21 ".$KAs[137]."</option>";

     echo "<option value='Alle'";
     if ($GeneralAutowatchTime == "Alle") echo " selected";
     echo ">".$KAs[135]."</option>";

    echo "</select></td>";

echo "</table><br>";




// Multi-Beep ---------------------------------------
if ($MultiType > 1)
  {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[101].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[17].'.</td></tr>
  </table>
</details></td></table>';     

  echo "<table border='4' style='color:black; background-color:#626567
  ;'><td style='background-color: white;'><img src='../beelogger_icons/bw_beep.png' height=30 width=30 style='margin-bottom:-4px';> <td><b>&nbsp;".$KAs[102]."&nbsp;</b></td>";
  for ($ib=1; $ib <= $MultiType; $ib++) 
    { 
    if ($BeepArray[$ib-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;''>";
    else echo "<td>";

    echo "<b>\n&nbsp; ".$KAs[103]." beelogger".$MultiSign.$ServerMultiNumber."_".$ib.": ";

    echo "<input type='checkbox' name='neubeep".$ib."' value='aktiviert'";
     if ($BeepArray[$ib] == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";
    
    if ($BeepArray[$ib-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;''>";
    else echo "<td>";
    
    echo "\n<b>&nbspBeep-ID".$ServerMultiNumber."_".$ib.": <input type='text' style='display:inline;' name = 'neubeepid".$ib."' value ='".$BeepIdArray[$ib-1]."' size ='12' maxlength ='100'>&nbsp;&nbsp;&nbsp;</td>";
    }
  echo "</table><br>"; 
  } // if ($MultiType > 1)
elseif ($MultiType == 1)
  {
      echo'<table><td style="font-size:20px; color:blue;">'.$KAs[101].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[17].'.
  </td></tr>
  </table>
</details></td></table>';
  echo "<table border='4' style='color:black; background-color:#626567;'><td style='background-color: white;'><img src='../beelogger_icons/bw_beep.png' height=30 width=30 style='margin-bottom:-4px';><td><b>&nbsp;".$KAs[102]."&nbsp;</b></td>";
  if ($Beep == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";
  echo "<b>\n&nbsp;";
  echo "<input type='checkbox' name='neubeep' value='aktiviert'";
     if ($Beep == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

  if ($Beep == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";
  echo "\n<b>&nbspBeep-ID: <input type='text' style='display:inline;' name = 'neubeepid' value ='".$BeepId."' size ='12' maxlength ='100'>&nbsp;&nbsp;&nbsp;</td></table><br>";
  }
else 
  { //UnterbeeloggerHinweis zur Kooperation
  echo'<span style="font-size:20px; color:blue;">'.$KAs[101].'</span>';  
  echo "<table border='2' style='color:black; background-color: white'><td align=middle><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';</td><td>&nbsp;".$KAs[241]."&nbsp;</td>";
  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";


  include ($WechselIniName);
  echo "<tr>";
  echo "<td align=middle><img src='../beelogger_icons/bw_beep.png' height=18  style='margin-bottom:-4px';></td>";
  echo "<td colspan=3 align=middle style='color:black; ";
  if ($BeepArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo "background-color: #a9dfbf;'>";
  else echo "background-color:#626567;'>";
  echo $KAs[249];
  if ($BeepArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo "&nbsp;".$KAs[250].": ".$BeepIdArray[$ServerMultiUnterordnerNummer-1]."&nbsp;".$KAs[252].".";
  else echo "&nbsp;".$KAs[251].".";
  echo "</td></tr>";
  include ("beelogger_ini.php");
  }

// Multi-iBeekeeper ---------------------------------------
if ($MultiType > 1)
  {
  echo "<table border='4' style='color:black; background-color:#626567
;'><td style='background-color: white;'><img src='../beelogger_icons/bw_ibeekeeper.png' height=30 style='margin-bottom:-4px';> <td><b>&nbsp;".$KAs[104]."&nbsp;</b></td>";
  for ($ib=1; $ib <= $MultiType; $ib++) 
    { 
    if ($iBeekeeperArray[$ib-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";

    echo "<b>\n&nbsp; ".$KAs[103]." beelogger".$MultiSign.$ServerMultiNumber."_".$ib.": ";

    echo "<input type='checkbox' name='neuibeekeeper".$ib."' value='aktiviert'";
     if ($iBeekeeperArray[$ib-1] == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";
    
    if ($iBeekeeperArray[$ib-1] == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;''>";
    else echo "<td>";
    
    echo "\n<b>&nbspiBeekeeper-UID".$ServerMultiNumber."_".$ib.": <input type='text' style='display:inline;' name = 'neuibeekeeperuid".$ib."' value ='".$iBeekeeperUIdArray[$ib-1]."' size ='17' maxlength ='100'>&nbsp;&nbsp;&nbsp;</td>";
    }
  echo "</table><br>"; 
  } // if ($MultiType > 1)

elseif ($MultiType == 1)
  {
  echo "<table border='4' style='color:black; background-color:#626567
;'><td style='background-color: white;'><img src='../beelogger_icons/bw_ibeekeeper.png' height=30  style='margin-bottom:-4px';><td><b>&nbsp;".$KAs[104]."&nbsp;</b></td>";
  if ($iBeekeeper == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";
  echo "<b>\n&nbsp;";
  echo "<input type='checkbox' name='neuibeekeeper' value='aktiviert'";
     if ($iBeekeeper == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

  if ($iBeekeeper == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
  else echo "<td>";
  echo "\n<b>&nbspiBeekeeperUId: <input type='text' style='display:inline;' name = 'neuibeekeeperuid' value ='".$iBeekeeperUId."' size ='12' maxlength ='100'>&nbsp;&nbsp;&nbsp;</td></table><br>";
  } 
else 
  { //Unterbeelogger i-beekeeper Hinweis
  include ($WechselIniName);
  echo "<tr>";
  echo "<td align=middle><img src='../beelogger_icons/bw_ibeekeeper.png' height=18  style='margin-bottom:-4px';></td>";
  echo "<td colspan=3 align=middle style='color:black; ";
  if ($iBeekeeperArray [$ServerMultiUnterordnerNummer-1] == "aktiviert") echo "background-color: #a9dfbf;'>";
  else echo "background-color:#626567;'>";
  echo $KAs[253];
  if ($iBeekeeperArray[$ServerMultiUnterordnerNummer-1] == "aktiviert") echo "&nbsp;".$KAs[254].": ".$iBeekeeperUIdArray[$ServerMultiUnterordnerNummer-1]."&nbsp;".$KAs[251].".";
  else echo "&nbsp;".$KAs[251].".";
  echo "</td></tr>";
  echo "</table><br>";
  include ("beelogger_ini.php");
  }


//Openweathermap
  if (!$CommunityUser)
  {
  echo'<table><td style="font-size:20px; color:blue;">'.$KAs[264].'</td>'; 
  echo'<td><details><summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[39].'.</td></tr>
  </table></details></td></table>';   

  if ($MultiType >= 1)
    {
    echo "<table border='4' style='color:black; background-color:#626567
    ;'>";
    if ($OpenweathermapKey != "") echo "<tr style='color:black; background-color: #a9dfbf;'>";
    else echo "<tr>";
    echo"<td style='background-color: white;'><img src='../beelogger_icons/n_Wetter (Schnee-Hagel-Wind).png' height=30  style='margin-bottom:-4px';><td style='color:black; background-color:#626567;'><b>&nbsp;".$KAs[265]."&nbsp;&nbsp;</b></td>";

    echo "<td>\n<b>&nbspOpenweathermap-Key: <input type='text' style='display:inline;' name = 'neuopenweathermapkey' value ='".$OpenweathermapKey."' size ='12' maxlength ='40'>&nbsp;&nbsp;&nbsp;</td>";

    echo "<td><b>\n&nbsp;".$KAs[267].": ";
    echo "<select name='neuwettericons'>";
    echo "<option value='1'";

    if ($WetterIcons == "") $WetterIcons = "1";//init

    if ($WetterIcons == "1") echo " selected";
    echo ">beeloggerIcons</option>";

    echo "<option value='2'";
    if ($WetterIcons == "2") echo " selected";
    echo ">Openweathermap-Icons</option>";
    echo "</select></td>";
    echo"</tr></table><br>";
    } 
  }
else //CommunityUser!
  { 
  echo'<table><td style="font-size:20px; color:blue;">'.$KAs[266].'</td>'; 
echo '</table>';   

    echo "<table border='4' style='color:black; background-color:#626567
    ;'>";
    echo"<td style='background-color: white;'><img src='../beelogger_icons/n_Wetter (Schnee-Hagel-Wind).png' height=30  style='margin-bottom:-4px';></td>";

    echo "<td><b>\n&nbsp;".$KAs[267].": ";
    echo "<select name='neuwettericons'>";
    echo "<option value='1'";

    if ($WetterIcons == "") $WetterIcons = "1";//init

    if ($WetterIcons == "1") echo " selected";
    echo ">beeloggerIcons</option>";
    
    echo "<option value='2'";
    if ($WetterIcons == "2") echo " selected";
    echo ">Openweathermap-Icons</option>";
    echo "</select>&nbsp;&nbsp;</td>";

    // if ($ExWetterDaten) echo "<td style='color:black; background-color: #a9dfbf;'>";
    // else echo "<td>";
    // echo "&nbsp;&nbsp;Alte Wetterdaten als Sensoren anzeigen: <input type='checkbox' name='neuexwetterdaten'";
    // if ($ExWetterDaten) echo " checked";
    // echo ">&nbsp;</td>";



    echo"</tr></table><br>";
  }


if (file_exists("../mobileWatch_ini.php")) include("../mobileWatch_ini.php");//Abwärtskomp
if (file_exists("../general_ini.php")) include("../general_ini.php");


// --------------------------- beelogger Map  ------------------------- 

if ($MultiType == 0) //nur Hinweis
  { 
  echo'<span style="font-size:20px; color:blue;">'.$KAs[105].'</span>';  
  echo "<table border='2' style='color:black; background-color: white'><td align=middle><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';</td><td>&nbsp;".$KAs[241]."&nbsp;</td>";
  echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";

  include ($WechselIniName);
  echo "<tr>";
  echo "<td><img src='../beelogger_icons/bw_map.png' width='18' height='18' style='margin-bottom:-4px';</td>";
  echo "<td colspan=3 align=middle style='color:black; ";
  if (($beeloggerMap2 == "aktiviert" OR $beeloggerMap == "aktiviert") AND $beeloggerMapWaage == $ServerMultiUnterordnerNummer) echo "background-color: #a9dfbf;'>";
  else echo "background-color:#626567;'>";
  echo $KAs[111];
  if (($beeloggerMap2 == "aktiviert" OR $beeloggerMap == "aktiviert") AND $beeloggerMapWaage == $ServerMultiUnterordnerNummer) echo "&nbsp;".$KAs[89].".";
  else echo "&nbsp;".$KAs[33].".";
  echo "</td></tr>";
  echo "</table><br>"; 
  include ("beelogger_ini.php");
  }

//Map 1  ------------------------------------------
if ($beeloggerMap2BeeID == "" AND $beeloggerMapId1 != "") // MAP2 noch nicht aktiviert UND Map1 angelegt
  {
  if ($MultiType > 0)
    {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[105].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
'.$HAs[18].'<br>beelogger '.$KAs[89].'  <img src="../beelogger_icons/map_aktiv.png" width="70" height="30"> beelogger '.$KAs[33].'  <img src="../beelogger_icons/map_inaktiv.png" width="70" height="30">
  </td></tr>
  </table>
</details></td></table>';  
 
         
    echo "<table border='4' style='color:lightgray; background-color: #4d5656 ;'>";
    if ($beeloggerMap == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'><img src='../beelogger_icons/bw_map.png' width='70' height='30'></td>";
      else echo "<td><img src='../beelogger_icons/bw_map.png' width='70' height='30'></td>";
      echo" <td><b>&nbsp;beeloggerMap&nbsp;</b></td>";

    if ((strlen($beeloggerMapId1) == 8) AND (strlen($beeloggerMapId2) == 6) AND ($beeloggerMapLocation != "") AND ($beeloggerMapLocation != "beelogger-Standort"))
      {//ID erhalten und Ort bereits gewählt
      if ($beeloggerMap == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
      else echo "<td>";
      echo "<b>\n&nbspbeeloggerMap: ";
      echo "<select name='neubeeloggermap'>";
      echo "<option value='aktiviert'";

      if ($beeloggerMap == "aktiviert") echo " selected";
      echo ">aktiviert</option>";
      echo "</select></td>";
      }

    $SendbeeloggerMapLocation = str_replace(" ", "_",$beeloggerMapLocation); // Leerzeichen mit "_" ersetzen

    if (($beeloggerMapLocation != "beelogger-Standort") AND ($beeloggerMapLocation != "")) 
      {
      echo "<td style='color:black; background-color: #a9dfbf;'>";
      echo "\n<b>&nbspStandort des beeloggers: ".html_entity_decode($beeloggerMapLocation)."    </td>";
      echo "<input type='hidden' name = 'neubeeloggermaplocation' value = '".$beeloggerMapLocation."'>";
    
      
      if ($MultiType > 1)
        {
        echo "<td style='color:black; background-color: #a9dfbf;'>";
        echo "<b>\n&nbspAuswahl Sende-beelogger: ".$beeloggerMapWaage;
        echo "</td>";
        echo "<input type='hidden' name = 'neubeeloggermapwaage' value = '".$beeloggerMapWaage."'>";
    
        } // if ($MultiType > 1)

      }

    if  ($beeloggerMapStatus == "[UPDATED]") echo "<td>Das beeloggerMap-Profil ist bereits vollständig ";

    echo "<td style='color:black; background-color: white'><a href='https://beelogger.de/?page_id=196999' target='_blank'>Link zur Map&nbsp;</a></td>";


    // versteckte Variablen
    echo "<input type='hidden' name = 'neubeeloggermapid1' value = '".$beeloggerMapId1."'>";
    echo "<input type='hidden' name = 'neubeeloggermapid2' value = '".$beeloggerMapId2."'>";
    echo "<input type='hidden' name = 'neubeeloggermapstatus' value = '".$beeloggerMapStatus."'>";
    echo "<input type='hidden' name = 'altbeeloggermaplocation' value = '".$beeloggerMapLocation."'>";
    echo "<input type='hidden' name = 'altbeeloggermap' value = '".$beeloggerMap."'>";
    echo "</table>";
    

    //Umstieg Map1 auf Map2 abfragen
    if ($beeloggerMap == "aktiviert")
      {
      echo "<table border='4' style='color:black; background-color:#e7846f;'>";
      echo "<td>";
      echo "\n<b>&nbsp".$KAs[238]."&nbsp;&nbsp;";
      echo'<input type="checkbox" value="yes" name="neubeeloggermap1tomap2"></input></td>';
      echo"<td><button type='submit' style='BACKGROUND-COLOR: #81BEF7;' name='config' value='1'><b>".$KAs[204]."</b></button>&nbsp;&nbsp;</td>";
      echo "</table><br>";
      }
    }  
  }
else // $beeloggerMap2BeeID == ""
  {  
  //MAP2 ----------------------------------------------------------------
  if ($MultiType > 0)
    {
        echo'<table><td style="font-size:20px; color:blue;">'.$KAs[105].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
'.$HAs[18].'
<br>beelogger aktiv  <img src="../beelogger_icons/map_aktiv.png" width="70" height="30"> beelogger inaktiv  <img src="../beelogger_icons/map_inaktiv.png" width="70" height="30">
  </td></tr>
  </table>
</details></td></table>';   
         
    echo "<table border='4' style='color:lightgray; background-color: #4d5656;'>";
    if ($beeloggerMap2 == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'><img src='../beelogger_icons/map_aktiv.png' width='100' height='40'></td>";
    else echo "<td><img src='../beelogger_icons/map_inaktiv.png' width='100' height='40'></td>";
      echo "<td align='middle'></b>&nbsp;<a href='https://beelogger.de/?page_id=196999' style='color:white;' target='_blank'>&nbsp".$KAs[118]."&nbsp;</a></td>";


    if ((strlen($beeloggerMap2ID) == 8) AND (strlen($beeloggerMap2Key) == 6 AND $beeloggerMap2BeeID != ""))
      {//ID bereits erhalten

      if ($beeloggerMap2 == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
      else echo "<td style='color:black; background-color: #e7846f;'>";

      if (file_exists("loc.php") AND ($beeloggerMap2Lat == "00.00" OR $beeloggerMap2Lat == "00.00" OR $beeloggerMap2Lat == "" OR $beeloggerMap2Lat == "")) 
      { //eventuellen GSM Standort übernehmen)
      include("loc.php");
      if ($beeloggerMap2Lat == "00.00" OR $beeloggerMap2Lat == "") $beeloggerMap2Lat = $lat;
      if ($beeloggerMap2Lon == "00.00" OR $beeloggerMap2Lon == "") $beeloggerMap2Lon = $lon;
      }


    $Vollstaendig = true;
    if ($beeloggerMap2Location == "") {$Fehlend .= " ".$KAs[107].", ";$Vollstaendig = false;}
    if ($beeloggerMap2Lat == "" OR $beeloggerMap2Lat == "00.00") {$Fehlend .= " ".$KAs[108].", ";$Vollstaendig = false;}
    if ($beeloggerMap2Lon == "" OR $beeloggerMap2Lon == "00.00") {$Fehlend .= " ".$KAs[109].", ";$Vollstaendig = false;}
    if ($beeloggerMap2BeeloggerType == "" OR $beeloggerMap2Connect == "") {$Fehlend .= $KAs[110];$Vollstaendig = false;}

    echo "<b>\n&nbsp".$KAs[111]."&nbsp;";
      
    if ($Vollstaendig ==  true)
      {
      echo "<select name='neubeeloggermap2'>";

      echo "<option value='aktiviert'";
      if ($beeloggerMap2 == "aktiviert") echo " selected";
      echo ">".$KAs[89]."</option>";
    
      echo "<option value='deaktiviert'";
      if ($beeloggerMap2 != "aktiviert") echo " selected";
      echo ">".$KAs[33]."</option>";
      echo "</select>&nbsp;";
      if ($beeloggerMap2 != "aktiviert") echo " - ".$KAs[127]."!";
      }
    else echo $KAs[112]."! <br>&nbsp".$KAs[113].": ".$Fehlend;
    echo"</td>";


    //beelogger ist deaktiviert ! Evtl alle Daten löschen?
    if ($beeloggerMap2 == "deaktiviert" OR $beeloggerMap2 == "")
      {
      echo "<tr><td colspan=2>&nbsp;".$KAs[230]."?</td>";
      echo "<td><select name='beeloggermap2loeschebeeid'>";

      echo "<option value='Nein' selected>&nbsp;".$KAs[10]."&nbsp;</option>";
    
      echo "<option value='loeschen' style='color:red';>&nbsp;".$KAs[231]."</option>";
      echo "</select>&nbsp</td>";

      echo"<tr>";
      }

    if ($MultiType > 1)
      {
      echo "<tr><td>";
      echo "<b>\n&nbsp".$KAs[117]."</td>";
      echo "<td><select name='neubeeloggermapwaage'>";

      for ($i=1; $i <= $MultiType; $i++) 
        { 
        echo "<option value='".$i."'";
        if ($beeloggerMapWaage == $i) echo " selected";
        echo ">beelogger".$MultiSign.$ServerMultiNumber."_".$i."</option>";
        }
   
      echo "</select></td><td style='color:black; background-color: white'>  ".$KAs[114].".</td></tr>";
      } // if ($MultiType > 1)


    echo"<tr>";
    if ($beeloggerMap2Location == "" AND $Bienenvolkbezeichnung != "auto") $beeloggerMap2Location = $Bienenvolkbezeichnung;
    if ($beeloggerMap2Location == "") 
      {
      $pfad = getcwd(); //  kompletter Verzeichnispfad
      $verz = strrchr($pfad, "/"); // Verzeichnisname einschließlich des / zu Beginn
      $beeloggerMap2Location = str_replace("/","",$verz);//  / noch entfernt 
      }

    echo"<td>".$KAs[107]."&nbsp;</td>";
  
    if ($beeloggerMap2Location != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td style='color:black; background-color: #e7846f;'>";
    echo"<input type='text' style='display:inline;' name = 'neubeeloggermap2location' value ='".html_entity_decode($beeloggerMap2Location)."' size ='20' maxlength ='20'>&nbsp;&nbsp;&nbsp;</td>";
    echo "<td style='color:black; background-color: white'>  ".$KAs[115].".</td>";
    echo "</tr>";


    if ($beeloggerMap2Lat == "") $beeloggerMap2Lat = "00.00";
    if ($beeloggerMap2Lon == "") $beeloggerMap2Lon = "00.00";
    echo"<td>".$KAs[108]."&nbsp;</td>";
    if ($beeloggerMap2Lat != "" AND $beeloggerMap2Lat != "00.00") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td style='color:black; background-color: #e7846f;'>";

    $LatLonInfo = ""; //INIT
    if (file_exists("loc.php") AND $beeloggerMap2Lat == $lat) $LatLonInfo = $KAs[228];
    echo "<input style='display:inline;' type='text' title='".$KAs[120]."' name='neubeeloggermap2lat' value ='".number_format($beeloggerMap2Lat,2,'.','')."' pattern='[0-9\-]{1,3}[.]{1}[0-9]{1,2}'  size ='6' maxlength ='6' >&nbsp;".$LatLonInfo."</td>";
    echo '<td rowspan= "2" style="color:black; background-color: white">  '.$KAs[116].'&nbsp;';
    echo "<a href='https://nominatim.openstreetmap.org' target='_blank'>OpenStreetMaps</a>";
    echo $KAs[119].'.</td></tr>';

    echo"<tr><td>".$KAs[109]."&nbsp;</td>";
    if ($beeloggerMap2Lon != "" AND $beeloggerMap2Lon != "00.00") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td style='color:black; background-color: #e7846f;'>";
    $LatLonInfo = ""; //INIT
    if (file_exists("loc.php") AND $beeloggerMap2Lat == $lat) $LatLonInfo = $KAs[228];
    echo"<input style='display:inline;' type='text' title='".$KAs[120]."' name='neubeeloggermap2lon' value ='".number_format($beeloggerMap2Lon,2,'.','')."' pattern='[0-9\-]{1,3}[.]{1}[0-9]{1,2}' size ='6' maxlength ='6' >&nbsp;".$LatLonInfo."</td>";
    echo "</tr>";
    echo "<tr>";
    echo"<td rowspan='2'>".$KAs[110]." </td>";
    if ($beeloggerMap2BeeloggerType != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td style='color:black; background-color: #e7846f;'>";
    echo "Typ&nbsp;";
    echo '<select name="beeloggermap2beeloggertype" size="1">';
    if ($beeloggerMap2BeeloggerType == "") echo '<option value="">'.$KAs[122].'</option>';

    echo '<option value="S"';
    if ($beeloggerMap2BeeloggerType == "S") echo " selected";
    echo '>Solar</option>
      <option value="U"';
    if ($beeloggerMap2BeeloggerType == "U") echo " selected";
    echo '>Universal</option>
      <option value="E"';
    if ($beeloggerMap2BeeloggerType == "E") echo " selected";
    echo '>EasyPlug/Funk</option>
    </select>';
    echo "</td>";
    echo"<td rowspan='2'style='color:black; background-color: white'>  ".$KAs[123].". </td>";
    echo "<tr>";
    if ($beeloggerMap2Connect != "" AND $beeloggerMap2Connect != "undefined") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td style='color:black; background-color: #e7846f;'>";
    echo $KAs[232].': <select name="beeloggermap2connect" size="1">';

    if ($beeloggerMap2Connect == "") echo '<option value="" selected>'.$KAs[122].'</option>';
    if ($beeloggerMap2Connect == "undefined") echo '<option value="undefined" selected>'.$KAs[122].'</option>';

    echo '<option value="G"';
    if ($beeloggerMap2Connect == "G") echo " selected";
    echo '>GSM</option>';
    echo '<option value="T"';
    if ($beeloggerMap2Connect == "T") echo " selected";
    echo '>LTE</option>';
    echo'  <option value="W"';
    if ($beeloggerMap2Connect == "W") echo " selected";
    echo '>WLAN</option>
      <option value="L"';
    if ($beeloggerMap2Connect == "L") echo " selected";
    echo '>LORA</option>
      <option value="A"';
    if ($beeloggerMap2Connect == "A") echo " selected";
    echo '>LAN</option>
      <option value="F"';
    if ($beeloggerMap2Connect == "F") echo " selected";
    echo '>Funk</option>
    </select>';
    echo "</td></tr>";

//------------------
    echo "<tr><td>URL&nbsp;</td>";

   if ($beeloggerMap2URL != "deaktiviert") echo "<td style='color:black; background-color: #a9dfbf;'";
   else echo "<td";

   echo ">&nbsp;".$KAs[239]."?&nbsp;";

   echo "<input type='checkbox' name='neubeeloggermap2url' value='aktiviert'";
   if ($beeloggerMap2URL != "deaktiviert") echo " checked";
     echo ">&nbsp;</td>";


   echo "<td style='color:black; background-color: white'>".$KAs[240];
    echo "</td></tr>";
//------------------
    if ($beeloggerMap2Email == "" AND $beeloggerMap2 == "" AND $Empfaenger_Email != "empfaenger@meineDomain.de") $beeloggerMap2Email = $Empfaenger_Email;
    echo"<tr><td>E-Mail&nbsp;</td><td><input type='email' name='neubeeloggermap2email' value ='".$beeloggerMap2Email."' size ='35'>&nbsp;&nbsp;</td><td style='color:black; background-color: white'>".$KAs[125]."</td>";
    echo "</tr></table>";


//----------------
    echo "<table border='4' style='color:lightgray; background-color: #4d5656;'>";
    echo "<tr><td>&nbsp;".$KAs[233]."&nbsp;&nbsp;</td>";

    if ($beeloggerMap2Sensoren[0] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo"&nbsp;<img src='../beelogger_icons/off_temp_o.png' width='25' height='25'>&nbsp;</td>"; 
    if ($beeloggerMap2Sensoren[0] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";


    echo "&nbsp;".$KAs[168]."&nbsp;";
    echo '<select name="neubeeloggermap2sensor1" size="1">';

    if ($beeloggerMap2Sensoren[0] == "") echo '<option value="" selected>'.$KAs[234].'?</option>';
    else echo '<option value="">'.$KAs[235].'!</option>';
    
    for ($s=0; $s < $AnzahlSensoren ; $s++) 
      {
      if (!is_numeric($Sensoren[$s*5]))
        {
        echo '<option value="'.$s.'"';
        if ($beeloggerMap2Sensoren[0] != "" AND $beeloggerMap2Sensoren[0] == $s) echo " selected";
        echo '>'.$Sensoren[$s*5].'</option>';
        }
      } 
    echo '</select></td>';

//----------------
    if ($beeloggerMap2Sensoren[1] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo"&nbsp;<img src='../beelogger_icons/off_hum_o.png' width='25' height='25'>&nbsp;</td>"; 
    if ($beeloggerMap2Sensoren[1] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";


    echo "&nbsp;".$KAs[170]."&nbsp;";
    echo '<select name="neubeeloggermap2sensor2" size="1">';

    if ($beeloggerMap2Sensoren[1] == "") echo '<option value="" selected>'.$KAs[234].'?</option>';
    else echo '<option value="">'.$KAs[235].'!</option>';
    
    for ($s=0; $s < $AnzahlSensoren ; $s++) 
      {
      if (!is_numeric($Sensoren[$s*5]))
        {
        echo '<option value="'.$s.'"';
        if ($beeloggerMap2Sensoren[1] != "" AND $beeloggerMap2Sensoren[1] == $s) echo " selected";
        echo '>'.$Sensoren[$s*5].'</option>';
        }
      } 
    echo '</select></td>';

    //----------------
    if ($beeloggerMap2Sensoren[2] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo"&nbsp;<img src='../beelogger_icons/off_press.png' width='25' height='25'>&nbsp;</td>"; 
    if ($beeloggerMap2Sensoren[2] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";


    echo "&nbsp;".$KAs[176]."&nbsp;";
    echo '<select name="neubeeloggermap2sensor3" size="1">';

    if ($beeloggerMap2Sensoren[2] == "") echo '<option value="" selected>'.$KAs[234].'?</option>';
    else echo '<option value="">'.$KAs[235].'!</option>';
    
    for ($s=0; $s < $AnzahlSensoren ; $s++) 
      {
      if (!is_numeric($Sensoren[$s*5]))
        {
        echo '<option value="'.$s.'"';
        if ($beeloggerMap2Sensoren[2] != "" AND $beeloggerMap2Sensoren[2] == $s) echo " selected";
        echo '>'.$Sensoren[$s*5].'</option>';
        }
      } 
    echo '</select></td>';

        //----------------
    if ($beeloggerMap2Sensoren[3] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
    echo"&nbsp;<img src='../beelogger_icons/off_rain.png' width='25' height='25'>&nbsp;</td>"; 
    if ($beeloggerMap2Sensoren[3] != "") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";


    echo "&nbsp;".$KAs[177]."&nbsp;";
    echo '<select name="neubeeloggermap2sensor4" size="1">';

    if ($beeloggerMap2Sensoren[3] == "") echo '<option value="" selected>'.$KAs[234].'?</option>';
    else echo '<option value="">'.$KAs[235].'!</option>';
    for ($s=0; $s < $AnzahlSensoren ; $s++) 
      {
      if (!is_numeric($Sensoren[$s*5]))
        {
        echo '<option value="'.$s.'"';
        if ($beeloggerMap2Sensoren[3] != "" AND $beeloggerMap2Sensoren[3] == $s) echo " selected";
        echo '>'.$Sensoren[$s*5].'</option>';
        }
      } 
    echo '</select></td>';
    echo "</tr>";

    echo "<tr><td style='color:black; background-color: #a9dfbf;' colspan=9>&nbsp;".$KAs[236]."</td></tr>";
    echo "<tr><td colspan=9 align=right><button type='submit' style='BACKGROUND-COLOR: #81BEF7;' name='config' value='1'><b>".$KAs[204]."</b></button></td></tr>";

    }
  else 
    {
    echo "<td>";
    echo "\n<b>&nbsp".$KAs[126]."&nbsp;&nbsp;";
    echo'<input type="checkbox" value="yes" name="neubeeloggermap2teilnahme"></input>';
    }


  // versteckte Variablen
  echo "<input type='hidden' name = 'neubeeloggermap2beeid' value = '".$beeloggerMap2BeeID."'>";
  echo "<input type='hidden' name = 'neubeeloggermap2status' value = '".$beeloggerMap2Status."'>";
  echo "</table><br>";
  } // Ende beeloggerMap
} //else $beeloggerMap2BeeID == ""



//Leergewichte
if ($MultiType < 2)  
  {
    echo'<table><td style="font-size:20px; color:blue;">'.$KAs[128].'</td>'; 
    echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><td>
  '.$HAs[19].'.</td>
  </table>
</details></td></table>';   
 

  echo "<table border='4' style='color:lightgray; background-color: #424949;'>";
  echo "\n<td rowspan=2><b>&nbsp".$KAs[129].":&nbsp;</b></td>\n";
  echo "\n<td  rowspan=2 style='background-color: white';>&nbsp<img src='../beelogger_icons/Beute.png' height=40 width=40 style='margin-bottom:-4px';>&nbsp</td>";
  if ($BeutenLeergewicht != "" AND $BeutenLeergewicht > 1) echo "<td style='color:black; background-color: #a9dfbf;'";
  else echo "<td";
  echo "  rowspan=2><b>&nbsp".$KAs[130]."&nbsp; <input type='text' style='display:inline;' name = 'neubeutenleergewicht' value ='".$BeutenLeergewicht."' size ='2' maxlength ='5'>&nbsp;".$GewichtsEinheit."&nbsp;</b></td>";
  

  if (($Honigraeume[0]+$Honigraeume[2]) > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo " rowspan=2><b>&nbsp+&nbsp;</b></td>";
      if (($Honigraeume[0] > 0 AND $Honigraeume[1]) != "") echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo "><b><input type='number' style='width:30px;' min='0' max='5' step='1' style='display:inline;' name = 'neuhonigraum1anzahl' value='".$Honigraeume[0]."'>";
      echo "\n&nbspx&nbsp".$KAs[131]."&nbsp;<input type='text' style='display:inline;' name = 'neuhonigraum1leergewicht' value ='".$Honigraeume[1]."' size ='2' maxlength ='5'>&nbsp;".$GewichtsEinheit."&nbsp;</b></td>";
      
     
             
      if ($BeutenUtils[0][0] == 1  AND $BeutenUtils[0][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo " rowspan=2><b>&nbsp+&nbsp;</b></td>";
      echo "\n<td  rowspan=2 style='background-color: white';>&nbsp<img src='../beelogger_icons/n_Varoaeinschub.png' height=40 width=40 style='margin-bottom:-4px';>&nbsp</td>";
      if ($BeutenUtils[0][0] == 1  AND $BeutenUtils[0][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo " rowspan=2><input type='checkbox' name='neuutil1' value='1'";
      if ($BeutenUtils[0][0] == 1) echo " checked";
      echo ">\n&nbsp".$KAs[269]."&nbsp;<input type='text' style='display:inline;' name = 'neuutil1leergewicht' value ='".$BeutenUtils[0][1]."' size ='2' maxlength ='5'>&nbsp;".$GewichtsEinheit."</b>";
     echo "&nbsp;</td>";

      if ($BeutenUtils[1][0] == 1  AND $BeutenUtils[1][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo " rowspan=2><b>&nbsp+&nbsp;</b></td>";
      echo "\n<td  rowspan=2 style='background-color: white';>&nbsp<img src='../beelogger_icons/n_Absperrgitter.png' height=40 width=40 style='margin-bottom:-4px';>&nbsp</td>";
      if ($BeutenUtils[1][0] == 1  AND $BeutenUtils[1][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo "  rowspan=2><input type='checkbox' name='neuutil2' value='1'";
      if ($BeutenUtils[1][0] == 1) echo " checked";
      echo ">\n&nbsp".$KAs[270]."&nbsp;<input type='text' style='display:inline;' name = 'neuutil2leergewicht' value ='".$BeutenUtils[1][1]."' size ='2' maxlength ='5'>&nbsp;".$GewichtsEinheit."</b>";
      echo "&nbsp;</td>";

      if ($BeutenUtils[2][0] == 1  AND $BeutenUtils[2][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo " rowspan=2><b>&nbsp+&nbsp;</b></td>";
      echo "\n<td  rowspan=2 style='background-color: white';>&nbsp<img src='../beelogger_icons/n_Futterzarge.png' height=40 width=40 style='margin-bottom:-4px';>&nbsp</td>";
      if ($BeutenUtils[2][0] == 1  AND $BeutenUtils[2][1] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo "  rowspan=2><input type='checkbox' name='neuutil3' value='1'";
      if ($BeutenUtils[2][0] == 1) echo " checked";
      echo ">\n&nbsp".$KAs[271]."&nbsp;<input type='text' style='display:inline;' name = 'neuutil3leergewicht' value ='".$BeutenUtils[2][1]."' size ='4' maxlength ='5'>&nbsp;".$GewichtsEinheit."</b>";
     echo "&nbsp;</td>";




     echo "</tr><tr>";

      if ($Honigraeume[2] > 0 AND $Honigraeume[3] > 0) echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo "><b><input type='number' style='width:30px;' min='0' max='5' step='1' style='display:inline;' name = 'neuhonigraum2anzahl' value='".$Honigraeume[2]."'>";
      echo "\n&nbspx&nbsp".$KAs[131]."&nbsp;<input type='text' style='display:inline;' name = 'neuhonigraum2leergewicht' value ='".$Honigraeume[3]."' size ='2' maxlength ='5'>&nbsp;".$GewichtsEinheit."&nbsp;</b></td>";
      echo "</tr>";



    echo "</table><br>";    
  }




// Akkuspannung

echo'<table><td style="font-size:20px; color:blue;">'.$KAs[148].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>
    '.$HAs[20].'.</td></tr>
  </table>
</details></td></table>';
    //echo'<span style="font-size:20px; color:blue;">'.$KAs[148].'</span>';

if ($MultiType == 0) 
    {
    include($WechselIniName);
    echo "<table border='2' style='color:black; background-color:white'><td><img src='../beelogger_icons/off_no.png' width='18' height='18' style='margin-bottom:-4px';</td><td  colspan=4>&nbsp;".$KAs[241]."&nbsp;</td>";
    echo "<td><a href='".$NeubeeloggerURL."'>&nbsp;".$MultiTypeName.$ServerMultiNumber."&nbsp;</a></td>";

    echo "<tr style='color:lightgray; background-color: black;'><td style='background-color: white;'>";
    echo'<img src="../beelogger_icons/Akku25.png" height=18 width=34></td>';
    echo "\n<td align=right><b>&nbsp;".$KAs[149]."&nbsp;";
    echo $AkkuLeerSchwelle;
    echo "&nbsp;[V]&nbsp;&nbsp; </b></td>";
    echo "<td style='background-color: white;'>";
    echo'<img src="../beelogger_icons/Akku50.png" height=18 width=34></td>';
    echo "\n<td><b>&nbsp;".$KAs[150]."&nbsp;";
    echo $AkkuVollSchwelle."&nbsp;[V]&nbsp;&nbsp; </b></td><td style='background-color: white;'><img src='../beelogger_icons/Akku100.png' height=18 width=34></td>";
    if ($AkkuLeerSchwelle != 0 && $AkkuVollSchwelle != 0 && $LetztesVBatt != "")
              {
              echo "<td>&nbsp;".$KAs[272]." :".$LetztesVBatt."V&nbsp;</td>";  
              }
    else echo "<td></td>";          
    include("beelogger_ini.php"); //ReInit
    }
  else
    {
    //echo'<span style="font-size:20px; color:blue;">'.$KAs[148].'</span>';
    echo "<table border='4' style='color:lightgray; background-color: black;'>";
    echo "<td></td><td style='background-color: white;'>";
    echo'<img src="../beelogger_icons/Akku25.png" height=18 width=34></td>';
    echo "\n<td align=right><b>&nbsp;".$KAs[149]."&nbsp;<input type='text' style='display:inline;' name = 'neuakkuleerschwelle' value ='".$AkkuLeerSchwelle."' size ='2' maxlength ='4'>&nbsp;[V]&nbsp;&nbsp; </b></td>";
    echo "<td style='background-color: white;'>";
    echo'<img src="../beelogger_icons/Akku50.png" height=18 width=34></td>';
    echo "\n<td><b>&nbsp;".$KAs[150]."&nbsp; <input type='text' style='display:inline;' name = 'neuakkuvollschwelle' value ='".$AkkuVollSchwelle."' size ='2' maxlength ='4'>&nbsp;[V]&nbsp;&nbsp; </b></td>";
    echo "<td style='background-color: white;'>";
    echo'<img src="../beelogger_icons/Akku100.png" height=18 width=34></td>';

if ($AkkuLeerSchwelle != 0 && $AkkuVollSchwelle != 0 && $LetztesVBatt != "")
              {
              echo "<td>&nbsp;".$KAs[272].": ".$LetztesVBatt."V&nbsp;</td>";  
              }  

    echo '</tr>';
    echo "<tr><td rowspan=4>".$KAs[151].": </td><td colspan=2 align=right> beelogger-Solar (Li-Ion-".$KAs[152].") : 3,8 [V]&nbsp;&nbsp;</td><td></td><td align=right> 4.0 [V]&nbsp;&nbsp;</td><td></td>";

if ($AkkuLeerSchwelle != 0 && $AkkuVollSchwelle != 0 && $LetztesVBatt != "")
              { 
              echo "<td rowspan=4 style='background-color: white;'>";  
              if ($LetztesVBatt > $AkkuVollSchwelle) echo'&nbsp;<img src="../beelogger_icons/Akku100.png" width="100" height="50" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              elseif ($LetztesVBatt < $AkkuLeerSchwelle) echo'&nbsp;<img src="../beelogger_icons/Akku25.png" width="100" height="50" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              else echo'&nbsp;<img src="../beelogger_icons/Akku50.png" width="100" height="50" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              echo "&nbsp;</td>";
              }

    echo"</tr><tr><td colspan=2 align=right> beelogger-Universal (LiIon-".$KAs[152].") : 7.5 [V]&nbsp;&nbsp;</td><td></td><td align=right> 7.7 [V]&nbsp;&nbsp; 
        <td></td></td></tr><tr><td colspan=2 align=right> beelogger-Universal (6V-".$KAs[152].") : 5.9 [V]&nbsp;&nbsp;</td><td></td><td align=right> 6.1 [V]&nbsp;&nbsp; 
        <td></td></td></tr><tr><td colspan=2 align=right> beelogger-Universal (12V-".$KAs[152].") : 11.9 [V]&nbsp;&nbsp;</td><td></td><td align=right> 12.1 [V]&nbsp;&nbsp;</td><td></td></tr>";
    echo "</table><br><br>";
    }  
echo "</tr></table><br><br>";



//Grafikbereich Konfiguration                            
//echo'<span style="font-size:20px; color:blue;">'.$KAs[138].'</span>';
echo'<table><td style="font-size:20px; color:blue;">'.$KAs[138].'</td>'; 
echo'<td><details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[21].'.<img src="../beelogger_icons/n_Koenigin weiss gezeichnet.png" height=40 width=40 style="margin-bottom:-11px";></td></tr>
  </table>
</details></td></table>';

echo "<table border=5 style='color:white; background-color: black'>";
echo "\n<td><b>&nbsp;".$KAs[139].": <input type='text' style='display:inline;' name = 'neubienenvolkbezeichnung' value ='".html_entity_decode($Bienenvolkbezeichnung)."' size ='15' maxlength ='20'>&nbsp;</td>";


echo "<td><b>&nbsp;".$KAs[140].": <select name='neustandardcsvdatei'><option value='beelogger.csv'";
if ($StandardCSV == "beelogger.csv") echo " selected";
echo ">".$KAs[141]."</option>";

echo "<option value='month.csv'";
if ($StandardCSV == "month.csv") echo " selected";
echo ">".$KAs[142]."</option>";

echo "<option value='week.csv'";
if ($StandardCSV == "week.csv") echo " selected";
echo ">".$KAs[143]."</option>";

echo"</select>&nbsp;</td>";

echo "</table>";
echo "<table border=5 style='color:white; background-color: black;'>";
echo "<td style='background-color: white;'><img src='../beelogger_icons/n_Koenigin weiss gezeichnet.png' height=20 width=20 style='margin-bottom:-4px';></td>";
echo "<td>&nbsp;".$KAs[268]."&nbsp;</td>";

if ($KoeAnzeige != "deaktiviert" AND $KoeAnzeige != "") echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
echo "><b>&nbsp;".$KAs[258].": <select name='neukoeanzeige'>";

if ($KoeAnzeige == "") $KoeAnzeige = "deaktiviert"; //INIT

echo "<option value='deaktiviert'";
if ($KoeAnzeige == "deaktiviert") echo " selected";
echo ">".$KAs[33]."</option>";

echo "<option value='n_Koenigin blau gezeichnet.png'";
if ($KoeAnzeige == "n_Koenigin blau gezeichnet.png") echo " selected";
echo ">".$KAs[222]." (2020)</option>";

echo "<option value='n_Koenigin gruen gezeichnet.png'";
if ($KoeAnzeige == "n_Koenigin gruen gezeichnet.png") echo " selected";
echo ">".$KAs[215]." (2019)</option>";

echo "<option value='n_Koenigin rot gezeichnet.png'";
if ($KoenAnzeige == "n_Koenigin rot gezeichnet.png") echo " selected";
echo ">".$KAs[213]." (2018)</option>";

echo "<option value='n_Koenigin gelb gezeichnet.png'";
if ($KoeAnzeige == "n_Koenigin gelb gezeichnet.png") echo " selected";
echo ">".$KAs[219]." (2017)</option>";

echo "<option value='n_Koenigin weiss gezeichnet.png'";
if ($KoeAnzeige == "n_Koenigin weiss gezeichnet.png") echo " selected";
echo ">".$KAs[257]." (2016)</option>";

echo"</select>&nbsp;</td>";

if ($KoeAnzeige != "deaktiviert" AND $KoeAnzeige != "") echo "<td style='color:black; background-color: #a9dfbf;'";
      else echo "<td";
      echo ">&nbsp;Info:&nbsp;<input type='text' style='display:inline;' name = 'neukoeinfo' value ='".$KoeInfo."' size ='50' maxlength ='50'>&nbsp;</td>";

echo "</table>";



echo "<table border=5><tr><td>";
echo "<b>".$KAs[153]." ".$KAs[154];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[22].'.</td></tr>
  </table>
</details>';

echo "</td><td>".$KAs[155];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[23].'.
</td></tr>
  </table>
</details>';
echo"</td><td>".$KAs[156];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[24].'.
</td></tr>
  </table>
</details>';
echo "</td><td>".$KAs[157]."</td><td>".$KAs[158];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[25].'.
</td></tr>
  </table>
</details>';
echo"</td><td>".$KAs[159];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[26].'.
</td></tr>
  </table>
</details>';
echo "</td><td>".$KAs[160];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[27].'.
</td></tr>
  </table>
</details>';
echo "</td><td>".$KAs[161];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[34].'.
</td></tr>
  </table>
</details>';
echo"</td><td>".$KAs[162];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[28].'.
</td></tr>
  </table>
</details>';
echo"</td><td>".$KAs[163];
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[29].'.
</td></tr>
  </table>
</details>';
echo"</td><td>".$KAs[164]." ".$LetzteZeile[0]."</b>";
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[30].'.
</td></tr>
  </table>
</details>';
echo"</td></tr><tr>";

$SensorCounter = $AnzahlSensoren;
if ($Sensoren[($AnzahlSensoren-1)*5] == "Summe") $SensorCounter =  ($AnzahlSensoren-2);

for ($a=0;$a<$SensorCounter;$a++){ 

if ($Sensoren[$a*5+1] == "white") echo "<td style='color:black ; background-color: ".$Sensoren[$a*5+1].";''>".$KAs[165]." ".str_pad(($a+1), 2, 0, STR_PAD_LEFT);
else echo "\n<td style='color:white; background-color:".$Sensoren[$a*5+1].";'>".$KAs[165]." ".str_pad(($a+1), 2, 0, STR_PAD_LEFT);


echo ": <input type='text' name='sensor".($a+1)."' value ='".$Sensoren[$a*5]."' size ='18' maxlength ='30'></td>"; 


//Icon statt Sensorname anzeigen?
if ($UseIcon[$a] == "true") echo "<td style='color:#a9dfbf; background-color: #a9dfbf;'>";
else echo "<td style='color:#e7846f; background-color: #e7846f;'>";
echo"<input type='checkbox' value='true' name='useicon".($a+1)."'";
if ($UseIcon[$a] == "true") echo " checked";
echo">";
if (file_exists('../beelogger_icons/'.$Icon[$a]) AND $UseIcon[$a] == "true") echo"<img src='../beelogger_icons/off_".$Icon[$a]."' width='30' height='30' style='margin-bottom:-4px';";
echo"</td>"; 


//Iconauswahl 
echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";''>";
echo "<select name='icon".($a+1)."'>";

echo"<option value='temp_w.png'";
if ($Icon[$a] == "temp_w.png") echo " selected";
echo">".$KAs[166]."</option>";

echo"<option value='temp_i.png'";
if ($Icon[$a] == "temp_i.png") echo " selected";
echo">".$KAs[167]."</option>";

echo"<option value='temp_o.png'";
if ($Icon[$a] == "temp_o.png") echo " selected";
echo">".$KAs[168]."</option>";

echo"<option value='hum_i.png'";
if ($Icon[$a] == "hum_i.png") echo " selected";
echo">".$KAs[169]."</option>";

echo"<option value='hum_o.png'";
if ($Icon[$a] == "hum_o.png") echo " selected";
echo">".$KAs[170]."</option>";

echo"<option value='sun.png'";
if ($Icon[$a] == "sun.png") echo " selected";
echo">".$KAs[171]."</option>";

echo"<option value='weight.png'";
if ($Icon[$a] == "weight.png") echo " selected";
echo">".$KAs[172]."</option>";

echo"<option value='bat.png'";
if ($Icon[$a] == "bat.png") echo " selected";
echo">".$KAs[173]."</option>";

echo"<option value='solar.png'";
if ($Icon[$a] == "solar.png") echo " selected";
echo">".$KAs[174]."</option>";

echo"<option value='wifi.png'";
if ($Icon[$a] == "wifi.png") echo " selected";
echo">".$KAs[175]."</option>";

echo"<option value='press.png'";
if ($Icon[$a] == "press.png") echo " selected";
echo">".$KAs[176]."</option>";

echo"<option value='rain.png'";
if ($Icon[$a] == "rain.png") echo " selected";
echo">".$KAs[177]."</option>";

if ($bhome>0) {
  echo"<option value='info.png'";
  if ($Icon[$a] == "info.png") echo " selected";
  echo">".$KAs[178]."</option>";
}
else {
  echo"<option value='service.png'";
  if ($Icon[$a] == "service.png") echo " selected";
  echo">".$KAs[179]."</option>";
}

echo"<option value='bienen_in.png'";
if ($Icon[$a] == "bienen_in.png") echo " selected";
echo">".$KAs[180]."</option>";

echo"<option value='bienen_out.png'";
if ($Icon[$a] == "bienen_out.png") echo " selected";
echo">".$KAs[181]."</option>";

echo"<option value='honey.png'";
if ($Icon[$a] == "honey.png") echo " selected";
echo">".$KAs[182]."</option>";

echo"<option value='no.png'";
if ($Icon[$a] == "no.png" OR $Icon[$a] == "") echo " selected";
echo">".$KAs[33]."</option>";

echo"</select></td>";

//icons
if (!file_exists('../beelogger_icons/'.$Icon[$a]) OR $Icon[$a] == "") $Icon[$a] = "no.png";
echo"<td>";
if (file_exists('../beelogger_icons/'.$Icon[$a])) echo"<img src='../beelogger_icons/".$Icon[$a]."' width='30' height='30' style='margin-bottom:-4px';";
echo "</td>";

// Farbe des Sensors
echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";'>";
echo "<select name = 'farbe".($a+1)."'>";
 for ($f = 0; $f < sizeof($Farben);$f++){
   echo "<option style='color:".$RealFarben[$f]."'; value='".$RealFarben[$f]."'";
   if ($Sensoren[$a*5+1] == $RealFarben[$f]) echo " selected";
   echo ">".$Farben[$f]."</option>";
  } 
echo"</select></td>";


//Sensoranzeige direkt? true oder false
if ($Sensoren[$a*5+2] == "true") echo "<td style='color:#a9dfbf; background-color: #a9dfbf;'>";
else echo "<td style='color:#e7846f; background-color: #e7846f;'>";

echo"<input type='checkbox' value='true' name='anzeige".($a+1)."'";
if ($Sensoren[$a*5+2] == "true") echo " checked";
echo"></td>"; 


//Tageswertoption aktivieren true oder false
if ($TageswertOptionArray[$a] == "true" OR $a == 5) echo "<td style='color:#a9dfbf; background-color: #a9dfbf;'>";
else echo "<td style='color:#e7846f; background-color: #e7846f;'>";

echo"<input type='checkbox' value='true' name='tageswertoption".($a+1)."'";
if ($TageswertOptionArray[$a] == "true"  OR $a == 5) echo " checked";
echo"></td>"; 


//Achse
echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";'>";

//if ($a != "5") {
  echo "<select name='achse".($a+1)."'>";
   echo "<option value=";
   echo "'y'";
   if (strpos($Sensoren[$a*5+3],"'y'") !== false) echo " selected";
   echo ">y</option>";
      
   echo "<option value=";
   echo "'y2'";
   if (strpos($Sensoren[$a*5+3],"'y2'") !== false) echo " selected";
   echo ">y2</option>";
  echo'</select></td>';


//Einheit des Sensors
echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";''><input type='text' name = 'einheit".($a+1)."' value ='".$Sensoren[$a*5+4]."' size ='5' maxlength ='8'></td>"; 



//Sensorlöschen
if ($a > 12) echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";''><input type='checkbox' name='loeschen".($a+1)."' value='1'> </td>";
else echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";''></td>";


echo "<td style='color:white ; background-color: ".$Sensoren[$a*5+1].";''><input type='text' name='' value='".$LetzteZeile[$a+1]."' size ='4'>";
if ($MultiType == 2) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[167]."1",$KAs[167]."2",$KAs[169]."1",$KAs[169]."2",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 3) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[184]."3",$KAs[167]."1",$KAs[167]."2",$KAs[167]."3",$KAs[169]."1",$KAs[169]."2",$KAs[169]."3",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[185]." HX711(2)".$KAs[208]."-A",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 4) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[184]."3",$KAs[184]."4",$KAs[167]."1",$KAs[167]."2",$KAs[167]."3",$KAs[167]."4",$KAs[169]."1",$KAs[169]."2",$KAs[169]."3",$KAs[169]."4",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[185]." HX711(2)".$KAs[208]."-A",$KAs[185]." HX711(2)".$KAs[208]."-B",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 5) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[184]."3",$KAs[184]."4",$KAs[184]."5",$KAs[167]."1",$KAs[167]."2",$KAs[167]."3",$KAs[167]."4",$KAs[167]."5",$KAs[169]."1",$KAs[169]."2",$KAs[169]."3",$KAs[169]."4",$KAs[169]."5",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[185]." HX711(2)".$KAs[208]."-A",$KAs[185]." HX711(2)".$KAs[208]."-B",$KAs[185]." HX711(3)".$KAs[208]."-A",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 6) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[184]."3",$KAs[184]."4",$KAs[184]."5",$KAs[184]."6",$KAs[167]."1",$KAs[167]."2",$KAs[167]."3",$KAs[167]."4",$KAs[167]."5",$KAs[167]."6",$KAs[169]."1",$KAs[169]."2",$KAs[169]."3",$KAs[169]."4",$KAs[169]."5",$KAs[169]."6",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[185]." HX711(2)".$KAs[208]."-A",$KAs[185]." HX711(2)".$KAs[208]."-B",$KAs[185]." HX711(3)".$KAs[208]."-A",$KAs[185]." HX711(3)".$KAs[208]."-B",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 7) $Werte = array($KAs[166],$KAs[168],$KAs[170],$KAs[179],$KAs[171],$KAs[184]."1",$KAs[184]."2",$KAs[184]."3",$KAs[184]."4",$KAs[184]."5",$KAs[184]."6",$KAs[184]."7",$KAs[167]."1",$KAs[167]."2",$KAs[167]."3",$KAs[167]."4",$KAs[167]."5",$KAs[167]."6",$KAs[167]."7",$KAs[169]."1",$KAs[169]."2",$KAs[169]."3",$KAs[169]."4",$KAs[169]."5",$KAs[169]."6",$KAs[169]."7",$KAs[173],$KAs[174],$KAs[185]." HX711(1)".$KAs[208]."-A",$KAs[185]." HX711(1)".$KAs[208]."-B",$KAs[185]." HX711(2)".$KAs[208]."-A",$KAs[185]." HX711(2)".$KAs[208]."-B",$KAs[185]." HX711(3)".$KAs[208]."-A",$KAs[185]." HX711(3)".$KAs[208]."-B",$KAs[185]." HX711(4)".$KAs[208]."-A",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
elseif ($MultiType == 1) $Werte = array($KAs[167],$KAs[168],$KAs[169],$KAs[170],$KAs[171],$KAs[184],$KAs[173]."/".$KAs[180],$KAs[174]."/".$KAs[181],$KAs[179]."/".$KAs[183],$KAs[185]." HX711-".$KAs[208]."A",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");

else $Werte = array($KAs[167],$KAs[168],$KAs[169],$KAs[170],$KAs[171],$KAs[184],$KAs[173],$KAs[174],$KAs[179],$KAs[185]." HX711",$KAs[186]." Aux1",$KAs[186]." Aux2",$KAs[186]." Aux3");
echo $Werte[$a];
echo "</td></tr>";
}



//eventueller neuer Sensor
echo "\n<tr></tr><tr><td colspan=2><b>".$KAs[187].":</td></tr>";
echo "<tr><td>Sensor ".str_pad(($a+1), 2, 0, STR_PAD_LEFT).": <input type='text' name = 'sensorneu' value ='' size ='15' maxlength ='30'></td>";

echo"<td style='color:#e7846f; background-color: #e7846f;'><input type='checkbox' value='true' name='useiconneu' ></td>";


echo "<td>";
echo "<select name='iconneu'>";

echo"<option value='temp_w.png'";
if ($Icon[$a] == "temp_w.png") echo " selected";
echo">".$KAs[166]."</option>";

echo"<option value='temp_i.png'";
if ($Icon[$a] == "temp_i.png") echo " selected";
echo">".$KAs[167]."</option>";

echo"<option value='temp_o.png'";
if ($Icon[$a] == "temp_o.png") echo " selected";
echo">".$KAs[168]."</option>";

echo"<option value='hum_i.png'";
if ($Icon[$a] == "hum_i.png") echo " selected";
echo">".$KAs[169]."</option>";

echo"<option value='hum_o.png'";
if ($Icon[$a] == "hum_o.png") echo " selected";
echo">".$KAs[170]."</option>";

echo"<option value='sun.png'";
if ($Icon[$a] == "sun.png") echo " selected";
echo">".$KAs[171]."</option>";

echo"<option value='weight.png'";
if ($Icon[$a] == "weight.png") echo " selected";
echo">".$KAs[172]."</option>";

echo"<option value='bat.png'";
if ($Icon[$a] == "bat.png") echo " selected";
echo">".$KAs[173]."</option>";

echo"<option value='solar.png'";
if ($Icon[$a] == "solar.png") echo " selected";
echo">".$KAs[174]."</option>";

echo"<option value='wifi.png'";
if ($Icon[$a] == "wifi.png") echo " selected";
echo">".$KAs[175]."</option>";

echo"<option value='press.png'";
if ($Icon[$a] == "press.png") echo " selected";
echo">".$KAs[176]."</option>";

echo"<option value='rain.png'";
if ($Icon[$a] == "rain.png") echo " selected";
echo">".$KAs[177]."</option>";

if ($bhome>0) {
  echo"<option value='info.png'";
  if ($Icon[$a] == "info.png") echo " selected";
  echo">".$KAs[178]."</option>";
}
else {
  echo"<option value='service.png'";
  if ($Icon[$a] == "service.png") echo " selected";
  echo">".$KAs[179]."</option>";
}

echo"<option value='bienen_in.png'";
if ($Icon[$a] == "bienen_in.png") echo " selected";
echo">".$KAs[180]."</option>";

echo"<option value='bienen_out.png'";
if ($Icon[$a] == "bienen_out.png") echo " selected";
echo">".$KAs[181]."</option>";

echo"<option value='honey.png'";
if ($Icon[$a] == "honey.png") echo " selected";
echo">".$KAs[182]."</option>";

echo"<option value='no.png'";
if ($Icon[$a] == "no.png" OR $Icon[$a] == "") echo " selected";
echo">".$KAs[33]."</option>";

echo"</select></td>";

//icon
echo"<td>";
echo"<img src='../beelogger_icons/no.png' width='30' height='30' style='margin-bottom:-4px';";
echo "</td>";

echo "<td style='color:white ; background-color: black;'><select name='farbeneu'>";
 for ($f = 0; $f <= sizeof($Farben);$f++){
   echo "<option style='color:".$RealFarben[$f]."'; value='".$RealFarben[$f]."'";
   echo ">".$Farben[$f]."</option>";
 }
echo"</select></td>";

echo"<td style='background-color: #e7846f;'><input type='checkbox' name='anzeigeneu' value='true'></td>";

echo"<td style='background-color: #e7846f;'><input type='checkbox' name='tageswertoptionneu' value='true'></td>";

echo "<td>";
echo "<select name='achseneu'>";
 echo "<option value=";
 echo "'y'";
 echo ">y</option>";
 echo "<option value=";
 echo "'y2'";
 echo ">y2</option>";                          
echo'</select></td>';

echo "<td><input type='text' name = 'einheitneu' value ='[]' size ='5' maxlength ='10'></td>"; 
 echo "<td></td>";

  echo "<td ><input type='text' name='' value='".$LetzteZeile[$a+1]."' size ='4'>";
  echo $Werte[$a]."</td>";

echo "</tr><br><br>";

// direkte Tageswertanzeige an oder ausstellen
if ($MultiType < 2)
    {
    echo "<tr style='color:black ; background-color: #848484;'><td colspan=5>".$KAs[188].":</td>";

if ($TageswertAnzeige == "deaktiviert") echo "<td style='color:black; background-color: #e7846f;'>";
else echo "<td style='color:black ; background-color: #e7846f;'>";

echo "<select name='tageswertanzeige'>";

echo "<option value ='false' selected>".$KAs[33]."</option>";
$AnzahlOptionen = 0;
   for ($s = 0; $s < $AnzahlSensoren; $s ++)
      {
      if ($TageswertOptionArray[$s] == "true")
        {
        echo "<option value='".$s."'";
        if ($TageswertAnzeige == $s) echo " selected";
        echo ">".$Sensoren[$s*5]."</option>\n";
        $AnzahlOptionen ++;
        }
      }
 if ($AnzahlOptionen == 0) echo "<option value='5'>".$Sensoren[5*5]."</option>\n";

  echo "</select>";
  echo"<td></td>";
  echo "<td>";
  echo "<select name='fix'>";
  echo "<option value=";
  echo "'y2'";
  echo " selected";
  echo ">y2</option>";
  echo"</select> ".$KAs[194]."</td>";
  echo "<td colspan='3'></td>";
  echo "</tr>";
    }


    //Datenpunkte anzeigen
    echo "<tr></tr><tr style='color:black ; background-color: #A4A4A4;'><td colspan=5>".$KAs[189].":";
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[31].'?. 
</td></tr>
  </table>
</details>';
    echo"</td>";
if ($PunktAnzeige == "true") echo "<td style='color:black ; background-color: #a9dfbf;'>";
else echo "<td style='color:black ; background-color: #e7846f;'>";

    echo"<input type='checkbox' value='true' name='neupunktanzeige'";
    if ($PunktAnzeige == "true") echo " checked";
    echo"></td>";
       echo "<td colspan='5'></td>";
  

// Legende konfigurieren
    echo "<tr style='color:black; background-color: silver;'><td colspan=5 >".$KAs[190].":";
echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[32].'?
</td></tr>
  </table>
</details>';
    echo"</td>";

  if ($Legende == "folgend") echo "<td style='background-color: lightgrey;'>";
  else if ($Legende == "keine") echo "<td style='background-color: lightgrey;''>";
  else echo "<td style='color:black; background-color: #a9dfbf;'>";

  echo "<select name='neulegende'>";
    echo "<option value='immer'";
    if ($Legende == "immer") echo " selected";
    echo ">".$KAs[191]."</option>";
  echo "<option value='folgend'";
    if ($Legende == "folgend") echo " selected";
    echo ">".$KAs[192]."</option>";
  echo "<option value='keine'";
    if ($Legende == "keine") echo " selected";
    echo ">".$KAs[193]."</option>";
 echo "</select>&nbsp;</td>";
   echo "<td colspan=5></td>";
    echo "</tr>";


// RollPeriod konfigurieren
    echo "<tr style='color:black ; background-color: lightgray;'><td colspan=5>".$KAs[195]." :";
    echo '<details>
  <summary>
    <img src="../beelogger_icons/m_help.png" height=40 width=40 style="margin-bottom:-11px";>
  </summary>
  <table style="font-size:15px; color:'.$HelpColor.'; background-color: '.$HelpBackColor.';"><tr><td>'.$HAs[33].'.
</td></tr>
  </table>
</details>';
    echo"</td>";
  if (!isset ($RollPeriod)) $RollPeriod = 1;
  echo  "<td><input type='number' style='width:40px;' style='display:inline;' name ='rollperiod' value ='".$RollPeriod."' min='1' max='50' step='1'></b></td>"; 
   echo "<td colspan='5'></td>";
  echo "</tr>";


echo"</table><br><br>";

include("beelogger_ini.php"); //REINIT

//mobileWatch_konfig
echo'<span style="font-size:20px; color:blue;">'.$KAs[196].'</span>';
  echo "<table border='4' style='color:lightgray; background-color: #0a0a0f;'><td style='background-color: white;'><img src='../beelogger_icons/watch.png' height=30 width=20> ";
      if ($mobileWatch_Show == "aktiviert") echo "<td style='color:black; background-color: #a9dfbf;'>";
    else echo "<td>";
  echo "&nbsp;".$KAs[197]."?&nbsp;";

     echo "<input type='checkbox' name='neumobilewatch_show' value='aktiviert'";
     if ($mobileWatch_Show == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

     echo "<td>&nbsp;".$KAs[165]."1&nbsp;<select style='display:inline;' name = 'neumobilewatch_sensor1'>";

      for ($i=1; $i < $AnzahlSensoren+1; $i++) 
       {
       if (!is_numeric($Sensoren[($i-1)*5]))
         {  
         echo "<option value='".$i."'";
         if ($mW_sensor1 == $i) echo " selected";
         echo ">".$Sensoren[($i-1)*5]."</option>";
         }
       }
      echo "</select></td>";

    echo "<td><b>&nbsp;".$KAs[165]."2&nbsp;<select style='display:inline;' name = 'neumobilewatch_sensor2'>";
     for ($i=0; $i < ($AnzahlSensoren+1); $i++) 
      { 
      if ($i == 0) echo "<option value ='0' selected>".$KAs[33]."</option>";
      else 
        {
        if (!is_numeric($Sensoren[($i-1)*5]))
          {  
          echo "<option value='".$i."'";
          if ($mW_sensor2 == $i) echo " selected";
          echo ">".$Sensoren[($i-1)*5]."</option>";
          }
        }
      }
     echo "</select></td>";

     echo "<td><b>&nbsp;".$KAs[229]."&nbsp;<input type='number' style='width:45px;' style='display:inline;' name = 'neumobilewatch_roll' value ='".$mW_roll."' min='0' max='50' step='1'></b></td>";

     
     echo "<td>&nbsp;".$KAs[188]."?&nbsp;";
     echo "<input type='checkbox' name='neumobilewatch_tageswertanzeige' value='aktiviert'";
     if ($mW_tageswertanzeige == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";


     echo "<td>&nbsp;".$KAs[190]."?&nbsp;<select name='neumobilewatch_legende'>";
     echo "<option value='always'";
     if ($mW_legende == "always" OR $mW_legende == "") echo " selected";
     echo ">".$KAs[191]."</option>";
     echo "<option value='follow'";
     if ($mW_legende == "follow") echo " selected";
     echo ">".$KAs[192]."</option>";
     echo "<option value='never'";
     if ($mW_legende == "never") echo " selected";
     echo ">".$KAs[193]."</option>";
     echo '</select>&nbsp;</td>';


     echo "<td>&nbsp;".$KAs[26]."?&nbsp;";
     echo "<input type='checkbox' name='neumobilewatch_notes' value='aktiviert'";
     if ($mW_tageswertanzeige == "aktiviert") echo " checked";
     echo ">&nbsp;</td>";

     echo'</table>';



// mobileWatch-Konfiguration
if (file_exists("../mobileWatch_ini.php") OR file_exists("../general_ini.php"))
  {
  if (file_exists("../mobileWatch_ini.php")) include("../mobileWatch_ini.php");//Abwärtskomp
  if (file_exists("../general_ini.php")) include("../general_ini.php");
  
  echo "</table><table border='4' style='color:lightgray; background-color: #0a0a0f;'><td><b>&nbsp;".$KAs[198].": &nbsp;</b></td>";  
  echo "<td>&nbsp;".$KAs[199].":&nbsp;<select name='neumobilewatch_sort'>";    
  echo "<option value='Ordner'";
  if ($mW_sort == "Ordner") echo " selected";
  echo ">".$KAs[200]."</option>";
  echo "<option value='Bienenvolkbezeichnung'";
  if ($mW_sort == "Bienenvolkbezeichnung") echo " selected";
  echo ">".$KAs[139]."</option>";
  echo "</select>&nbsp;</td>";  
  
  echo "<td><b>&nbsp;".$KAs[201]."&nbsp;<input type='number' style='width:45px;' style='display:inline;' name = 'neumobilewatch_tage' value ='".$mW_tage."' min='1' max='90' step='1'></b></td>";
  echo "<td><b>&nbsp;".$KAs[202]."&nbsp;<input type='number' style='width:35px;' style='display:inline;' name = 'neumobilewatch_spalten' value ='".$mW_spalten."' min='1' max='2' step='1'></b></td>"; 
  echo '</table><br><br><br>';
  }
 else echo '</table><br><br><br>';
//mobileWatch_konfig

echo "<input type='hidden' name = 'alteanzahlsensoren' value = '".$SensorCounter."'>";
echo "<input type='hidden' name = 'passwort' value = '".$Passwort."'>";

echo"<table>"; 
//Symbole: Daten speichern und zurück zur Show
echo '<td align="center"><img src="../beelogger_icons/show.png" height="50" >
<img src="../beelogger_icons/arrow.png" height="50" ></td>';

//Symbole: Daten speichern und erneut Konfig aufrufen
echo '<td align="center"><img src="../beelogger_icons/konfig1.png" height="50" ><img src="../beelogger_icons/konfig.png" value="1" height="50" ></td>';

//Symbole: Daten verwerfen und zurück zur Show
echo '<td align="center"><img src="../beelogger_icons/show.png" height="50" ><img src="../beelogger_icons/cancel.png" height="50" ></td></tr>';

echo"<tr><td><button type='submit' style='BACKGROUND-COLOR: #a9dfbf;' name='configsichern' value='1'><b>".$KAs[203]."</b></button>&nbsp;&nbsp;</td>";

echo "<input type='hidden' name = 'configsichern' value = '1'>";
echo"<td><button type='submit' style='BACKGROUND-COLOR: #81BEF7;' name='config' value='1'><b>".$KAs[204]."</b></button>&nbsp;&nbsp;</td>";

echo"<td><button type='submit' style='BACKGROUND-COLOR: red;' name='configsichern' value='0'><b>".$KAs[205]."</b></button>&nbsp;&nbsp;</td>";

echo "<td>&nbsp;&nbsp;".$KAs[206].": ".$Softwareversion;

$SoftwareversionNummer = (str_replace("M.","",$Softwareversion));
$AktuelleServerversion = file_get_contents('https://www.community.beelogger.de/AktuelleSkriptVersion.php');
$AktuelleServerversionNummer = (str_replace("M.", "", $AktuelleServerversion));
if ($AktuelleServerversionNummer > $SoftwareversionNummer) echo "
<a href='https://www.community.beelogger.de/aktuelleWebserverskripte.zip'>".$KAs[207]."(".$AktuelleServerversion.")</a>";

echo '</form></td></tr></table></div>';

