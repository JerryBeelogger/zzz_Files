<?php
/*
 * (C) 2020 Jeremias Bruker & Thorsten Gurzan - beelogger.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses>.
*/

// beelogger.de - Arduino Datenlogger für Imker
// Erläuterungen dieses Programmcodes unter https://beelogger.de

$Softwareversion = "M.15";//vom 5.12.2020 - beelogger_log.php 


$Sprache = 1; // INIT falls noch keine Speicherung stattfand
if (file_exists("../general_ini.php")) include("../general_ini.php");
if (file_exists("../beelogger_sprachfiles/Log_Sprache_".$Sprache.".php")) include ("../beelogger_sprachfiles/Log_Sprache_".$Sprache.".php"); // Sprache einbinden

error_reporting(0);
date_default_timezone_set('Europe/Berlin');
$beelogger = substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1);


    //Abwärtskompatibilität gewährleisten
    if (file_exists("beelogger.ini")) 
        {
        rename("beelogger.ini","beelogger_ini.php");
        unlink ("beelogger.ini");
        }  

    // Triple oder Quad-Ordner umwandeln oder erzeugen, falls noch nicht vorhanden
    if (strpos($beelogger,'Triple') !== FALSE) 
      {
      $Type = "T";
      $beeloggerUCounter = intval(substr(dirname(__FILE__), -1))*3-3;
      $beeloggerU = intval(substr(dirname(__FILE__), -1));
      }
    elseif (strpos($beelogger,'Quad') !== FALSE) 
      {
      $Type = "Q";
      $beeloggerUCounter = intval(substr(dirname(__FILE__), -1))*4-4;
      $beeloggerU = intval(substr(dirname(__FILE__), -1));
      }

    if ($Type == "T" OR $Type == "Q")
        { 
        for ($i=1; $i < 5; $i++) 
            { 
            if (!file_exists('../beelogger'.$Type.$beeloggerU."_".$i))
                {
                mkdir('../beelogger'.$Type.$beeloggerU."_".$i);
                copy("beelogger_show.php", "../beelogger".$Type.$beeloggerU."_".$i."/beelogger_show.php");
                if (file_exists('../beelogger'.$Type.($beeloggerUCounter+$i)))
                    {
                    $verzeichnis = opendir ('../beelogger'.$Type.($beeloggerUCounter+$i));
                    while ($file = readdir ($verzeichnis))  // Verzeichnis öffnen und auslesen
                        {
                        copy("../beelogger".$Type.($beeloggerUCounter+$i)."/$file","../beelogger".$Type.$beeloggerU."_".$i."/$file"); // Datei kopieren und löschen
                        unlink("../beelogger".$Type.($beeloggerUCounter+$i)."/$file");    
                        }
                        closedir($verzeichnis);
                        rmdir("../beelogger".$Type.($beeloggerUCounter+$i));
                    }
                }
               if ($Type == "T" AND $i == 3) $i++; // Triple: nach  3 Ordnern raus
            }
        }  

$ExternDataCounter = 0; //Init

if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php");
else echo $LAs[0]."!";

if (strpos($beelogger,'beelogger') !== FALSE) 
  {
  $MultiType = 1; //anzahl der Waagen pro beelogger
  $MultiTypeName = "Normal";
  $MultiSign = "";
  $ServerMultiNumber = intval(str_replace("beelogger","", $beelogger));
  }
elseif (strpos($beelogger,'Duo') !== FALSE) 
  {
  $MultiType = 2;
  $MultiTypeName = "Duo";
  $MultiSign = "D";
  $ServerMultiNumber = intval(str_replace("Duo","", $beelogger));
  }
elseif (strpos($beelogger,'Triple') !== FALSE) 
  {
  $MultiType = 3;
  $MultiTypeName = "Triple";
  $MultiSign = "T";
  $ServerMultiNumber = intval(str_replace("Triple","", $beelogger));
  }
elseif (strpos($beelogger,'Quad') !== FALSE) 
  {
  $MultiType = 4;
  $MultiTypeName = "Quad";
  $MultiSign = "Q";
  $ServerMultiNumber = intval(str_replace("Quad","", $beelogger));
  }
elseif (strpos($beelogger,'Penta') !== FALSE) 
  {
  $MultiType = 5;
  $MultiTypeName = "Penta";
  $MultiSign = "P";
  $ServerMultiNumber = intval(str_replace("Penta","", $beelogger));
  }
elseif (strpos($beelogger,'Hexa') !== FALSE) 
  {
  $MultiType = 6;
  $MultiTypeName = "Hexa";
  $MultiSign = "H";
  $ServerMultiNumber = intval(str_replace("Hexa","", $beelogger));
  }
elseif (strpos($beelogger,'Sept') !== FALSE) 
  {
  $MultiType = 7;
  $MultiTypeName = "Sept";
  $MultiSign = "S";
  $ServerMultiNumber = intval(str_replace("Sept","", $beelogger));
  }

// Zusatz TTN Lora POST jsonData
if (isset($_GET["LORA"]) && preg_match("#^[0-9\_]+$#",$_GET["LORA"]))
  { 
  $LORA = $_GET["LORA"];
  $postdataTTN = file_get_contents('php://input');
  $decodedTTN = json_decode($postdataTTN, true);


  if(is_array($decodedTTN))
    {
    if (isset($decodedTTN['payload_fields']['TempIn'])) {$ext_TempIn1M = $decodedTTN['payload_fields']['TempIn'];}
     if (isset($decodedTTN['payload_fields']['TempIn2'])) {$ext_TempIn2M = $decodedTTN['payload_fields']['TempIn2'];}
      if (isset($decodedTTN['payload_fields']['TempIn3'])) {$ext_TempIn3M = $decodedTTN['payload_fields']['TempIn3'];}
       if (isset($decodedTTN['payload_fields']['TempIn4'])) {$ext_TempIn4M = $decodedTTN['payload_fields']['TempIn4'];}    
    if (isset($decodedTTN['payload_fields']['TempOut'])) {$ext_TempOutM = $decodedTTN['payload_fields']['TempOut'];}  
    if (isset($decodedTTN['payload_fields']['FeuchteIn'])) {$ext_FeuchteIn1M = $decodedTTN['payload_fields']['FeuchteIn'];}
     if (isset($decodedTTN['payload_fields']['FeuchteIn2'])) {$ext_FeuchteIn2M = $decodedTTN['payload_fields']['FeuchteIn2'];}
      if (isset($decodedTTN['payload_fields']['FeuchteIn3'])) {$ext_FeuchteIn3M = $decodedTTN['payload_fields']['FeuchteIn3'];}
       if (isset($decodedTTN['payload_fields']['FeuchteIn4'])) {$ext_FeuchteIn4M = $decodedTTN['payload_fields']['FeuchteIn4'];}      
    if (isset($decodedTTN['payload_fields']['FeuchteOut'])) {$ext_FeuchteOutM = $decodedTTN['payload_fields']['FeuchteOut'];} 
    if (isset($decodedTTN['payload_fields']['Licht'])) {$ext_LichtM = $decodedTTN['payload_fields']['Licht'];} 
    if (isset($decodedTTN['payload_fields']['Gewicht'])) {$ext_Gewicht1M = $decodedTTN['payload_fields']['Gewicht'];} 
     if (isset($decodedTTN['payload_fields']['Gewicht2'])) {$ext_Gewicht2M = $decodedTTN['payload_fields']['Gewicht2'];}
      if (isset($decodedTTN['payload_fields']['Gewicht3'])) {$ext_Gewicht3M = $decodedTTN['payload_fields']['Gewicht3'];}
       if (isset($decodedTTN['payload_fields']['Gewicht4'])) {$ext_Gewicht4M = $decodedTTN['payload_fields']['Gewicht4'];}

    if (isset($decodedTTN['payload_fields']['VBatt'])) {$ext_VBattM = $decodedTTN['payload_fields']['VBatt'];}  
    if (isset($decodedTTN['payload_fields']['VSolar'])) {$ext_VSolarM = $decodedTTN['payload_fields']['VSolar'];}

    if (isset($decodedTTN['payload_fields']['BienenIn'])) {$ext_BienenInM = $decodedTTN['payload_fields']['BienenIn'];}  
    if (isset($decodedTTN['payload_fields']['BienenOut'])) {$ext_BienenOutM = $decodedTTN['payload_fields']['BienenOut'];}

    if (isset($decodedTTN['payload_fields']['Service'])) {$ext_ServiceM = $decodedTTN['payload_fields']['Service'];}
    if (isset($decodedTTN['payload_fields']['Aux1'])) {$ext_Aux1M = $decodedTTN['payload_fields']['Aux1'];}
    if (isset($decodedTTN['metadata']['gateways'][0]['rssi'])) {$ext_Aux2M = $decodedTTN['metadata']['gateways'][0]['rssi'];$ext_rssi = $ext_Aux2M;}
    if (isset($decodedTTN['payload_fields']['Aux3'])) 
      {
      $ext_Aux3M = $decodedTTN['payload_fields']['Aux3'];
      $NeubeeloggerSketchID = "LORA_".$ext_Aux3M;//SketchID ablegen
      }
    }  //  if(is_array($decodedTTN))
  } //if (isset($_GET["LORA"])
  
//Sensordaten Multibeelogger und beelogger 2019
if (isset($_GET["Passwort"]) && preg_match("#^[a-zA-Z0-9äöüÄÖÜ \!\-]+$#", $_GET["Passwort"])) $ext_Passwort = $_GET["Passwort"];
elseif (preg_match( "#^[a-zA-Z0-9äöüÄÖÜ \!\-]+$#", $_GET["PW"])) $ext_Passwort = $_GET["PW"];
   
if (preg_match( "#^[0-9 \-\.]+$#", $_GET["W"])) $ext_WaegTempM = $_GET["W"];

if (isset($_GET["TempIn"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["TempIn"])) $ext_TempIn1M = $_GET["TempIn"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["T1"])) $ext_TempIn1M = $_GET["T1"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T2"])) $ext_TempIn2M = $_GET["T2"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T3"])) $ext_TempIn3M = $_GET["T3"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T4"])) $ext_TempIn4M = $_GET["T4"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T5"])) $ext_TempIn5M = $_GET["T5"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T6"])) $ext_TempIn6M = $_GET["T6"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["T7"])) $ext_TempIn7M = $_GET["T7"];
if (isset($_GET["TempOut"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["TempOut"])) $ext_TempOutM = $_GET["TempOut"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["TO"])) $ext_TempOutM = $_GET["TO"];

if (isset($_GET["FeuchteIn"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["FeuchteIn"])) $ext_FeuchteIn1M = $_GET["FeuchteIn"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["F1"])) $ext_FeuchteIn1M = $_GET["F1"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F2"])) $ext_FeuchteIn2M = $_GET["F2"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F3"])) $ext_FeuchteIn3M = $_GET["F3"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F4"])) $ext_FeuchteIn4M = $_GET["F4"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F5"])) $ext_FeuchteIn5M = $_GET["F5"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F6"])) $ext_FeuchteIn6M = $_GET["F6"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["F7"])) $ext_FeuchteIn7M = $_GET["F7"];
if (isset($_GET["FeuchteOut"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["FeuchteOut"])) $ext_FeuchteOutM = $_GET["FeuchteOut"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["FO"])) $ext_FeuchteOutM = $_GET["FO"];
if (isset($_GET["Licht"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["Licht"])) $ext_LichtM = $_GET["Licht"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["L"])) $ext_LichtM = $_GET["L"];

if (isset($_GET["Gewicht"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["Gewicht"])) $ext_Gewicht1M = $_GET["Gewicht"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["G1"])) $ext_Gewicht1M = $_GET["G1"];
    if ($ext_Gewicht1M != "") $ext_MultiType = 1;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G2"])) $ext_Gewicht2M = $_GET["G2"];
    if ($ext_Gewicht2M != "") $ext_MultiType = 2;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G3"])) $ext_Gewicht3M = $_GET["G3"];
    if ($ext_Gewicht3M != "") $ext_MultiType = 3;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G4"])) $ext_Gewicht4M = $_GET["G4"];
    if ($ext_Gewicht4M != "") $ext_MultiType = 4;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G5"])) $ext_Gewicht5M = $_GET["G5"];
    if ($ext_Gewicht5M != "") $ext_MultiType = 5;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G6"])) $ext_Gewicht6M = $_GET["G6"];
    if ($ext_Gewicht6M != "") {
    $ext_MultiType = 6;
    $ext_HelpMultiType = 3;//Abwärtskompatibilität
    }
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G7"])) $ext_Gewicht7M = $_GET["G7"];
    if ($ext_Gewicht7M != "") $ext_MultiType = 7;
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["G8"])) $ext_Gewicht8M = $_GET["G8"];
    if ($ext_Gewicht8M != "") $ext_MultiType = 4;


if (isset($_GET["VBatt"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["VBatt"])) $ext_VBattM = $_GET["VBatt"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["VB"])) $ext_VBattM = $_GET["VB"];
if (isset($_GET["VSolar"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["VSolar"])) $ext_VSolarM = $_GET["VSolar"]; //Abwärtskompatibilität
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["VS"])) $ext_VSolarM = $_GET["VS"];

// Bereich falls ein beelogger-EasyPlug sendet
if (isset($_GET["I"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["I"])) $ext_BienenInM = $_GET["I"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["BienenIn"])) $ext_BienenInM = $_GET["BienenIn"];
if (isset($_GET["O"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["O"])) $ext_BienenOutM = $_GET["O"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["BienenOut"])) $ext_BienenOutM = $_GET["BienenOut"];

//Sicherheitsprüfung
if (isset($_GET["C"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["C"])) $ext_CheckM = $_GET["C"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["Check"])) $ext_CheckM = $_GET["Check"];    

    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["S"])) $ext_ServiceM = $_GET["S"];
    if (preg_match( "#^[0-9 \-\.]+$#", $_GET["SX"])) $ext_ServiceXM = $_GET["SX"];

//Aux Sensordaten
if (isset($_GET["A1"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["A1"])) $ext_Aux1M = $_GET["A1"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["Aux1"])) $ext_Aux1M = $_GET["Aux1"];
if (isset($_GET["A2"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["A2"])) $ext_Aux2M = $_GET["A2"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["Aux2"])) $ext_Aux2M = $_GET["Aux2"];   
if (isset($_GET["A3"]) && preg_match( "#^[0-9 \-\.]+$#", $_GET["A3"])) $ext_Aux3M = $_GET["A3"];
elseif (preg_match( "#^[0-9 \-\.]+$#", $_GET["Aux3"])) $ext_Aux3M = $_GET["Aux3"];

//Spezialfunktion zur Anzeige für externe Displays oder INFO
if ($_GET["showlast"] == "zeigen") $ext_ShowLast = "zeigen";
if (preg_match( "#^[0-1]+$#", $_GET["Show"])) $ext_Show = $_GET["Show"];

// beeloggerSketchID
if (isset($_GET["ID"]) && preg_match("#^[A-Z0-9\_]+$#",$_GET["ID"])) $NeubeeloggerSketchID = $_GET["ID"];

//Multidaten EE
    //Zeitsynchronisation mit EE-beelogger aktivieren
    if (isset($_GET["Z"]) && preg_match("#^[A-Z0-9\_]+$#",$_GET["Z"])) $TimeSync = $_GET["Z"];
    //Aux-Sensoren im EE-Block
    if (isset($_GET["A"]) && preg_match("#^[A-Z0-9\_]+$#",$_GET["A"])) $AuxImBlock = $_GET["A"];

    if($ext_M_Data == ""){if (isset($_GET["M7_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M7_Data"])) {$ext_M_Data = $_GET["M7_Data"];$ext_MultiType=7;}}

    if($ext_M_Data == ""){if (isset($_GET["M6_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M6_Data"])) {$ext_M_Data = $_GET["M6_Data"];$ext_MultiType=6;}}

    if($ext_M_Data == ""){if (isset($_GET["M5_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M5_Data"])) {$ext_M_Data = $_GET["M5_Data"];$ext_MultiType=5;}}

    if($ext_M_Data == ""){if (isset($_GET["M4_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M4_Data"])) {$ext_M_Data = $_GET["M4_Data"];$ext_MultiType=4;}}

    if($ext_M_Data == ""){if (isset($_GET["M3_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M3_Data"])) {$ext_M_Data = $_GET["M3_Data"];$ext_MultiType=3;}}

    if($ext_M_Data == ""){if (isset($_GET["M2_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M2_Data"])) {$ext_M_Data = $_GET["M2_Data"];$ext_MultiType=2;}}

    if($ext_M_Data == ""){if (isset($_GET["M1_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M1_Data"])) {$ext_M_Data = $_GET["M1_Data"];$ext_MultiType=1;}}

    if($ext_M_Data == ""){if (isset($_GET["M0_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["M0_Data"])) {$ext_M_Data = $_GET["M0_Data"];$ext_MultiType=1;}}

    //Abwärtskompatibilität 2018
    if($ext_M_Data == ""){if (isset($_GET["G_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["G_Data"])) {$ext_M_Data = $_GET["G_Data"];$ext_MultiType=1;}}
    if($ext_M_Data == ""){if (isset($_GET["T_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["T_Data"])) {$ext_M_Data = $_GET["T_Data"];$ext_MultiType=3;}}
    if($ext_M_Data == ""){if (isset($_GET["Q_Data"]) && preg_match("#^[0-9SX\/\_\-\:\,\.]+$#", $_GET["Q_Data"])) {$ext_M_Data = $_GET["Q_Data"];$ext_MultiType=4;}}


$timestamp=time();
$zeit = date("Y/m/d H:i:s");


if ($ext_Show=='1') 
  {
  echo '<!DOCTYPE html><html lang="de">
                <head>
                <meta http-equiv="content-type" content="text/html; charset=UTF-8"><title>'.$LAs[1].' beelogger_log.php</title></head><body>';
  }


if ($ext_Passwort == $BeeloggerLogPasswort)
    {
    $SendeIntervall = ServerZeitSteuerung();
      
    // M_Data Verarbeitung
    if ($ext_M_Data != "")
      {
      if ($MultiType == $ext_MultiType)
        {
        $EingehendeDatensätze=0; //Counter für Datensatzanzahl EE-Beelogger  
        $EE_beelogger = "EE"; //EE erkannt
        $Multi_Data_Array = explode(",",$ext_M_Data);
        $MultiArraySize = count($Multi_Data_Array);
        $ext_ServiceMsaved = $ext_ServiceM; //zwischenspeichern
        $ext_ServiceXMsaved = $ext_ServiceXM; //zwischenspeichern

        $G =0;
        while ($G < ($MultiArraySize-5))
            {
            $Multi_Data_Array[$G] = str_replace('_',' ', $Multi_Data_Array[$G]);  
            $zeit = $Multi_Data_Array[$G];
            $timestamp = strtotime($zeit);
            $G++;
            if ($MultiType > 1) {$ext_WaegTempM   = $Multi_Data_Array[$G];$G++;}
            $ext_TempIn1M    = $Multi_Data_Array[$G];$G++;
            if ($ext_MultiType > 1) {$ext_TempIn2M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 2) {$ext_TempIn3M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 3) {$ext_TempIn4M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 4) {$ext_TempIn5M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 5) {$ext_TempIn6M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 6) {$ext_TempIn7M    = $Multi_Data_Array[$G];$G++;}
            
            $ext_TempOutM    = $Multi_Data_Array[$G];$G++;

            $ext_FeuchteIn1M = $Multi_Data_Array[$G];$G++;
            if ($ext_MultiType > 1) {$ext_FeuchteIn2M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 2) {$ext_FeuchteIn3M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 3) {$ext_FeuchteIn4M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 4) {$ext_FeuchteIn5M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 5) {$ext_FeuchteIn6M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 6) {$ext_FeuchteIn7M    = $Multi_Data_Array[$G];$G++;}

            $ext_FeuchteOutM = $Multi_Data_Array[$G];$G++;

            $ext_LichtM      = $Multi_Data_Array[$G];$G++;

            $ext_Gewicht1M   = $Multi_Data_Array[$G];$G++;
            if ($ext_MultiType > 1) {$ext_Gewicht2M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 2) {$ext_Gewicht3M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 3) {$ext_Gewicht4M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 4) {$ext_Gewicht5M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 5) {$ext_Gewicht6M    = $Multi_Data_Array[$G];$G++;}
            if ($ext_MultiType > 6) {$ext_Gewicht7M    = $Multi_Data_Array[$G];$G++;}

            $ext_VBattM      = $Multi_Data_Array[$G];$G++;
            $ext_VSolarM     = $Multi_Data_Array[$G];$G++;
            if  ($G < ($MultiArraySize-(4+$MultiType*4))) 
              {
              $ext_ServiceM = $Multi_Data_Array[$G];$G++;
              if ($AuxImBlock == 1)
                {
                $ext_Aux1M = $Multi_Data_Array[$G];$G++;
                $ext_Aux2M = $Multi_Data_Array[$G];$G++;
                $ext_Aux3M = $Multi_Data_Array[$G];$G++;
                }
              Auswertung(0); //Auswertung ohne Serverantwort
              }  
            else //der letzte Datensatz ist erreicht
                {
                if ($ext_ServiceMsaved == "") {$ext_ServiceM = $Multi_Data_Array[$G];$G++;}
                else 
                    {
                    $G++; // Es liegt ein Wert im Array vor - wird verworfen  
                    $ext_ServiceM  = $ext_ServiceMsaved;
                    $ext_ServiceXM = $ext_ServiceXMsaved;
                    }
                if ($AuxImBlock == 1)
                  {
                  $ext_Aux1M = $Multi_Data_Array[$G];$G++;
                  $ext_Aux2M = $Multi_Data_Array[$G];$G++;
                  $ext_Aux3M = $Multi_Data_Array[$G];$G++;
                  }    
                Auswertung(1); //Auswertung mit Serverantwort
                ExternDataOut(); //zwischengespeichherte Daten an Beep.nl usw. senden
                Triggeralarme(); // Verarbeitung der Triggeralarme
                }            
            
            } //while
        } // if ($MultiType == $ext_MultiType)
        else echo "<br>".$LAs[2].": ".$MultiType." beelogger: ".$ext_MultiType;
      } //if ($ext_M_Data != "")    


      else   // normale Daten laufen ein - kein EE
        {
        $int_Check = round($ext_WaegTempM+$ext_TempIn1M+$ext_TempIn2M+$ext_TempIn3M+$ext_TempIn4M+$ext_TempIn5M+$ext_TempIn6M+$ext_TempIn7M+$ext_TempOutM+$ext_FeuchteIn1M+$ext_FeuchteIn2M+$ext_FeuchteIn3M+$ext_FeuchteIn4M+$ext_FeuchteIn5M+$ext_FeuchteIn6M+$ext_FeuchteIn7M+$ext_FeuchteOutM+$ext_LichtM+$ext_ServiceM+$ext_ServiceXM+$ext_Gewicht1M+$ext_Gewicht2M+$ext_Gewicht3M+$ext_Gewicht4M+$ext_Gewicht5M+$ext_Gewicht6M+$ext_Gewicht7M+$ext_Gewicht8M+$ext_VBattM+$ext_VSolarM+$ext_BienenInM+$ext_BienenOutM+$ext_Aux1M+$ext_Aux2M+$ext_Aux3M +0.5);

        if ( ((($int_Check - $ext_CheckM) < 2) AND (($int_Check - $ext_CheckM) > -2) AND $ext_CheckM != "" ) OR $ext_Show == "1" OR $LORA == 1)
            
            {//Checksummencheck überstanden
            if ($ext_Show == "1") echo"<br>".$LAs[3].": ".$int_Check."----> ".$LAs[4].": ".$ext_CheckM; //nur zur Info

            if ($ext_MultiType == $MultiType OR $ext_HelpMultiType == $MultiType) 
                {
                Auswertung(1);
                ExternDataOut(); //zwischengespeichherte Daten an Beep usw. senden
                Triggeralarme(); // Verarbeitung der Triggeralarme
                }
            else echo "<br>".$LAs[2].": ".$MultiType." beelogger: ".$ext_MultiType;
            }

        elseif ($ext_Show=='1') echo "<br><blockquote>".$LAs[5]."</blockquote>";
        else echo"Csum_error(".$int_Check.",".$ext_CheckM.")";
        } 

    } // ENDE  if ($ext_Passwort == $BeeloggerLogPasswort)

elseif ($ext_ShowLast == "zeigen")
        {
        $CSVFile = "week.csv";
        
        if (file_exists($CSVFile)) 
            { //Letzte Werte aus beelogger.csv auslesen
            $input = $CSVFile;
            $array = file($input);
            $j = sizeof($array);
              while ($j--)  
                {
                $what = trim($array[$j]);    
                $x = explode( ",", $what );
                $s = sizeof($x);
                if ($x[$s-1] !='')  
                  {      
                  break;
                  }      
                }
            echo $what; 
            echo "ok *";   
            } //if file_exists...beelogger.csv
        }//elseif ($ext_ShowLast == "zeigen")
else 
        {
        echo "".$LAs[6]."!";
        echo "</body></html>";
        }
    


function ServerZeitSteuerung()
    {    //Berechnung der Serversendesteuerung--------------------------
    if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php");
    if (fmod($SommerBeginn, 1) == 0.5) $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
    else $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn)."/1"));
    
    if ($WinterBeginn != "deaktiviert") 
      {
      if (fmod($WinterBeginn, 1) == 0.5) $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
      else $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn)."/1"));
      }

    if ($IntervallSendeSteuerung == "lichtgesteuert") 
      { //lichtgesteuerte Sendeintervalle
      if ($ext_Licht > 1) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
      else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 
    
      if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") >= $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute
      if ($ext_Licht == "") $IntervallSendeSteuerung = "zeitgesteuert"; // Lichtsteuerung ohne Werte geht nicht
      } // if lichtgesteuert

    if ($IntervallSendeSteuerung == "solarspannungsgesteuert") 
      { //solarspannungsgesteuerte Sendeintervalle
      if ($ext_VSolar > 0.3 ) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
      else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 
      
      if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") >= $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute
    
      if ($ext_VSolar == "") $IntervallSendeSteuerung = "zeitgesteuert"; // Spannungsteuerung ohne Werte geht nicht
      } // if solarspannungsgesteuert

    if ($IntervallSendeSteuerung == "zeitgesteuert") 
      { //zeitgesteuerte Sendeintervalle
      if ( (intval(date("G")) == ($SommerTagZeit-1)  AND intval(date("i")) >= 56) OR (intval(date("G")) >= $SommerTagZeit AND (intval(date("G")) < $SommerNachtZeit)) ) $SendeIntervall = $SommerSendeIntervallTag;
      else $SendeIntervall = $SommerSendeIntervallNacht;

      if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") >= $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall;
      } // if zeitgesteuert
      return $SendeIntervall;
    }

function Auswertung($Answer)
  {         
        if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php");
        $Klammern = array("[", "]");
        $GewichtsEinheit = str_replace($Klammern,"",$Sensoren[5*5+4]);  
        // Globale Variablen definieren

        global $NeubeeloggerSketchID,$MultiType,$MultiTypeName,$MultiSign,$ServerMultiNumber,$beelogger,$zeit,$timestamp,$Softwareversion,$ext_Show,$SendeIntervall,$ExternDataCounter,$ExternDataArray,$BeepArray,$BeepIdArray,$iBeekeeperArray,$iBeekeeperUIdArray,$TimeSync,$PushToken,$PushUser,$Empfaenger_Email,$Absender_Email; //Allgemeine globale Multi-Daten
        global $ext_WaegTempM,$ext_TempIn1M,$ext_TempIn2M,$ext_TempIn3M,$ext_TempIn4M,$ext_TempIn5M,$ext_TempIn6M,$ext_TempIn7M,$ext_TempOutM,$ext_FeuchteIn1M,$ext_FeuchteIn2M,$ext_FeuchteIn3M,$ext_FeuchteIn4M,$ext_FeuchteIn5M,$ext_FeuchteIn6M,$ext_FeuchteIn7M,$ext_FeuchteOutM,$ext_LichtM,$ext_Gewicht1M,$ext_Gewicht2M,$ext_Gewicht3M,$ext_Gewicht4M,$ext_Gewicht5M,$ext_Gewicht6M,$ext_Gewicht7M,$ext_VBattM,$ext_VSolarM,$ext_ServiceM,$ext_ServiceXM,$ext_BienenInM,$ext_BienenOutM,$ext_Aux1M,$ext_Aux2M,$ext_Aux3M,$ext_rssi,$LAs,$EE_beelogger,$TriggerAlarmArray,$EingehendeDatensätze;

        $umbruch = "\r\n";
        $tz = ",";
        $EingehendeDatensätze++;
//Temp
        if ($ext_WaegTempM == '99.9' OR $ext_WaegTempM == '85' OR $ext_WaegTempM == '') $ext_WaegTempM = '';
        elseif (!is_int($ext_WaegTempM)) $ext_WaegTempM = number_format($ext_WaegTempM, 2, '.', '');

        if ($ext_TempOutM == '99.9' OR $ext_TempOutM == '999.99' OR $ext_TempOutM == '') $ext_TempOutM = '';
        elseif (!is_int($ext_TempOutM)) $ext_TempOutM = number_format($ext_TempOutM, 2, '.', '');

        $ext_TempArray = array('',$ext_TempIn1M,$ext_TempIn2M,$ext_TempIn3M,$ext_TempIn4M,$ext_TempIn5M,$ext_TempIn6M,$ext_TempIn7M);
        for ($i=0; $i <= $MultiType; $i++) 
            { 
            if ($ext_TempArray[$i] == '99.9' OR $ext_TempArray[$i] == '999.99' OR $ext_TempArray[$i] == '') $ext_TempArray[$i] = '';
            elseif (!is_int($ext_TempArray[$i])) $ext_TempArray[$i] = number_format($ext_TempArray[$i], 2, '.', '');
            }   
//Feuchte
        if ($ext_FeuchteOutM == '999.99' OR $ext_FeuchteOutM < 0 OR $ext_FeuchteOutM == '') $ext_FeuchteOutM = '';    
        $ext_FeuchteArray = array('',$ext_FeuchteIn1M,$ext_FeuchteIn2M,$ext_FeuchteIn3M,$ext_FeuchteIn4M,$ext_FeuchteIn5M,$ext_FeuchteIn6M,$ext_FeuchteIn7M);
        
        for ($i=0; $i <= $MultiType; $i++) 
            { 
            if ($ext_FeuchteArray[$i] == '999.99' OR $ext_FeuchteArray[$i] < 0 OR $ext_FeuchteArray[$i] == '') $ext_FeuchteArray[$i] = '';
            elseif (!is_int($ext_FeuchteArray[$i])) $ext_FeuchteArray[$i] = number_format($ext_FeuchteArray[$i], 2, '.', '');
            }   
//Weitere
        if ($ext_LichtM == '-1' OR $ext_LichtM == '') $ext_LichtM = '';
        elseif (!is_int($ext_LichtM)) $ext_LichtM = number_format($ext_LichtM, 2, '.', '');

        if ($ext_VBattM == '999.99' OR $ext_VBattM == '') $ext_VBattM == '';
        elseif (!is_int($ext_VBattM)) $ext_VBattM = number_format($ext_VBattM, 2, '.', '');
        //if ($AkkuSpannungKorrektur != "") $ext_VBattM *= $AkkuSpannungKorrektur;

        if ($ext_VSolarM == '999.99' OR $ext_VSolarM == '') $ext_VSolarM == '';
        elseif (!is_int($ext_VSolarM)) $ext_VSolarM = number_format($ext_VSolarM, 2, '.', '');
        //if ($SolarSpannungKorrektur != "") $ext_VSolarM *= $SolarSpannungKorrektur;

        if ($ext_BienenInM == '-1') $ext_BienenInM = '';
        if ($ext_BienenOutM == '-1') $ext_BienenOutM = '';

//Aux 
        if ($ext_Aux1M == '-1' OR $ext_Aux1M == '') $ext_Aux1M = ''; //default Druck BME280
        elseif (!is_int($ext_Aux1M)) $ext_Aux1M = number_format($ext_Aux1M, 2, '.', '');
        if ($ext_Aux2M == '-1' OR $ext_Aux2M == '') $ext_Aux2M = ''; //default Regensensor
        elseif (!is_int($ext_Aux2M)) $ext_Aux2M = number_format($ext_Aux2M, 2, '.', '');
        if ($ext_Aux1M == '-1' OR $ext_Aux3M == '') $ext_Aux3M = ''; //nn
        elseif (!is_int($ext_Aux3M)) $ext_Aux3M = number_format($ext_Aux3M, 2, '.', '');


//Gewichte   
        $ext_GewichtMArray = ['',$ext_Gewicht1M,$ext_Gewicht2M,$ext_Gewicht3M,$ext_Gewicht4M,$ext_Gewicht5M,$ext_Gewicht6M,$ext_Gewicht7M];
        for ($i=1; $i <= $MultiType; $i++) 
            { 
            if ($ext_GewichtMArray[$i] == '-1' OR $ext_GewichtMArray[$i] == '999999' OR $ext_GewichtMArray[$i] == '') $ext_GewichtMArray[$i] = '';
            elseif (!is_int($ext_GewichtMArray[$i])) $ext_GewichtMArray[$i] = number_format($ext_GewichtMArray[$i], 2, '.', '');
            }

//Service-Variablen
        if ($ext_ServiceM == '-1') $ext_ServiceM = '';
        if ($ext_ServiceM > 10000) $ext_ServiceM = '99';
        if ($ext_ServiceXM == '-1') $ext_ServiceXM = '';

        if ($ext_ServiceXM != "") //Locationfunktion 
        {
        $loc = '<?php 
        $lat = "'.$ext_ServiceM.'";
        $lon = "'.$ext_ServiceXM.'";
        $LocDate = "'.$zeit.'";
        ?>';

        $aktion = fOpen('loc.php',"w");
                fWrite($aktion , $loc);
                fClose($aktion);
        $ext_ServiceM = ""; //  Werte sollen nicht in die CSV-Datei    
        } 
        elseif ($ext_ServiceM != "") $ext_ServiceM = round($ext_ServiceM,2);

// externe Temperaturkompensation für die Gewichtssensoren Multi

        if ($Korrekturwert != "") $KorrekturwertArray[0] = $Korrekturwert; //Abwärtskompatibilität
        for ($ik=1; $ik <= $MultiType; $ik++) 
            {  
            
            if (($ext_TempOutM != '' OR $ext_WaegTempM != '') AND $KorrekturwertArray[$ik-1] != "0" AND $KorrekturwertArray[$ik-1] != "" AND $KalibrierTemperatur != "" AND $ext_GewichtMArray[$ik] != "")
                { 
                if (abs($KorrekturwertArray[$ik-1]) < 2)
                    { // Check Absolutwert --> Aktuelle Einheit im KG-Bereich
                    if ($ext_WaegTempM != '') $ext_GewichtKorrArray[$ik] = round($ext_GewichtMArray[$ik] + (($ext_WaegTempM-$KalibrierTemperatur) * $KorrekturwertArray[$ik-1]),2);
                    elseif ($ext_TempOutM != '') $ext_GewichtKorrArray[$ik] = round($ext_GewichtMArray[$ik] + (($ext_TempOutM-$KalibrierTemperatur) * $KorrekturwertArray[$ik-1]),2);
                    }
                else 
                    { // Aktuelle Einheit im Gramm-Bereich
                    if ($ext_WaegTempM != '') $ext_GewichtKorrArray[$ik] = round($ext_GewichtMArray[$ik] + (($ext_WaegTempM-$KalibrierTemperatur) * $KorrekturwertArray[$ik-1]));
                    elseif ($ext_TempOutM != '') $ext_GewichtKorrArray[$ik] = round($ext_GewichtMArray[$ik] + (($ext_TempOutM-$KalibrierTemperatur) * $KorrekturwertArray[$ik-1])); 
                    } 
                } 
            else $ext_GewichtKorrArray[$ik] = $ext_GewichtMArray[$ik];

            }  



//Alte Werte zum Vergleich ranholen
              for ($i=0; $i <= $MultiType; $i++) 
                  {

                   if ($MultiType == 1 OR $i == 0) $CSVFile = "week.csv";
                   else $CSVFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$i.'/week.csv';

                  if (file_exists($CSVFile)) 
                    { //Letzte Werte aus week.csv auslesen
                    $input = $CSVFile;
                    $array = file($input);
                    $j = sizeof($array);
                      while ($j--)  
                        {
                        $what = trim($array[$j]);    
                        $x = explode( ",", $what );
                        $s = sizeof($x);
                        if ($x[$s-1] !='')  
                          {
                          $LetztesDatumArray[$i] = $x[0]; 
                          $AktualisierungStampArray[$i]=$x[$s-1];
                          $LetztesGewichtArray[$i]=$x[6];
                          break;
                          }      
                        }
                    } //if file_exists...beelogger.csv
                  }


// Bienendifferenz pro Tag berechnen gilt nur für "normale"beelogger
        $BienenDifferenz = ""; //INIT           
        if ($ext_BienenInM != '' AND $ext_BienenOutM != '')
          {  // Beelogger-Easy mit aktivem Bienenzähler
          if (file_exists("week.csv")) 
              { // Werte aus week.csv auslesen
              $input = "week.csv";
              $array = file($input);

              $j = sizeof($array);
               $BienenDifferenz = ($ext_BienenInM-$ext_BienenOutM); //INIT
                while ($j>0)  
                  {
                  $what = trim($array[$j]);    
                  $x = explode( ",", $what );
                  $s = sizeof($x);
                  if ($x[$s-1] !='')  
                      {
// Bienendifferenz wird nur Tagweise berechnet
                      if ( (date("Y",$x[$s-1]) < date("Y")) OR  (date("Y",$x[$s-1]) == date("Y") AND date("z",$x[$s-1]) < date("z")) ) 
                          { //Daten des vorherigen Tages gefunden
                          $BienenAktualisierungStamp=$x[$s-1];
                          $LastBienenIn  = $x[7];                  
                          $LastBienenOut = $x[8];
                          $BienenDifferenzM = ($ext_BienenInM-$LastBienenIn) - ($ext_BienenOutM-$LastBienenOut);
                          break;
                          } //if  
                      }//if 
                  $j--;      
                  }//while
              } //if file_exists...week.csv        
          }//if

// Anmerkungen erzeugen --------------------------------------


        for ($i=1; $i <= $MultiType; $i++) 
          {
          if ($MultiType == 1) $IniFile = 'beelogger_ini.php'; 
          else 
            {
            $IniFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$i.'/beelogger_ini.php'; 
            $AutoAnmerkungenErzeugen = ""; // Initialisierung des Wertes
            $AutoServiceAnmerkung = ""; //INIT
            }

          if (file_exists($IniFile))
            {
            include ($IniFile);

            if ($AutoAnmerkungenErzeugen == "aktiviert" OR $AutoServiceAnmerkung) 
              {//nur bei Aktivierung werden evtl. Anmerkungen erzeugt
              //Alte Werte zum Vergleich ranholen---------------------------

              if ($MultiType == 1) $NoteFile = 'notes.csv';  
              else $NoteFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$i.'/notes.csv';
              $NoteAktualisierungStamp = "";  //INIT     
              if (file_exists($NoteFile)) 
                { //Letzte Werte aus notes.csv auslesen 
                $input = $NoteFile;                
                $narray = file($input);
                $ni = sizeof($narray);
                while ($ni--)  
                  {
                  $nwhat = trim($narray[$ni]);    
                  $nx = explode( ",", $nwhat );
                  $ns = sizeof($nx);
                  if ($nx[$ns-1] !='')  
                    {
                    $NoteAktualisierungStamp=$nx[$ns-1];
                    break;
                    }      
                  }
                $note = ""; //Anmerkung definiert initialisieren
                //Gewichtsinterrupt: Hier wird eine Auto-Anmerkung erzeugt
              
                $GewichtsDifferenz = ($ext_GewichtKorrArray[$i] - $LetztesGewichtArray[$i]);
                if ($GewichtsDifferenz < 100 AND $GewichtsDifferenz > -100) $GewichtsDifferenz = round($GewichtsDifferenz,2); //es muss ein kg-Wert sein
                if (abs($GewichtsDifferenz) > $AnmerkungGewichtsDifferenz)
                  {//es liegt eine entscheidende Gewichtsänderung vor z.B. neue Zarge/Ernte/Durchsicht
                  if ($GewichtsDifferenz > 0 ) $GewichtsÄnderungsArt = "Gewichtszuwachs ";
                  else $GewichtsÄnderungsArt = "Gewichtsabnahme ";
                  if (($timestamp >= ($NoteAktualisierungStamp + $AnmerkungZeitDifferenz*60) OR $NoteAktualisierungStamp == "") AND $AutoAnmerkungenErzeugen == "aktiviert") $note = "5,".$zeit.",?G&".$GewichtsÄnderungsArt."von ".$GewichtsDifferenz.$GewichtsEinheit." erkannt,";    
                    // Anmerkung im Sensor 6 (Gewicht) erzeugen        
                    // Es soll nur eine Anmerkung pro Gewichtsänderung erzeugt werden - Deshalb wird x Minuten gewartet bevor eine zweite Anmerkung erscheint
                    $notedatensatz = $note.$timestamp.$umbruch;
                  if ($note != '')
                    {    // Interrupt gefunden Eintrag in notes.csv  machen
                    $aktion = fOpen($NoteFile, "a+");
                    fWrite($aktion , $notedatensatz);
                    fClose($aktion);                                        
                    }  //if $Gewichtsdifferenz
                  }//if (abs($GewichtsDifferenz) > $AnmerkungGewichtsDifferenz)
                  
                if ($AutoServiceAnmerkung) //ServiceIcon aktiviert
                  {
                  if ($timestamp >= ($NoteAktualisierungStamp + (60*60)) AND ($ext_ServiceM > ($AutoServiceAnmerkungZeit*60)) ) //Durchsicht erkannt und 60 min warten vorm nächsten Icon
                    { 
                    $note = "5,".$zeit.",../beelogger_icons/n_Durchsicht.png&".round($ext_ServiceM/60)." min Durchsicht,";
                    }
                  $notedatensatz = $note.$timestamp.$umbruch;
                  if ($note != '')
                    {    // Interrupt gefunden Eintrag in notes.csv  machen
                    $aktion = fOpen($NoteFile, "a+");
                    fWrite($aktion , $notedatensatz);
                    fClose($aktion);
                    }
                  } //if ($AutoServiceAnmerkung == "true")            
                } //if file_exists...notes.csv            
              } // if AutoAnmerkungen aktiviert...
            } //if (file_exists('../beelogger
          } // for(....) 

        if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php"); // ReInitialisierung der Werte

        //Die Übersicht der Multis bekommt eine Durchsichtanzeige im Service
        if ($AutoServiceAnmerkung == "true" AND $MultiType > 1)
          { //Wenn in Config aktiviert
          if ($ext_ServiceM > ($AutoServiceAnmerkungZeit*60)) // Durchsicht erkannt
            {
            if ($MultiType > 1) $NoteSensor = "3,";
            else $NoteSensor = "8,"; //normaler Einzelbeelogger
            $servicenote = $NoteSensor.$zeit.",../beelogger_icons/n_Durchsicht.png&".round($ext_ServiceM/60)." min Durchsicht,".$timestamp.$umbruch;
            $aktion = fOpen("notes.csv", "a+");
            fWrite($aktion,$servicenote);
            fClose($aktion);
            $Done = true;
            }
          }


        if ($EESendeIntervall == "") $EESendeIntervall = "A"; //INIT


    // Externe Daten ins Array packen 
        if ($Beep != "") $BeepArray[0] = $Beep; //Single/Multi-kompatibilität
        if ($BeepId != "") $BeepIdArray[0] = $BeepId; //Single/Multi-kompatibilität
        if ($iBeekeeper != "") $iBeekeeperArray[0] = $iBeekeeper; //Single/Multi-kompatibilität
        if ($iBeekeeperUId != "") $iBeekeeperUIdArray[0] = $iBeekeeperUId; //Single/Multi-kompatibilität
            
        for ($ib=0; $ib < $MultiType; $ib++) 
          {
          $ExternData[$ib] = "time=".$timestamp."&t=".$ext_TempOutM."&t_i=".$ext_TempArray[$ib+1]."&h=".$ext_FeuchteOutM."&h_i=".$ext_FeuchteArray[$ib+1]."&l=".$ext_LichtM."&bv=".$ext_VBattM."&w_v=".$ext_GewichtKorrArray[$ib+1]; //für Show=1-Info
          if ($ext_BienenInM != '') $ExternData[$ib] .="&bc_i=".$ext_BienenInM;
          if ($ext_BienenOutM != '') $ExternData[$ib] .="&bc_o=".$ext_BienenOutM;
          if ($ext_rssi != '') $ExternData[$ib] .="&rssi=".$ext_rssi;
          elseif ($ext_ServiceM < 0) $ExternData[$ib] .="&rssi=".$ext_ServiceM;
          $ExternDataArray[$ExternDataCounter] = $ExternData[$ib]; //zum Aussenden in ExternDataOut()
          $ExternDataCounter++;
          } //Ende Beepdaten vorbereiten


    // Datensatz normaler beelogger
      if ($MultiType == 1)
        {

        // Datensatz erzeugen für beelogger.csv
        $datensatz = $zeit.$tz.$ext_TempArray[1].$tz.$ext_TempOutM.$tz.$ext_FeuchteArray[1].$tz.$ext_FeuchteOutM.$tz.$ext_LichtM.$tz.$ext_GewichtKorrArray[1].$tz;

        if ($ext_VBattM != "") $datensatz .= $ext_VBattM .$tz.$ext_VSolarM.$tz.$ext_ServiceM; //SpezialTeil-Datensatz für beelogger solar
        else $datensatz .= $ext_BienenInM.$tz.$ext_BienenOutM.$tz.$BienenDifferenzM; //SpezialTeil-Datensatz für beelogger EASY
       
        $datensatz .= $tz.$ext_GewichtMArray[1]; //unkorr. Gewicht     
        $datensatz .= $tz.$ext_Aux1M.$tz.$ext_Aux2M.$tz.$ext_Aux3M; //SpezialTeil-Datensatz für Aux
        $datensatz .= $tz.$tz.$tz.$timestamp.$umbruch; //Ende Datensatz

        if  ($LetztesDatumArray[1] <= "2015/01/01 00:00:00") $LetztesDatumArray[1] = "2015/01/01 00:00:00";
        if ($zeit > $LetztesDatumArray[1]) 
          { //Abfrage zur Fehlervermeidung Sommer/Winterzeitumstellung 
          $aktion = fOpen('beelogger.csv',"a+");
          fWrite($aktion , $datensatz);
          fClose($aktion);
          }
        CSVbuilder();  
        FaceOut();
        } //if ($MultiType == 1)


    //CSV-Builder-Ansteuerung Multi
        if ($MultiType > 1) //ab Duo....
          for ($i=1; $i <= $MultiType; $i++) 
          {
              {
              if  ($LetztesDatumArray[$i] <= "2015/01/01 00:00:00") $LetztesDatumArray[$i] = "2015/01/01 00:00:00"; 
              if ($zeit > $LetztesDatumArray[$i]) 
                  { //Abfrage zur Fehlervermeidung Sommer/Winterzeitumstellung 
                  $datensatz = $zeit.$tz.$ext_TempArray[$i].$tz.$ext_TempOutM.$tz.$ext_FeuchteArray[$i].$tz.$ext_FeuchteOutM.$tz.$ext_LichtM.$tz.$ext_GewichtKorrArray[$i].$tz.$ext_VBattM.$tz.$ext_VSolarM.$tz.$ext_ServiceM.$tz.$ext_GewichtMArray[$i];
                  
                  $datensatz .= $tz.$ext_Aux1M.$tz.$ext_Aux2M.$tz.$ext_Aux3M; //SpezialTeil-Datensatz für Aux
                  $datensatz .= $tz.$tz.$tz.$timestamp.$umbruch; 

                  $CSVFile =  '../beelogger'.$MultiSign.$ServerMultiNumber."_".$i.'/beelogger.csv';
                  $aktion = fOpen($CSVFile,"a+");
                  fWrite($aktion , $datensatz);
                  fClose($aktion);
                  
                  $HelpOrdner = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$i.'/';
                  CSVbuilder($HelpOrdner);
                  FaceOut($HelpOrdner);
                  }

              } // for
          } // if ($MultiType > 1 ) 
     
                      
 
     //------- Gesamtdaten Multi-----------------
         if ($MultiType > 1)
          { 
          $datensatz = $zeit.$tz.$ext_WaegTempM.$tz.$ext_TempOutM.$tz.$ext_FeuchteOutM.$tz.$ext_ServiceM.$tz.$ext_LichtM.$tz.$ext_GewichtKorrArray[1].$tz.$ext_GewichtKorrArray[2];
          $i = 3;
          while ($i <= $MultiType) 
          {$datensatz .=$tz.$ext_GewichtKorrArray[$i];$i++;}
 
          $datensatz .=$tz.$ext_TempArray[1].$tz.$ext_TempArray[2];
          $i = 3;
          while ($i <= $MultiType) 
          {$datensatz .=$tz.$ext_TempArray[$i];$i++;}

          $datensatz .=$tz.$ext_FeuchteArray[1].$tz.$ext_FeuchteArray[2];
          $i = 3;
          while ($i <= $MultiType) 
          {$datensatz .=$tz.$ext_FeuchteArray[$i];$i++;}

          $datensatz .=$tz.$ext_VBattM.$tz.$ext_VSolarM.$tz.$ext_GewichtMArray[1].$tz.$ext_GewichtMArray[2];
          $i = 3;
          while ($i <= $MultiType)  
          {$datensatz .=$tz.$ext_GewichtMArray[$i];$i++;}

          $datensatz .= $tz.$ext_Aux1M.$tz.$ext_Aux2M.$tz.$ext_Aux3M; //SpezialTeil-Datensatz für Aux
          $datensatz .=$tz.$tz.$tz.$timestamp.$umbruch;
          if  ($LetztesDatumArray[0] <= "2015/01/01 00:00:00") $LetztesDatumArray[0] = "2015/01/01 00:00:00";
            if ($zeit > $LetztesDatumArray[0]) 
              { //Abfrage zur Fehlervermeidung Sommer/Winterzeitumstellung 
              $aktion = fOpen("beelogger.csv", "a+");
              fWrite($aktion , $datensatz);
              fClose($aktion);
              CSVbuilder();
              }
          }


   //Zeitsynchronisation für EE_beelogger      
        $date = new DateTime( '1970-01-01 00:00:00' );
        $date2 = new DateTime( "now" );
        $DS3231 = $date2->getTimestamp() - $date->getTimestamp();

        if ($TimeSync == '1') $Zeitsynchronisation = $DS3231."T";
        elseif ($TimeSync == '2') $Zeitsynchronisation = ($DS3231 + date("I")*3600)."T"; //Sketch Winter/Sommerzeitfähig
        else $Zeitsynchronisation = ""; //INIT



        if ($ext_Show=='1') 
        {
          
          if (!file_exists("beelogger_ini.php"))  echo "<br>".$LAs[0]."!";    
          if ($Softwareversion != "") echo "<br>".$LAs[7].": ".$Softwareversion."<br>";
          if ($NeubeeloggerSketchID != "") echo $LAs[8].": ".$NeubeeloggerSketchID."<br><br>";

          if ($MultiType == 1)
            {      
            if ($ext_VBattM != "") echo"<br><blockquote>".$LAs[9]."!";
            else echo"<br><blockquote>".$LAs[10]."!";
            echo "<br>".$LAs[11].":<br><br>";
            echo "<b>".$Sensoren[0].": </b>".$ext_TempArray[1]."<br><b>".$Sensoren[5].": </b>".$ext_TempOutM."<br><b>".$Sensoren[10].": </b>".$ext_FeuchteArray[1]."<br><b>".$Sensoren[15].": </b>".$ext_FeuchteOutM."<br><b>".$Sensoren[20].": </b>".$ext_LichtM."<br><b>".$Sensoren[25].": </b>".$ext_GewichtKorrArray[1]."<br>";
           
              echo"<b>".$LAs[12].": </b> ".$ext_VBattM.$ext_BienenInM."<br><b>".$LAs[13].": </b> ".$ext_VSolarM.$ext_BienenOutM."<br><b>".$LAs[14].": </b>".$BienenDifferenzM.$ext_ServiceM."</blockquote>";
              if ($ext_Aux1M != "") echo"<br><b>Aux1 : </b>".$ext_Aux1M;
              if ($ext_Aux2M != "") echo"<br><b>Aux2 : </b>".$ext_Aux2M;
              if ($ext_Aux3M != "") echo"<br><b>Aux3 : </b>".$ext_Aux3M;
              
            if  ($ext_GewichtKorrArray[1] != $ext_GewichtMArray[1]) echo "<br><b>".$LAs[15].": </b>".$ext_GewichtMArray[1]."<br><b>".$LAs[16].": </b>".$ext_GewichtKorrArray[1];
            if ($Trachtdurchschnitt != "") echo "<br>".$LAs[17].": ".$Trachtdurchschnitt;
          } //if ($MultiType == 1)

          if ($MultiType > 1) 
            {
            echo "<br><blockquote>".$LAs[18].": ".$MultiTypeName."</b> ".$LAs[19].":<br><br><table border=1><tr><td></td><td><b> ".$LAs[20]." 1 </b></td><td><b> ".$LAs[20]." 2 </b></td>";
            if ($MultiType > 2) echo "<td><b> ".$LAs[20]." 3 </b></td>";
            if ($MultiType > 3) echo "<td><b> ".$LAs[20]." 4 </b></td>";
            if ($MultiType > 4) echo "<td><b> ".$LAs[20]." 5 </b></td>";
            if ($MultiType > 5) echo "<td><b> ".$LAs[20]." 6 </b></td>";
            if ($MultiType > 6) echo "<td><b> ".$LAs[20]." 7 </b></td>";
            echo"</tr><tr><td><b>".$LAs[21]."</b></td><td>".$ext_TempArray[1]."</td><td>".$ext_TempArray[2]."</td>";
            if ($MultiType > 2) echo "<td>".$ext_TempArray[3]."</td>";
            if ($MultiType > 3) echo "<td>".$ext_TempArray[4]."</td>";
            if ($MultiType > 4) echo "<td>".$ext_TempArray[5]."</td>";
            if ($MultiType > 5) echo "<td>".$ext_TempArray[6]."</td>";
            if ($MultiType > 6) echo "<td>".$ext_TempArray[7]."</td>";
            echo"</tr><tr><td><b>".$LAs[22]."</b></td><td>".$ext_FeuchteArray[1]."</td><td>".$ext_FeuchteArray[2]."</td>";
            if ($MultiType > 2) echo "<td>".$ext_FeuchteArray[3]."</td>";
            if ($MultiType > 3) echo "<td>".$ext_FeuchteArray[4]."</td>";
            if ($MultiType > 4) echo "<td>".$ext_FeuchteArray[5]."</td>";
            if ($MultiType > 5) echo "<td>".$ext_FeuchteArray[6]."</td>";
            if ($MultiType > 6) echo "<td>".$ext_FeuchteArray[7]."</td>";
            echo"</tr><tr><td><b>".$LAs[23]."</b></td><td>".$ext_GewichtMArray[1]."</td><td>".$ext_GewichtMArray[2]."</td>";
            if ($MultiType > 2) echo "<td>".$ext_GewichtMArray[3]."</td>";
            if ($MultiType > 3) echo "<td>".$ext_GewichtMArray[4]."</td>";
            if ($MultiType > 4) echo "<td>".$ext_GewichtMArray[5]."</td>";
            if ($MultiType > 5) echo "<td>".$ext_GewichtMArray[6]."</td>";
            if ($MultiType > 6) echo "<td>".$ext_GewichtMArray[7]."</td>";
            echo"</tr><tr><td><b>".$LAs[24]."</b></td><td>".$ext_GewichtKorrArray[1]."</td><td>".$ext_GewichtKorrArray[2]."</td>";
            if ($MultiType > 2) echo "<td>".$ext_GewichtKorrArray[3]."</td>";
            if ($MultiType > 3) echo "<td>".$ext_GewichtKorrArray[4]."</td>";
            if ($MultiType > 4) echo "<td>".$ext_GewichtKorrArray[5]."</td>";
            if ($MultiType > 5) echo "<td>".$ext_GewichtKorrArray[6]."</td>";
            if ($MultiType > 6) echo "<td>".$ext_GewichtKorrArray[7]."</td>";
            echo"</tr></table><br><b>".$LAs[25].": </b>".$ext_TempOutM."<br><b>".$LAs[26].": </b>".$ext_WaegTempM."<br><b>".$LAs[27].": </b>".$ext_FeuchteOutM."<br><b>".$LAs[28].": </b>".$ext_LichtM."<br><b>".$LAs[29].": </b>".$ext_ServiceM."<br><b>".$LAs[30].": </b>".$ext_VBattM."<br><b>".$LAs[31].": </b>".$ext_VSolarM."<br><br><b>Aux1: </b>".$ext_Aux1M."<br><b>Aux2: </b>".$ext_Aux2M."<br><b>Aux3: </b>".$ext_Aux3M."<br></blockquote>";
            }
                        
            echo"<br><br>".$LAs[32].": ".$datensatz;
            if ($IntervallSendeSteuerung != "deaktiviert") echo "<br>".$LAs[33]." '".$IntervallSendeSteuerung."' ".$LAs[34].": ".$SendeIntervall. " ".$LAs[35].".";
            if ($note != "") echo "<br><br>".$LAs[36].": ".$notedatensatz;
            if ($ExternData[0] != "") echo "<br><br>".$LAs[37].": ".$BeepId." : ".$ExternData[0];



            for ($ib=1; $ib < 5; $ib++) 
                {
                if ($BeepWerte[$ib] != "") echo "<br><br>".$LAs[20]." No.".$ib.": ".$LAs[37].": ".$BeepIdArray[$ib-1]." : ".$ExternData[$ib];
                }
            
            if ($Answer == 1) echo "<br>".$LAs[38].": ".$Zeitsynchronisation.$SendeIntervall.$EESendeIntervall.$NextService."ok *"; 
        
        } //    if ($ext_Show=='1')    

        elseif ($Answer == 1) echo $Zeitsynchronisation.$SendeIntervall.$EESendeIntervall.$NextService."ok *";  // hier wird mitgeteilt, wann die nächste Aussendung stattfinden soll...diese Funktion muss im Arduinosketch im beelogger(ab Version 2.0)  implementiert sein!!!
              





        // Schwarmalarm 2.0  ----------------------------------------------
        if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php");

        $int_EmailSchwarmAlarmArray = ["",$EmailSchwarmAlarmArray[0],$EmailSchwarmAlarmArray[1],$EmailSchwarmAlarmArray[2],$EmailSchwarmAlarmArray[3]];
        if ($EmailSchwarmAlarm != "") $int_EmailSchwarmAlarmArray[1] = $EmailSchwarmAlarm; //Abwärtskompatibilität
        $int_PushSchwarmAlarmArray = [$PushSchwarmAlarm,$PushSchwarmAlarmArray[0],$PushSchwarmAlarmArray[1],$PushSchwarmAlarmArray[2],$PushSchwarmAlarmArray[3]];
        if ($PushSchwarmAlarm != "") $int_PushSchwarmAlarmArray[1] = $PushSchwarmAlarm; //Abwärtskompatibilität

        $int_DifferenzgewichtArray = ["",$DifferenzGewichtArray[0],$DifferenzGewichtArray[1],$DifferenzGewichtArray[2],$DifferenzGewichtArray[3]];

        if ($DifferenzGewicht != "") $int_DifferenzgewichtArray[1] = $DifferenzGewicht; //Abwärtskompatibilität

        $int_ReferenzZeitArray = ["",$ReferenzZeitArray[0],$ReferenzZeitArray[1],$ReferenzZeitArray[2],$ReferenzZeitArray[3]];
        if ($ReferenzZeit != "") $int_ReferenzZeitArray[1] = $ReferenzZeit; //Abwärtskompatibilität
        
        $S = 1; //Initialisierung
        while ($S <= $MultiType) 
            {
            if (($int_EmailSchwarmAlarmArray[$S] == "aktiviert") OR ($int_PushSchwarmAlarmArray[$S] == "aktiviert")) 
                { 
                if ($MultiType == 1) $WarnFile = 'warnung.txt';
                else $WarnFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$S.'/warnung.txt';
                if (!file_exists($WarnFile)) 
                    { //Falls Datei nicht existiert - anlegen !
                    $SchwarmalarmArray [0] = "<?php\n";
                    $SchwarmalarmArray [1] = "// Hilfs-Datei zur Ermittlung eines Schwarmalarms\n\n";
                    $SchwarmalarmArray [2] = '$ZeitpunktErsterGewichtsverlust = "";'."\n";
                    $SchwarmalarmArray [3] = '$LetztesGewichtVormVerlust = "";'."\n";
                    $SchwarmalarmArray [4] = '$SchwarmAlarmMessage = "-";'."\n";
                    $aktion = fOpen($WarnFile, "w+");
                    foreach($SchwarmalarmArray as $values) fputs($aktion, $values);
                    $OK = fputs($aktion,"?>"); //test ob Datei zu öffnen war
                    fClose($aktion);
                    } //if !file exists
                include ($WarnFile); // Damit wird eventuelle $SchwarmAlarmMessage aus dem Speicher geholt
                $Schwarm = "false";  //Init $Schwarm

                // Feststellung erster Gewichtsverlust                                   
                $Gewichtsverlust = ($LetztesGewichtArray[$S] - $ext_GewichtKorrArray[$S]);  // 

                if ($Gewichtsverlust > $int_DifferenzgewichtArray[$S]) 
                    { // Gewichtsverlust registriert
                    $SchwarmalarmArray [0] = "<?php\n";
                    $SchwarmalarmArray [1] = "// Hilfs-Datei zur Ermittlung eines Schwarmalarms\n\n";
                    $SchwarmalarmArray [2] = '$ZeitpunktVormErstenGewichtsverlust = "'.$AktualisierungStampArray[$S].'";'."\n";
                    $SchwarmalarmArray [3] = '$LetztesGewichtVormVerlust = "'.$LetztesGewichtArray[$S].'";'."\n";
                    $SchwarmalarmArray [4] = '$SchwarmAlarmMessage = "'.$SchwarmAlarmMessage.'";'."\n";
                    $aktion = fOpen($WarnFile, "w+");
                    foreach($SchwarmalarmArray as $values) fputs($aktion, $values);
                    $OK = fputs($aktion,"?>"); //test ob Datei zu öffnen war
                    fClose($aktion);

                    // Check, ob bereits Zeitdifferenz überschritten
                    $MaxZeitSeitSchwarm = $timestamp - $AktualisierungStampArray[$S]; //in ticks = sekunden
                    $MaxZeitSeitSchwarm = round(($MaxZeitSeitSchwarm/60),0).' '.$LAs[39].' '.($MaxZeitSeitSchwarm%60).' '.$LAs[40];
                    if ($timestamp >= ($AktualisierungStampArray[$S] + $int_ReferenzZeitArray[$S]*60)) 
                        { 
                        $Schwarm = "true"; 
                        if (intval($SchwarmAlarmMessage[3]) >= 1) 
                            { //Es gab bereits mind. einen Schwarmalarm -Neuen Index rausfinden
                            $SchwarmAlarmNewMessage = "No.".(intval($SchwarmAlarmMessage[3])+1)." ".$LAs[41].": ".date("d.m H:i",$AktualisierungStampArray[$S]);
                            }
                        else 
                            { //Erster Schwarmalarm
                            $SchwarmAlarmNewMessage = "No.1 ".$LAs[41].":".date("d.m",$AktualisierungStampArray[$S])." ".$LAs[42]." ".date("H:i",$AktualisierungStampArray[$S]);
                            }               
                        } // If Zeit direkt überschritten
                        // Schwarmabgang wahrscheinlich - Nachrichten raus und Reset für Nachschwarm
                    }  // if Gewichtsverlust größer als Differenzgewicht

                    if ($ZeitpunktVormErstenGewichtsverlust != "") 
                    { //Ja, erster Gewichtsverlust war da...

                    $MaxZeitSeitSchwarm = $timestamp - $ZeitpunktVormErstenGewichtsverlust; //in ticks = sekunden
                    $MaxZeitSeitSchwarm = floor($MaxZeitSeitSchwarm/60).' '.$LAs[39].' '.($MaxZeitSeitSchwarm%60).' '.$LAs[40];
                    if ($timestamp >= ($ZeitpunktVormErstenGewichtsverlust + $int_ReferenzZeitArray[$S]*60)) 
                        { 

                        $Gewichtsverlust =  ($LetztesGewichtVormVerlust - $ext_GewichtKorrArray[$S]);
                        if ($Gewichtsverlust >=  $int_DifferenzgewichtArray[$S]) 
                            {
                            $Schwarm = "true"; //Der Gewichtsverlust hält an !!!
                            // Schwarmabgang wahrscheinlich - Nachrichten raus und Reset für Nachschwarm
                            } // if Gewichtsverlust immernoch da
                        else 
                            { // Kommando zurück weil :  Ursprungsgewicht ist ungefähr wieder erreicht 
                            $SchwarmalarmArray [0] = "<?php\n";
                            $SchwarmalarmArray [1] = "// Hilfs-Datei zur Ermittlung eines Schwarmalarms\n\n";
                            $SchwarmalarmArray [2] = '$ZeitpunktVormErstenGewichtsverlust = "";'."\n";
                            $SchwarmalarmArray [3] = '$LetztesGewichtVormVerlust = "";'."\n";
                            $SchwarmalarmArray [4] = '$SchwarmAlarmMessage = "'.$SchwarmAlarmMessage.'";'."\n";
                            $aktion = fOpen($WarnFile, "w+");
                            foreach($SchwarmalarmArray as $values) fputs($aktion, $values);
                            $OK = fputs($aktion,"?>"); //test ob Datei zu öffnen war
                            fClose($aktion);
                            } //else Kommando zurück
                        } // if Timestamp >= ....

                    if (intval($SchwarmAlarmMessage[3]) >= 1) 
                        { //Es gab bereits mind. einen Schwarmalarm
                        $SchwarmAlarmNewMessage = "No.".(intval($SchwarmAlarmMessage[3])+1)." ".$LAs[41]." ".date("d.m",$ZeitpunktVormErstenGewichtsverlust)." ".$LAs[42]." ".date("H:i",$ZeitpunktVormErstenGewichtsverlust);
                        }
                    else 
                        { //Erster Schwarmalarm
                        $SchwarmAlarmNewMessage = "No.1 ".$LAs[41].": ".date("d.m",$ZeitpunktVormErstenGewichtsverlust)." ".$LAs[42]." ".date("H:i",$ZeitpunktVormErstenGewichtsverlust);
                        }  
                    } //if ($ZeitpunktVormErstenGewichtsverlust != "")  


                    if ($Schwarm == "true")
                        {
                        $SchwarmalarmArray [0] = "<?php\n";
                        $SchwarmalarmArray [1] = "// Hilfs-Datei zur Ermittlung eines Schwarmalarms\n\n";
                        $SchwarmalarmArray [2] = '$ZeitpunktVormErstenGewichtsverlust = "";'."\n";
                        $SchwarmalarmArray [3] = '$LetztesGewichtVormVerlust = "";'."\n";
                        $SchwarmalarmArray [4] = '$SchwarmAlarmMessage = "'.$SchwarmAlarmNewMessage.'";'."\n";
                        $aktion = fOpen($WarnFile, "w+");
                        foreach($SchwarmalarmArray as $values) fputs($aktion, $values);
                        $OK = fputs($aktion,"?>"); //test ob Datei zu öffnen war
                        fClose($aktion);

                        $actual_link = strstr("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'beelogger_log',TRUE)."beelogger_show.php";

                        if ($MultiType == 1) $MailPushbeeloggerNummer = str_replace("beelogger", "",$beelogger);
                            else  $MailPushbeeloggerNummer = $MultiTypeName.$ServerMultiNumber."_".$S;

                        // EMAIL- Nachricht
                        if (($int_EmailSchwarmAlarmArray[$S] == "aktiviert") AND ($Absender_Email != "absender@meineDomain.de") AND ($Empfaenger_Email != "empfaenger@meineDomain.de")) 
                            {  
                            $Mailbetreff = $LAs[43].": beelogger".$MailPushbeeloggerNummer;
                            
                            $Absender_Name = "beelogger-".$LAs[44]." 2.0";
                            $Header  = "From:".$Absender_Name." <".$Absender_Email.">\n";
                            //$Header .= ("Content-type: text/plain; charset=\"utf-8\"\r\n");       
                            $Nachricht  = $LAs[45].":\n\n";
                            $Nachricht .= $LAs[46]." ".$Gewichtsverlust.$GewichtsEinheit.$LAs[47].": ".$MaxZeitSeitSchwarm;
                            $Nachricht .= "\nLink: ".$actual_link;
                            mail($Empfaenger_Email, $Mailbetreff, $Nachricht, $Header);     
                            } //if EmailAlarm aktiviert

                        //PUSH-Nachricht
                        if ($int_PushSchwarmAlarmArray[$S] == "aktiviert") 
                            {
                            curl_setopt_array($ch = curl_init(), array(
                            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                            CURLOPT_POSTFIELDS => array(
                            "token" => $PushToken,
                            "user" => $PushUser,
                            "message" => $LAs[48]." beelogger".$MailPushbeeloggerNummer." - ".$LAs[46]." ".$Gewichtsverlust.$GewichtsEinheit.". ".$LAs[47].": ".$MaxZeitSeitSchwarm." Link: ".$actual_link,
                                                    ),
                            CURLOPT_SAFE_UPLOAD => true,
                            CURLOPT_RETURNTRANSFER => true,
                                                                      )
                                             );
                            curl_exec($ch);
                            curl_close($ch);
                            } //if PushSchwarmAlarm aktiviert

                        // Anmerkung erzeugen
                        $note = ""; //INIT   
                        if ($MultiType == 1) 
                            {      
                            $note = "5,".$zeit.",../beelogger_icons/n_Schwarmabgang.png&".$LAs[44]." ".$SchwarmAlarmNewMessage." ".$LAs[49]." ".$Gewichtsverlust.$GewichtsEinheit." ".$LAs[50].",";
                              $NoteFile = "./notes.csv";
                            }
                        else 
                            {        
                            $IniFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$S.'/beelogger_ini.php';  
                            if (file_exists($IniFile))
                              {
                              // $AutoAnmerkungenErzeugen = ""; // Initialisierung des Wertes
                              // include ($IniFile);
                                $note = "5,".$zeit.",../beelogger_icons/n_Schwarmabgang.png&".$LAs[44]." ".$SchwarmAlarmNewMessage." ".$LAs[49]." ".$Gewichtsverlust.$GewichtsEinheit." ".$LAs[50].",";
                                $NoteFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$S.'/notes.csv';
                              }
                            }

                        $notedatensatz = $note.$timestamp.$umbruch;
                        $aktion = fOpen($NoteFile, "a+");
                        fWrite($aktion , $notedatensatz);
                        fClose($aktion);     
                        } // If Schwarm = true

                        if ($ext_Show=='1') 
                        {

                        echo "<br><br><br>".$LAs[44]."-INFO:";
                        if ($MultiType != 1) echo $S;
                        if ($int_EmailSchwarmAlarmArray[$S] == "aktiviert") 
                            {
                            echo"<br>Email-".$LAs[44];
                            if ($MultiType != 1) echo $S;
                            echo " ".$LAs[51]."!";
                            }
                        if ($int_PushSchwarmAlarmArray[$S] == "aktiviert") 
                            {
                            echo"<br>Push-".$LAs[44];
                            if ($MultiType != 1) echo $S;
                            echo " ".$LAs[51]."!";
                            }
                        if ($Gewichtsverlust > 0) echo "<br>".$LAs[46].": ".$Gewichtsverlust.$GewichtsEinheit;
                        echo "<br>".$LAs[52].": ".$int_DifferenzgewichtArray[$S].$GewichtsEinheit." ".$LAs[53].".";
                        if ($MaxZeitSeitSchwarm != "") echo "<br> ".$LAs[47]." ".$MaxZeitSeitSchwarm;
                        echo "<br>".$LAs[54].": ".$int_ReferenzZeitArray[$S].' '.$LAs[35].' ';
                        if ($ZeitpunktVormErstenGewichtsverlust != "") echo "<br> ".$LAs[55].": ".date("d.m.Y H:i:s",$ZeitpunktVormErstenGewichtsverlust)." ".$LAs[56]."!";
                        } //if ext_show=1 

                } //if  SchwarmEmailalarm oder push aktiviert  
                if ($MultiType == 1) break; //nach erstem Durchgagng raus
                $S++;
            } //while Schleiffe Schwarmalarm

        // ENDE Schwarmalarm 2.0 -----------------------------------------  
  } //Ende function    


function FaceOut($beeOrdner = "") 
  {    
  if (file_exists($beeOrdner."beelogger_ini.php")) include ($beeOrdner."beelogger_ini.php");

  $FaceArray[0] =  "<?php\n\n".'$sensor_anzahl = '.(sizeof($Sensoren)/5).";\n";

  $FaceArray[1] =  "$sensor_aktualisierung = 'no';\n"; //keine week.csv existent

          $FaceArrayHelp = '$sensor_bezeichnung = array('."'";
          for ($i=0; $i < ((sizeof($Sensoren)/5)-1); $i++) 
              { 
              $FaceArrayHelp .= $Sensoren[$i*5+0]."','"; //Sensorbezeichnungen   
              }
  $FaceArray[2] .= $FaceArrayHelp.$Sensoren[$i*5+0]."');\n";


  $FaceArray[3] = '$sensor_wert = array('."'no'".");\n";  //INIT
  $FaceArray[4] = '$sensor_wert_1 = array('."'no'".");\n";  //INIT
  $FaceArray[5] = '$sensor_wert_24 = array('."'no'".");\n";  //INIT
  $FaceArray[6] = '$sensor_icon = array('."'no'".");\n";  //INIT
  $FaceArray[7] = '$sensor_intervall = "no";'."\n"; //init
  $FaceArray[8] = '$rain_for_24 = "no";'."\n"; //init
  $FaceArray[9] = "?>";

  //Ermittlung ob Regensensor vorhanden ---
  $Size = sizeof($Icon);
  while ($Size--)
    {
    if ($Icon[$Size] == "rain.png" OR $Size == 0) break;
    }


  $RegenSumme24h = "-1"; //INIT  
  if ($Size > 0) //Ja, Regensensor wahrscheinlich angelegt, da Icon vorhanden
    {
    $RegenSumme24h = 0; //INIT  
    $RegenSensorStelle = $Size+1;
    $CSVFile = $beeOrdner."week.csv";         
    if (file_exists($CSVFile))  
      { //eine week.csv gefunden
      $array = file($CSVFile);
      $i = sizeof($array);
      $what = trim($array[$i-1]);
      $LetzteZeile = explode( ",",$what);
      $LastZeitpunkt = "";
      while ($i--) 
        { 
        $what = trim($array[$i]);    
        $x = explode( ",", $what );
        $s = sizeof($x);
        if ($x[$s-1] != '' AND $LastZeitpunkt == "") $LastZeitpunkt = $x[$s-1];
        if (($x[$s-1]+86400) >= $LastZeitpunkt) $RegenSumme24h += $x[$RegenSensorStelle];
        else break;
        } //while
      }//if (file_exists($CSVFile)) 
    }//if ($Size > 0)


         
  $CSVFile = $beeOrdner."week.csv";         
  if (file_exists($CSVFile)) 
    { //Letzte Werte aus week.csv auslesen
    $FaceArrayHelp = '$sensor_wert = array('."'";    
    $array = file($CSVFile);
    $j = sizeof($array);
    while ($j--)  
      {
      $what = trim($array[$j]);    
      $x = explode( ",", $what );
      $s = sizeof($x);
      if ($x[$s-1] !='')  
        {
        $LetzterDatensatz =  $x[$s-1]; 
        $FaceArray[1] =  '$sensor_aktualisierung = "'.$x[$s-1].'"; //'.date("Y/m/d H:i:s",($x[$s-1]))."\n";
        for ($k=1; $k < ($s-2); $k++) 
          { 
          $FaceArrayHelp .= $x[$k]."','";
          }
        $FaceArray[3] = $FaceArrayHelp.$x[$k]."');\n";
        break;
        }      
      }

    $FaceArrayHelp = '$sensor_wert_1 = array('."'";   
    $what = trim($array[$j-1]);    
    $x = explode( ",", $what );
    $s = sizeof($x);
    if ($x[$s-1] !='')  
      {
      $VorletzterDatensatz =  $x[$s-1];   
      for ($k=1; $k < ($s-2); $k++) 
        { 
        $FaceArrayHelp .= $x[$k]."','";
        }
      $FaceArray[4] = $FaceArrayHelp.$x[$k]."');\n";
      }

    //24 Stundenwert ermitteln
    $FaceArrayHelp = '$sensor_wert_24 = array('."'";    
    $i = sizeof($array);
    while ($i--) 
      {
      $what = trim($array[$i]);    
      $x = explode( ",", $what );
      $s = sizeof($x);
      if ($x[$s-1] !='') 
        {
        $AktualisierungsStamp=$x[$s-1];
        if ($AktualisierungsStamp <= ($LetzterDatensatz - 86400) AND $AktualisierungsStamp > ($LetzterDatensatz - 90000)) //ticks = 60*60*24 = 86400  = 1 Tag - 1Stunde Toleranz zugelassen
          {
          for ($k=1; $k < ($s-2); $k++) 
            { 
            $FaceArrayHelp .= $x[$k]."','";
            }
          $FaceArray[5] = $FaceArrayHelp.$x[$k]."');\n";
          break;   
          }
        if ($AktualisierungsStamp < ($LetzterDatensatz - 90000)) break; //keinen Wert gefunden
        }// if ($x[$s-1] !='')
      } //while
    } //if file_exists...beelogger.csv
        

  $FaceArrayHelp = '$sensor_icon = array(';
  $f = sizeof($Icon);
  for ($fi=0; $fi < $f-1; $fi++) 
    { 
    $FaceArrayHelp .= "'".$Icon[$fi]."',";
    }
  $FaceArray[6] = $FaceArrayHelp."'".$Icon[$fi]."');\n";



  if (file_exists("beelogger_ini.php")) include ("beelogger_ini.php"); //Sonderfall Multibeelogger
  $SendeIntervall = "0"; //INIT
  
  //Berechnung der Serversendesteuerung--------------------------
  if (fmod($SommerBeginn, 1) == 0.5) $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
  else $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn)."/1"));

  if ($WinterBeginn != "deaktiviert") 
    {
    if (fmod($WinterBeginn, 1) == 0.5) $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
    else $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn)."/1"));
    }

  if ($IntervallSendeSteuerung == "lichtgesteuert") 
    { //lichtgesteuerte Sendeintervalle
    if ($ext_Licht > 1) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
    else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 

    if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute
    if ($ext_Licht == "") $IntervallSendeSteuerung = "zeitgesteuert"; // Lichtsteuerung ohne Werte geht nicht
    } // if lichtgesteuert

  if ($IntervallSendeSteuerung == "solarspannungsgesteuert") 
    { //solarspannungsgesteuerte Sendeintervalle
    if ($ext_VSolar > 0.3 ) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
    else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 
    
    if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute
    if ($ext_VSolar == "") $IntervallSendeSteuerung = "zeitgesteuert"; // Spannungsteuerung ohne Werte geht nicht
    } // if solarspannungsgesteuert

  if ($IntervallSendeSteuerung == "zeitgesteuert") 
    { //zeitgesteuerte Sendeintervalle
    if ((intval(date("G")) >= $SommerTagZeit)  AND (intval(date("G")) < $SommerNachtZeit)) $SendeIntervall = $SommerSendeIntervallTag;//Berechnung (sehr angenähert) für den Sonnen-Auf und Untergang
    else $SendeIntervall = $SommerSendeIntervallNacht;
    
    if ($WinterBeginn != "deaktiviert") 
      { 
      if ((date("z") < $SommerBeginnInTagen) OR (date("z") > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall;
      }
    } // if zeitgesteuert

  include("beelogger_map.php"); 
  if (strpos($beeloggerSketchID,"EE") !== FALSE)
    {
    $SendeIntervallNormal = $SendeIntervall; //Hilfsvariable  
    if ($EESendeIntervall == "A" AND $SendeIntervallNormal <= 30) $SendeIntervall = 60;
    if ($EESendeIntervall == "A" AND $SendeIntervallNormal > 30) $SendeIntervall = 240;
    if ($EESendeIntervall == "B" AND $SendeIntervallNormal <= 30) $SendeIntervall = 120;
    if ($EESendeIntervall == "B" AND $SendeIntervallNormal > 30) $SendeIntervall = 480;
    if ($EESendeIntervall == "C" AND $SendeIntervallNormal <= 30) $SendeIntervall = 240;
    if ($EESendeIntervall == "C" AND $SendeIntervallNormal > 30) $SendeIntervall = 960;
    if ($EESendeIntervall == "D" AND $SendeIntervallNormal <= 30) $SendeIntervall = 480;
    if ($EESendeIntervall == "D" AND $SendeIntervallNormal > 30) $SendeIntervall = 1440; 
    }  

   if ($SendeIntervall == "0")  
    {
    if ($LetzterDatensatz != "" AND $VorletzterDatensatz != "") $SendeIntervall = round(($LetzterDatensatz-$VorletzterDatensatz) / 60); //in Miunten 
    }
  if ($SendeIntervall != "0")  $FaceArray[7] =  '$sensor_intervall = "'.$SendeIntervall.'"'.";\n";
  if ($RegenSumme24h != "-1")  $FaceArray[8] =  '$rain_for_24 = "'.$RegenSumme24h.'"'.";\n";
          
  $fp = fOpen($beeOrdner."beelogger_interface.php","w");
  foreach($FaceArray as $values) {fputs($fp, $values);}
  fclose($fp);
}// Ende function FaceOut 



function CSVbuilder($beeOrdner = "")   //CSV Builder Version 3.0-------------
    {
    $input = $beeOrdner."beelogger.csv";                
    $array = file($input);
    $asize = sizeof($array);  //Grösse des Arrays

    $i = $asize;
    while ($i--) 
        {
        $what = trim($array[$i]);    
        $x = explode( ",", $what );
        $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
        if ($x[$s-1] !='') 
            {     // um hier die letzte Spalte = Zeitstempel abzufragen
            $LastDate=$x[$s-1];
            break;
            }
        }

        //Monats-Datei erzeugen:Ein Monat hat etwa  60*60*24*30 = 2592000 Ticks
        $fp = fOpen(($beeOrdner."month.csv"), "w");
        foreach($array as $val) 
            {
            $what = trim($val);
            $x = explode( ",", $what);
            $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
            if ($x[$s-1] >= ($LastDate-2592000)) fputs($fp, $val);
            }
        fclose($fp);

        //Wochen-Datei erzeugen:Eine Woche hat etwa  60*60*24*7 = 604800 Ticks
        $fp = fOpen(($beeOrdner."week.csv"), "w");
        foreach($array as $val) 
            {
            $what = trim($val);
            $x = explode( ",", $what);
            $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
            if ($x[$s-1] >= ($LastDate-604800)) fputs($fp, $val);
            }
        fclose($fp);
    }// Ende function csv-Builder 3.0    


function ExternDataOut()  
    {
    global $MultiType,$ExternDataArray,$ExternDataCounter,$BeepArray,$BeepIdArray,$iBeekeeperArray,$iBeekeeperUIdArray,$NeubeeloggerSketchID,$EE_beelogger;

    for ($ia=0; $ia < $ExternDataCounter; $ia++) 
      { 
      for ($ib=0; $ib < $MultiType; $ib++) 
        { 
        if (($BeepArray[$ib] == "aktiviert") AND ($BeepIdArray[$ib] != "meineID" OR $BeepIdArray[$ib] != ""))
          { //wenn Funktion aktiviert wurde
          $ch = curl_init("https://api.beep.nl/api/sensors?key=".$BeepIdArray[$ib]); // cURL ínitialisieren
          curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
          curl_setopt($ch, CURLOPT_POST, 1); // POST-Request wird abgesetzt
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $ExternDataArray[$ia]); // POST-Felder festlegen, die gesendet werden sollen
          curl_exec($ch); // Ausführen
          curl_close($ch); // Objekt schließen und Ressourcen freigeben
          }// Beep.nl Datenübertragung ende

        if (($iBeekeeperArray[$ib] == "aktiviert") AND ($iBeekeeperUIdArray[$ib] != "meine UId" OR $iBeekeeperUIdArray[$ib] != ""))
          { //wenn Funktion aktiviert wurde
          $ch = curl_init("https://webapp.ibeekeeper.de/deviceapi/v1/".$iBeekeeperUIdArray[$ib]); // cURL ínitialisieren
          curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
          curl_setopt($ch, CURLOPT_POST, 1); // POST-Request wird abgesetzt
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $ExternDataArray[$ia]); // POST-Felder festlegen, die gesendet werden sollen
          curl_exec($ch); // Ausführen
          curl_close($ch); // Objekt schließen und Ressourcen freigeben
          }// ibeekeeper Datenübertragung ende

          if (($ib+1) < $MultiType) $ia++;
        }
      }

    // beeloggerMap: Daten an beeloggerMap übertragen
    if (file_exists("beelogger_map.php")) include("beelogger_map.php"); 

    //MAP1---------------------------------------------------
    $SendbeeloggerMapLocation = str_replace(" ","%20",$beeloggerMapLocation); // Leerzeichen mit "%20" ersetzen
    
    if ($beeloggerMap == "aktiviert" AND $beeloggerMapLocation != "" AND $beeloggerMapLocation != "beelogger-Standort")
      { 

      //zunächst Trachtsituation der letzten 7 Tage berechnen
      if ($beeloggerMap2Waage == "") $beeloggerMap2Waage = 1; //falls Singlebeelogger
      $SensorStelle = 5+$beeloggerMap2Waage;
      if (file_exists("week.csv")) 
        { //eine week.csv gefunden
        $array = file("week.csv");
        $i = sizeof($array);
        $what = trim($array[$i-1]);
        $LetzteZeile = explode( ",",$what);
        while ($i--) 
          { 
          $what = trim($array[$i]);    
          $x = explode( ",", $what );

          $s = sizeof($x);
          if ($x[$s-1] !='') 
              {
              $LastTrachtGewicht = $x[$SensorStelle]; //letzter Wert
              break;
              }
          } //while

        $what = trim($array[0]);  //erster Wert  
        $x = explode( ",", $what );
        $FirstTrachtGewicht = $x[$SensorStelle];
        if ($LastTrachtGewicht > 150 ) $Trachtdurchschnitt = round((($LastTrachtGewicht - $FirstTrachtGewicht)/7000),2); //Grammbereich
        else $Trachtdurchschnitt = round(($LastTrachtGewicht - $FirstTrachtGewicht)/7,2);
        }  //KG-Bereich
        
      //Übertragung der Map1-Daten----------------------------
      $curlString = "https://map.beelogger.de/log.php?key1=".$beeloggerMapId1."&loc=".$SendbeeloggerMapLocation."&t1=".$Trachtdurchschnitt;
      $ch = curl_init($curlString); // cURL ínitialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $ServerAntwort = curl_exec($ch); // Ausführen
      curl_close($ch); // Objekt schließen und Ressourcxen freigeben
      $CurlArray[8] = ("beelogger_log: {".$ServerAntwort."}_vom :".date("m/d H:i:s")."_beelogger_log_Daten an Map1 gesendet");
      } //if ($beeloggerMap == "aktiviert")
      // beeloggerMap1 Datenübertragung ende


    //MAP2-------------------------------------------------------------------
    if (file_exists("../general_ini.php")) include("../general_ini.php"); 
      
    if ($beeloggerMap2 == "aktiviert")
      {  
      //zunächst Trachtsituation der letzten 7 Tage berechnen
      if ($beeloggerMap2Waage == "") $beeloggerMap2Waage = 1; //falls Singlebeelogger
      $GewichtSensorStelle = 5+$beeloggerMap2Waage;
      $RegenSensorStelle = $beeloggerMap2Sensoren[3]+1; //Sendesensor4

      if (file_exists("week.csv")) 
        { //eine week.csv gefunden
        $array = file("week.csv");
        $i = sizeof($array);
        $what = trim($array[$i-1]);
        $LetzteZeile = explode( ",",$what);
        while ($i--) 
          { 
          $what = trim($array[$i]);    
          $x = explode( ",", $what );
          $s = sizeof($x);
          if ($x[$s-1] !='' AND $LastTrachtGewicht == "")
              { 
              $LastTrachtGewicht = $x[$GewichtSensorStelle]; //letzter Wert
              $LastZeitpunkt = $x[$s-1];
              //break;
              }
          if (($x[$s-1]+86400) >= $LastZeitpunkt) $RegenSumme24h += $x[$RegenSensorStelle];
          else break;
          } //while

        $what = trim($array[0]);  //erster Wert  
        $x = explode( ",", $what );
        $ZeitSpanne = ($LastZeitpunkt - $x[$s-1])/(3600*24); //Tage der Betrachtung
        $FirstTrachtGewicht = $x[$GewichtSensorStelle];
        if ($LastTrachtGewicht > 0 OR $FirstTrachtGewicht > 0 OR $LastTrachtGewicht < 0 OR $FirstTrachtGewicht < 0) //falls brauchbare Werte vorhanden
          {
          if ($LastTrachtGewicht > 150 ) $Trachtdurchschnitt = round((($LastTrachtGewicht - $FirstTrachtGewicht)/($ZeitSpanne*1000)),2);
          else $Trachtdurchschnitt = round((($LastTrachtGewicht - $FirstTrachtGewicht)/$ZeitSpanne),2);
          }
        else $Trachtdurchschnitt = "";
        } 


      //Übertragung der Map2-Daten----------------------------
      $curlerString = "https://map2.beelogger.de/log.php?mapid=".$beeloggerMap2ID."&beeid=".$beeloggerMap2BeeID."&do=164052&tracht=".$Trachtdurchschnitt;
      for ($c=0; $c < 7; $c++) 
        { 
        if ($beeloggerMap2Sensoren[$c] != "") 
          {
          if ($c == 3) $curlerString .= "&s4=".$RegenSumme24h; 
          else $curlerString .= "&s".($c+1)."=".$LetzteZeile[($beeloggerMap2Sensoren[$c]+1)];  
          }
        }

      $ch = curl_init($curlerString);
      curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $ServerAntwort = curl_exec($ch); // Ausführen
      curl_close($ch); // Objekt schließen und Ressourcxen freigeben
      $ServerAntwort = str_replace('"','',$ServerAntwort); //Anführungszeichen verboten
      $CurlArray[8] = "beelogger_log: {".$curlerString."}_{".$ServerAntwort."}_vom :".date("m/d H:i:s")."_Daten an Map2 gesendet";
      $beeloggerMap2Status = $ServerAntwort;            
      } //if ($beeloggerMap2 == "aktiviert")
  

    //beeloggerMAP Daten immer speichern!
    $m = 0;
    $MapArray [$m] = "<?php\n";$m++; 
    //MAP1
    if ($beeloggerMap2 != "aktiviert") //nur wenn noch im alten Map-Modus
      {
      $MapArray [$m] = "//MAP1:\n".'$beeloggerMap = "'.$beeloggerMap.'"; //Sollen Daten an beeloggerMap gesendet werden? '."\n";$m++;  
      $MapArray [$m] = '$beeloggerMapId1 = "'.$beeloggerMapId1.'"; //Bekommt man als angemeldeter User automatisch von beelogger.de '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapId2 = "'.$beeloggerMapId2.'"; //Bekommt man als angemeldeter User automatisch von beelogger.de '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapLocation = "'.$beeloggerMapLocation.'"; //Ort des beeloggers '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapStatus = "'.$beeloggerMapStatus.'"; //Ist das Profil komplett? '."\n";$m++;
      }

    //MAP2
    $MapArray [$m] = "//MAP2:\n".'$beeloggerMap2 = "'.$beeloggerMap2.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Location = "'.$beeloggerMap2Location.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2BeeID = "'.$beeloggerMap2BeeID.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Status = "'.$beeloggerMap2Status.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Lat = "'.$beeloggerMap2Lat.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Lon = "'.$beeloggerMap2Lon.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2BeeloggerType = "'.$beeloggerMap2BeeloggerType.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Connect = "'.$beeloggerMap2Connect.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2URL = "'.$beeloggerMap2URL.'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Email = "'.$beeloggerMap2Email.'";'."\n";$m++;
    if ($MultiType > 1)
      { 
      $MapArray [$m] = '$beeloggerMapWaage = "'.$beeloggerMapWaage.'";'."\n";$m++;
      }
    $MapArray [$m] = '$beeloggerMap2Sensoren = array("'.$beeloggerMap2Sensoren[0].'","'.$beeloggerMap2Sensoren[1].'","'.$beeloggerMap2Sensoren[2].'","'.$beeloggerMap2Sensoren[3].'","'.$beeloggerMap2Sensoren[4].'","'.$beeloggerMap2Sensoren[5].'");'."\n";$m++;  
    $MapArray [$m] = '$CurlArray = array('."\n".'"'.$CurlArray[0].'"'.",\n".'"'.$CurlArray[1].'"'.",\n".'"'.$CurlArray[2].'"'.",\n".'"'.$CurlArray[3].'"'.",\n".'"'.$CurlArray[4].'"'.",\n".'"'.$CurlArray[5].'"'.",\n".'"'.$CurlArray[6].'"'.",\n".'"'.$CurlArray[7].'"'.",\n".'"'.$CurlArray[8].'");'."\n";$m++;
    

    //$NeubeeloggerSketchID vom beelogger mitgesendet
    if ($beeloggerSketchID == "" AND $NeubeeloggerSketchID == "" AND $EE_beelogger == "EE") $OutputSketchID = "EE"; //kein Info also wenigstens EE anzeigen
    
    elseif ($NeubeeloggerSketchID == "") $OutputSketchID = $beeloggerSketchID; //Kein Neuinfo alten Stand übernehmen
    else $OutputSketchID = $NeubeeloggerSketchID;

    $MapArray [$m] = '$beeloggerSketchID = "'.$OutputSketchID.'";'."\n";$m++;
    if ($NeubeeloggerSketchID != "") // Nur wenn neue Aussendung
      {
      $MapArray [$m] = '$InfoSketchID = "Info vom:'.date("m/d H:i:s").'";'."\n";$m++;  
      }
    else $MapArray [$m] = '$InfoSketchID = "'.$InfoSketchID.'";'."\n";$m++; 
       
    $fp = fOpen("beelogger_map.php", "w+");
    foreach($MapArray as $values){fputs($fp,$values);}
    $OK = fputs($fp,"?>");
    fclose($fp);
    // beeloggerMap Datenübertragung ende  

    if ($ServerAntwort =! "" && $ext_Show == "1") echo "<br><br>".$LAs[57].": ".$beeloggerMap2Status;
   
    } //Ende function Externdaten übertragen       

function Triggeralarme()  
    {
    global $ext_Show,$Sensoren,$beelogger,$TriggerAlarmArray,$PushToken,$PushUser,$Empfaenger_Email,$Absender_Email,$TriggerGesendetArray,$zeit,$timestamp,$EingehendeDatensätze;

    if (file_exists("Triggeralarm.php")) include("Triggeralarm.php");

    if (file_exists("week.csv")) 
      { //Letzte Zeile aus week.csv auslesen
      $input = "week.csv";
      $array = file($input);
      $j = sizeof($array);
        while ($j--)  
          {
          $what = trim($array[$j]);    
          $LetzteZeile = explode( ",", $what );
          $s = sizeof($LetzteZeile);
          if ($LetzteZeile[$s-1] != '') break;     
          }

      $what = trim($array[$j-$EingehendeDatensätze]); //Letztes Sendedatum ermitteln
      $Zeile = explode( ",", $what);
      $s = sizeof($Zeile);
      $LetztesTriggerSendeDatum =  $Zeile[$s-1];



      while($EingehendeDatensätze > 0) 
        { 
        $what = trim($array[$j+1-$EingehendeDatensätze]);
        $Zeile = explode( ",", $what);
        $s = sizeof($Zeile);

        
          for ($i=1; $i < 5; $i++) 
            {
            if ($TriggerAlarmArray[$i][1] == "aktiviert" OR $TriggerAlarmArray[$i][2] == "aktiviert")
              {
              $TriggerErkannt = false; //INIT
              $triggernote = ""; //INIT
      
              if  ($TriggerAlarmArray[$i][4] == "<")
                { 

                if ($Zeile[($TriggerAlarmArray[$i][3]+1)] < $TriggerAlarmArray[$i][5])
                  {
                  $TriggerErkannt = true;
                  }
                }
              else 
                { 

                if ($Zeile[($TriggerAlarmArray[$i][3]+1)] > $TriggerAlarmArray[$i][5]) 
                  {
                  $TriggerErkannt = true;
                  }
                }

                if ($TriggerErkannt) 
                  {
                  $Nachricht  = $beelogger." meldet einen Triggeralarm um ".date("H:i",$Zeile[$s-1])." Uhr\n\n".'Der Sensor "'.html_entity_decode($Sensoren[(($TriggerAlarmArray[$i][3])*5)]).'" hat mit dem Wert '.$Zeile[($TriggerAlarmArray[$i][3]+1)]." (".$TriggerAlarmArray[$i][4]." als ".$TriggerAlarmArray[$i][5].") eine Nachricht ausgelöst.";

                  if ($TriggerAlarmArray[$i][6] < 9000) 
                    {
                    $Nachricht .= "\nDer nächst mögliche Alarm erfolgt frühstens in: ";
                    
                    $TimeNow = $Zeile[$s-1];
                    switch($TriggerAlarmArray[$i][6])
                      {
                      case 5:
                      $Nachricht .= "5 Minuten - um ".date("H:i",($TimeNow+5*60))." Uhr.";break;
                      case 30:
                      $Nachricht .= "30 Minuten - um ".date("H:i",($TimeNow+30*60))." Uhr.";break;
                      case 60:
                      $Nachricht .= "einer Stunde - um ".date("H:i",($TimeNow+60*60))." Uhr.";break;
                      case 720:
                      $Nachricht .= "12 Stunden - um ".date("H:i",($TimeNow+720*60))." Uhr.";break;
                      case 1440:
                      $Nachricht .= "24 Stunden - um ".date("H:i",($TimeNow+1440*60))." Uhr.";break;
                      }

                    }
                  else 
                    { 
                    $Nachricht .= "\nDer Triggeralarm wurde nun automatisch deaktiviert und kann in der Konfiguration neu aktiviert werden.";
                    }
                  $actual_link = strstr("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'beelogger_log',TRUE)."beelogger_show.php";
                  $Nachricht .= "\nLink zum ".$beelogger.": ".$actual_link;  
                  }
              

              if ($TriggerErkannt && (($TimeNow-$TriggerGesendetArray[$i]) > ($TriggerAlarmArray[$i][6]*60)) && $TriggerGesendetArray[$i] != "deaktiviert")
                {           

                if ($TriggerAlarmArray[$i][1] == "aktiviert") 
                  {//Sende Email

                  if ($Empfaenger_Email != "empfaenger@meineDomain.de") 
                      {  
                      $Mailbetreff = $beelogger."-Triggeralarm";
                      
                      $Absender_Name = $beelogger."-Triggeralarm";
                      $Header  = "From:".$Absender_Name." <".$Absender_Email.">\n";     
                      //$Nachricht .= "\nLink: ".$actual_link;
                      mail($Empfaenger_Email, $Mailbetreff, $Nachricht, $Header);
                      if ($ext_Show=='1') echo "<br>"."Triggeralarm".$i." per Mail versendet: ".$Nachricht;
                      //$TriggerGesendetArray[$i][0] = true;
                      $TriggerGesendetArray[$i] = $Zeile[$s-1];
                      if ($TriggerAlarmArray[$i][6] == 9999) $TriggerGesendetArray[$i] = "deaktiviert";
                      if ($TriggerAlarmArray[$i][7] == "aktiviert") $triggernote = $TriggerAlarmArray[$i][3].",".$Zeile[0].",../beelogger_icons/n_Triggeralarm.png&Mit dem Wert: ".$Zeile[($TriggerAlarmArray[$i][3]+1)]." (".$TriggerAlarmArray[$i][4]." als ".$TriggerAlarmArray[$i][5].") wurde ein Mail-Triggeralarm ausgelöst,".$timestamp."\r\n";
                      } //if EmailAlarm aktiviert

                             

                  }
                if ($TriggerAlarmArray[$i][2] == "aktiviert")
                  {//Sende Pushnachricht
                  curl_setopt_array(
                  $ch = curl_init(), array(
                    CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                    CURLOPT_POSTFIELDS => array(
                          "token" => $PushToken,
                          "user" => $PushUser,
                          "message" => $Nachricht,),
                    CURLOPT_SAFE_UPLOAD => true,
                    CURLOPT_RETURNTRANSFER => true,));
                    curl_exec($ch);
                    curl_close($ch);
                    if ($ext_Show=='1') echo "<br>"."Triggeralarm".$i." per Push versendet: ".$Nachricht;
                    $TriggerGesendetArray[$i] = $Zeile[$s-1];
                    if ($TriggerAlarmArray[$i][6] == 9999) $TriggerGesendetArray[$i] = "deaktiviert";
                    if ($TriggerAlarmArray[$i][7] == "aktiviert") $triggernote = $TriggerAlarmArray[$i][3].",".$Zeile[0].",../beelogger_icons/n_Triggeralarm.png&Mit dem Wert: ".$Zeile[($TriggerAlarmArray[$i][3]+1)]." (".$TriggerAlarmArray[$i][4]." als ".$TriggerAlarmArray[$i][5].") wurde ein Push-Triggeralarm ausgelöst,".$timestamp."\r\n";  
                  } //if PushSchwarmAlarm aktiviert

                  if ($triggernote != "")
                    {
                    $aktion = fOpen("notes.csv", "a+");
                    fWrite($aktion,$triggernote);
                    fClose($aktion);
                    }
                    
                }

              }

            } //for

           

          $EingehendeDatensätze--;
          }  // while
        }
        
         $TriggerDatei [0] = "<?php // Hilfs-Datei zur Steurerung der Triggeralarm\n";
            $TriggerDatei [1] = '$TriggerGesendetArray = array("WannGesendet?","'.$TriggerGesendetArray[1].'","'.$TriggerGesendetArray[2].'","'.$TriggerGesendetArray[3].'","'.$TriggerGesendetArray[4].'");'."\n?>";
                       
            $aktion = fOpen("Triggeralarm.php", "w");
            foreach($TriggerDatei as $values) fputs($aktion,$values);
            fClose($aktion);

    } //Ende Triggeralarme
?>  
    
