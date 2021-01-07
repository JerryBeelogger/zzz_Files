<?php 
/*
 * (C) 2019 Jeremias Bruker, Thorsten Gurzan, Rudolf Schick - beelogger.de
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


$Softwareversion = "M.15";//vom 25.11.2020 - beelogger_show.php
$show_version = 0;  //Programmertool

$isHttps = (!empty($_SERVER['HTTPS']));
if($isHttps == 0){
    echo 'Webseite   ';
    echo $_SERVER['SERVER_NAME'];
    echo ' bitte mit https:// aufrufen';
    exit;
}

error_reporting(0);

if (file_exists("home.php")) include ("home.php");
if (file_exists("Triggeralarm.php")) include("Triggeralarm.php");

SESSION_START();

if  ($_SERVER['SERVER_NAME'] == "testsystem.beelogger.de" OR $_SERVER['SERVER_NAME'] == "community.beelogger.de")  $CommunityUser = true;

                                       

$Sprache = 1; // INIT falls noch keine Speicherung stattfand
if (file_exists("../general_ini.php")) include("../general_ini.php"); //Sprache abfragen
if ($Sprache == "") $Sprache = 1; // INIT
if (file_exists("../beelogger_sprachfiles/Show_Sprache_".$Sprache.".php")) include ("../beelogger_sprachfiles/Show_Sprache_".$Sprache.".php"); // Sprache einbinden


if ($show_version) error_reporting(E_ERROR | E_WARNING | E_PARSE);
else error_reporting(0);
date_default_timezone_set('Europe/Berlin');

if (file_exists("beelogger.ini")) 
    {
    rename("beelogger.ini","beelogger_ini.php");
    unlink ("beelogger.ini");
    }

$ForscherDateiName = basename(__DIR__).".csv"; //Datei löschen
if (file_exists($ForscherDateiName)) unlink($ForscherDateiName);


$AnzahlSensoren = $_POST['alteanzahlsensoren'];

$beelogger = substr(dirname(__FILE__), strrpos(dirname(__FILE__),"/")+1);

if(strchr($beelogger,"\\")){
  $beelogger = substr(dirname(__FILE__), strrpos(dirname(__FILE__),"\\")+1);
}

if (strlen($beelogger) < 12 && strpos($beelogger,"beelogger") !== FALSE) 
  {
  $MultiTypeName = "";
  $MultiSign = "";
  $ServerMultiNumber = intval(str_replace("beelogger","", $beelogger));
  $MultiType = 1; //anzahl der Waagen pro beelogger
  }
elseif (strpos($beelogger,'Duo') === 0) // Duo an Position1
  {
  $MultiTypeName = "Duo";
  $MultiSign = "D";
  $ServerMultiNumber = intval(str_replace("Duo","", $beelogger));
  $MultiType =  2;  //anzahl der Waagen pro beelogger
  }
elseif (strpos($beelogger,'Triple') === 0) 
  {
  $MultiTypeName = "Triple";
  $MultiSign = "T";
  $ServerMultiNumber = intval(str_replace("Triple","", $beelogger));
  $MultiType =  3;
  }  
elseif (strpos($beelogger,'Quad') === 0) 
  {
  $MultiTypeName = "Quad";
  $MultiSign = "Q";
  $ServerMultiNumber = intval(str_replace("Quad","", $beelogger));
  $MultiType =  4;  
  }
elseif (strpos($beelogger,'Penta') === 0) 
  {
  $MultiTypeName = "Penta";
  $MultiSign = "P";
  $ServerMultiNumber = intval(str_replace("Penta","", $beelogger));
  $MultiType =  5;  
  }
elseif (strpos($beelogger,'Hexa') === 0) 
  {
  $MultiTypeName = "Hexa";
  $MultiSign = "H";
  $ServerMultiNumber = intval(str_replace("Hexa","", $beelogger));
  $MultiType =  6;  
  }  
elseif (strpos($beelogger,'Sept') === 0) 
  {
  $MultiTypeName = "Sept";
  $MultiSign = "S";
  $ServerMultiNumber = intval(str_replace("Sept","", $beelogger));
  $MultiType =  7;  
  }
else 
  {
  $MultiType =  0;   //Unterordner der Multis
  $MultiSign = substr(str_replace("beelogger","",$beelogger),0,1); //D,T,Q,P oder S
  $ServerMultiNumber = intval(substr(str_replace("beelogger","",$beelogger), -3, 1));
  $ServerMultiUnterordnerNummer = intval(substr($beelogger,-1,1));
  switch ($MultiSign) 
      {
      case 'D':
        $MultiTypeName = "Duo";
        $MultiTypeAnzahl = 2;
        break;
      case 'T':
        $MultiTypeName = "Triple";
        $MultiTypeAnzahl = 3;
        break;
      case 'Q':
        $MultiTypeName = "Quad";
        $MultiTypeAnzahl = 4;
        break;
      case 'P':
        $MultiTypeName = "Penta";
        $MultiTypeAnzahl = 5;
        break;
      case 'H':
        $MultiTypeName = "Hexa";
        $MultiTypeAnzahl = 6;
        break;
      case 'S':
        $MultiTypeName = "Sept";
        $MultiTypeAnzahl = 7;
        break;
      }
  $WechselIniName =  "../".$MultiTypeName.$ServerMultiNumber."/beelogger_ini.php";
  $WechselMapName =  "../".$MultiTypeName.$ServerMultiNumber."/beelogger_map.php";
  $WechselWetterName =  "../".$MultiTypeName.$ServerMultiNumber."/wetter_daten.php";
  $WechselLocName=  "../".$MultiTypeName.$ServerMultiNumber."/loc.php";
  }


//POST-Variablen für Anmerkungen
$Termin = $_POST['termin'];
$Sensor = $_POST['sensor'];
$Kurztext=htmlentities(strip_tags(stripslashes($_POST['kurztext'])));
$Note_icon = $_POST['note_icon'];
$Langtext=strip_tags(stripslashes($_POST['langtext']));
$Langtext = str_replace("<", "", $Langtext);
$Langtext = str_replace('"', "'", $Langtext);
$Langtext = str_replace(',', ";", $Langtext);
$Erzeugen = $_POST['erzeugen'];
$Loeschen = $_POST['loeschen'];
$Aendern = $_POST['aendern'];
$Speichern = $_POST['speichern'];
$Wertloeschen = $_POST['wertloeschen']; 
$NeuWert = htmlentities(strip_tags(stripslashes($_POST['neuwert'])));

$SchwarmalarmLoeschen = $_POST['schwarmalarmloeschen'];
$SchwarmalarmLoeschenArray[0] = $_POST['schwarmalarmloeschen0'];
$SchwarmalarmLoeschenArray[1] = $_POST['schwarmalarmloeschen1'];
if ($SchwarmalarmLoeschen == "1") $SchwarmalarmLoeschenArray[1] = "1";
$SchwarmalarmLoeschenArray[2] = $_POST['schwarmalarmloeschen2'];
$SchwarmalarmLoeschenArray[3] = $_POST['schwarmalarmloeschen3'];
$SchwarmalarmLoeschenArray[4] = $_POST['schwarmalarmloeschen4'];
$SchwarmalarmLoeschenArray[5] = $_POST['schwarmalarmloeschen5'];
$SchwarmalarmLoeschenArray[6] = $_POST['schwarmalarmloeschen6'];
$SchwarmalarmLoeschenArray[7] = $_POST['schwarmalarmloeschen7'];

//POST-Variablen zum Logdateisplitten
$Splitten = $_POST['splitten'];

$ArchivDateiName = $_POST['archivdateiname'];
$ErstesSplitDatum = $_POST['erstessplitdatum'];
$LetztesSplitDatum = $_POST['letztessplitdatum'];
$AktuellesSplitDatum = $_POST['aktuellessplitdatum'];
$SplittenSichern = $_POST['splittensichern'];

$NeubeeloggerAnlage = $_POST['beeloggeranlage'];
$NeubeeloggerAnlagePasswort = $_POST['beeloggeranlagepasswort'];
$BeeloggerLoeschen = $_POST['beeloggerloeschen'];

//POST-Variablen für beelogger_ini.php
$Config = $_POST['config'];
$ConfigSichern = $_POST['configsichern'];

$NeuEmpfaenger_Email = htmlentities(strip_tags(stripslashes($_POST['neuempfaenger_email'])));
$NeuAbsender_Email = htmlentities(strip_tags(stripslashes($_POST['neuabsender_email'])));
    
    if ($CommunityUser) 
      { //Email auf beeloggerserver nur für CommunityUser
      if  ($_SERVER['SERVER_NAME'] == "testsystem.beelogger.de") $MailUser = substr(dirname(__FILE__), strrpos(dirname(__FILE__),"testsystem/")+11);
      if  ($_SERVER['SERVER_NAME'] == "community.beelogger.de") $MailUser = substr(dirname(__FILE__), strrpos(dirname(__FILE__),"community/")+10);
      $MailUser = substr($MailUser,0,strrpos($MailUser,"/"));

      include("../../CommunityMember.php"); // holt Array $Member
      $UserArraySize = sizeof($Member);

      $u = 0;
      while ($u <= $UserArraySize) 
        {
        if ($Member[$u][0] == $MailUser) break;
        $u++;
        }
        if ($u == ($UserArraySize+1))
          {
          $TestAccountUser = TRUE;  
          $NeuEmpfaenger_Email = "empfaenger@meineDomain.de";
          }
        $NeuAbsender_Email = "alarm@beelogger.de";  
      } 

if ($ConfigSichern == "1")
  { //Variablen nur annehmen, wenn Konfig gespeichert wurde
$NeuBeeloggerShowPasswort = $_POST['neubeeloggershowpasswort'];
  $CheckNeuBeeloggerShowPasswort =htmlentities(strip_tags(stripslashes($NeuBeeloggerShowPasswort)));
  if ($NeuBeeloggerShowPasswort == "" OR ($NeuBeeloggerShowPasswort != $CheckNeuBeeloggerShowPasswort)) $NeuBeeloggerShowPasswort = "Show"; // ein leeres Passwort ist nicht erlaubt
  //$NeuBeeloggerLogPasswort = $_POST['neubeeloggerlogpasswort'];
  $NeuBeeloggerLogPasswort =htmlentities(strip_tags(stripslashes($_POST['neubeeloggerlogpasswort'])));
  if (preg_match("#[\!\*\_\'\(\)\;\:\@\&\=\+\$\,\/\?\%\#\[\]]#", $NeuBeeloggerLogPasswort))
    {  
    $LogAlert = TRUE;
    $NeuBeeloggerLogPasswort = "Log"; // zurücksetzen
    }


  $NeuBienenvolkbezeichnung =htmlentities(strip_tags(stripslashes($_POST['neubienenvolkbezeichnung'])));
  $NeuStandardCSVDatei = htmlentities(strip_tags(stripslashes($_POST['neustandardcsvdatei'])));
  $NeuPunktAnzeige = htmlentities(strip_tags(stripslashes($_POST['neupunktanzeige'])));
  $NeuTageswertAnzeige = htmlentities(strip_tags(stripslashes($_POST['tageswertanzeige'])));
  $NeuLegende = htmlentities(strip_tags(stripslashes($_POST['neulegende'])));
  $NeuRollPeriod = htmlentities(strip_tags(stripslashes($_POST['rollperiod'])));
  $CsvLoeschDatei = htmlentities(strip_tags(stripslashes($_POST['csvloeschdatei'])));

  $NeuIntervallSendeSteuerung = htmlentities(strip_tags(stripslashes($_POST['neuintervallsendesteuerung'])));
  $NeuSommerBeginn = htmlentities(strip_tags(stripslashes($_POST['neusommerbeginn'])));
  $NeuSommerTagZeit = htmlentities(strip_tags(stripslashes($_POST['neusommertagzeit'])));
  $NeuSommerSendeIntervallTag = htmlentities(strip_tags(stripslashes($_POST['neusommersendeintervalltag'])));
  $NeuSommerNachtZeit = htmlentities(strip_tags(stripslashes($_POST['neusommernachtzeit'])));
  $NeuSommerSendeIntervallNacht = htmlentities(strip_tags(stripslashes($_POST['neusommersendeintervallnacht'])));
  $NeuWinterBeginn = htmlentities(strip_tags(stripslashes($_POST['neuwinterbeginn'])));

  $NeuWinterSendeIntervall = htmlentities(strip_tags(stripslashes($_POST['neuwintersendeintervall'])));

  $NeuEESendeIntervall = htmlentities(strip_tags(stripslashes($_POST['neueesendeintervall'])));

  $NeuKorrekturwert = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert'])));
  $NeuKorrekturwert = str_replace(",", ".",$NeuKorrekturwert);
  $NeuKorrekturwert1 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert1'])));
  $NeuKorrekturwert1 = str_replace(",", ".",$NeuKorrekturwert1);
  $NeuKorrekturwert2 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert2'])));
  $NeuKorrekturwert2 = str_replace(",", ".",$NeuKorrekturwert2);
  $NeuKorrekturwert3 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert3'])));
  $NeuKorrekturwert3 = str_replace(",", ".",$NeuKorrekturwert3);
  $NeuKorrekturwert4 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert4'])));
  $NeuKorrekturwert4 = str_replace(",", ".",$NeuKorrekturwert4);
  $NeuKorrekturwert5 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert5'])));
  $NeuKorrekturwert5 = str_replace(",", ".",$NeuKorrekturwert5);
  $NeuKorrekturwert6 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert6'])));
  $NeuKorrekturwert6 = str_replace(",", ".",$NeuKorrekturwert6);
  $NeuKorrekturwert7 = htmlentities(strip_tags(stripslashes($_POST['neukorrekturwert7'])));
  $NeuKorrekturwert7 = str_replace(",", ".",$NeuKorrekturwert7);


  $NeuKalibrierTemperatur = htmlentities(strip_tags(stripslashes($_POST['neukalibriertemperatur'])));

  $NeuWatchdog = htmlentities(strip_tags(stripslashes($_POST['neuwatchdog'])));
  $NeuGeneralAutoWatch = htmlentities(strip_tags(stripslashes($_POST['neugeneralautowatch'])));
  $NeuGeneralAutoWatchTime = htmlentities(strip_tags(stripslashes($_POST['neugeneralautowatchtime'])));

  $NeuBeutenLeergewicht = htmlentities(strip_tags(stripslashes($_POST['neubeutenleergewicht'])));
  $NeuHonigraum1Anzahl = htmlentities(strip_tags(stripslashes($_POST['neuhonigraum1anzahl'])));
  $NeuHonigraum1Leergewicht = htmlentities(strip_tags(stripslashes($_POST['neuhonigraum1leergewicht'])));
  $NeuHonigraum2Anzahl = htmlentities(strip_tags(stripslashes($_POST['neuhonigraum2anzahl'])));
  $NeuHonigraum2Leergewicht = htmlentities(strip_tags(stripslashes($_POST['neuhonigraum2leergewicht'])));
  
  $NeuUtil1Leergewicht = htmlentities(strip_tags(stripslashes($_POST['neuutil1leergewicht'])));
  $NeuUtil1 = htmlentities(strip_tags(stripslashes($_POST['neuutil1'])));

  $NeuUtil2Leergewicht = htmlentities(strip_tags(stripslashes($_POST['neuutil2leergewicht'])));
  $NeuUtil2 = htmlentities(strip_tags(stripslashes($_POST['neuutil2'])));
  
  $NeuUtil3Leergewicht = htmlentities(strip_tags(stripslashes($_POST['neuutil3leergewicht'])));
  $NeuUtil3 = htmlentities(strip_tags(stripslashes($_POST['neuutil3'])));




  $NeuAkkuLeerSchwelle = htmlentities(strip_tags(stripslashes($_POST['neuakkuleerschwelle'])));
  $NeuAkkuVollSchwelle = htmlentities(strip_tags(stripslashes($_POST['neuakkuvollschwelle'])));

  //$NeuAkkuSpannungKorrektur = htmlentities(strip_tags(stripslashes($_POST['neuakkuspannungkorrektur'])));
  //$NeuSolarSpannungKorrektur = htmlentities(strip_tags(stripslashes($_POST['neusolarspannungkorrektur'])));

  $NeuSprache = htmlentities(strip_tags(stripslashes($_POST['neusprache'])));

  $NeuNextService = htmlentities(strip_tags(stripslashes($_POST['neunextservice'])));
  $StandortLoeschen = htmlentities(strip_tags(stripslashes($_POST['standortloeschen'])));

  $NeuAutoAnmerkungenErzeugen = htmlentities(strip_tags(stripslashes($_POST['neuautoanmerkungenerzeugen'])));
  $NeuAnmerkungGewichtsDifferenz = htmlentities(strip_tags(stripslashes($_POST['neuanmerkunggewichtsdifferenz'])));
  $NeuAnmerkungZeitDifferenz = htmlentities(strip_tags(stripslashes($_POST['neuanmerkungzeitdifferenz'])));
  $NeuAutoServiceAnmerkung = htmlentities(strip_tags(stripslashes($_POST['neuautoserviceanmerkung'])));
  $NeuAutoServiceAnmerkungZeit = htmlentities(strip_tags(stripslashes($_POST['neuautoserviceanmerkungzeit'])));
  

  $NeuEmailSchwarmAlarm = $_POST['neuemailschwarmalarm'];
  $NeuEmailSchwarmAlarm1 = $_POST['neuemailschwarmalarm1'];
  $NeuEmailSchwarmAlarm2 = $_POST['neuemailschwarmalarm2'];
  $NeuEmailSchwarmAlarm3 = $_POST['neuemailschwarmalarm3'];
  $NeuEmailSchwarmAlarm4 = $_POST['neuemailschwarmalarm4'];
  $NeuEmailSchwarmAlarm5 = $_POST['neuemailschwarmalarm5'];
  $NeuEmailSchwarmAlarm6 = $_POST['neuemailschwarmalarm6'];
  $NeuEmailSchwarmAlarm7 = $_POST['neuemailschwarmalarm7'];

  $NeuReferenzZeit= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit'])));
  $NeuReferenzZeit1= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit1'])));
  $NeuReferenzZeit2= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit2'])));
  $NeuReferenzZeit3= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit3'])));
  $NeuReferenzZeit4= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit4'])));
  $NeuReferenzZeit5= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit5'])));
  $NeuReferenzZeit6= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit6'])));
  $NeuReferenzZeit7= htmlentities(strip_tags(stripslashes($_POST['neureferenzzeit7'])));

  $NeuDifferenzGewicht = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht'])));
  $NeuDifferenzGewicht1 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht1'])));
  $NeuDifferenzGewicht2 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht2'])));
  $NeuDifferenzGewicht3 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht3'])));
  $NeuDifferenzGewicht4 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht4'])));
  $NeuDifferenzGewicht5 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht5'])));
  $NeuDifferenzGewicht6 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht6'])));
  $NeuDifferenzGewicht7 = htmlentities(strip_tags(stripslashes($_POST['neudifferenzgewicht7'])));

 
  $NeuPushSchwarmAlarm = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm'])));
  $NeuPushSchwarmAlarm1 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm1'])));
  $NeuPushSchwarmAlarm2 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm2'])));
  $NeuPushSchwarmAlarm3 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm3'])));
  $NeuPushSchwarmAlarm4 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm4'])));
  $NeuPushSchwarmAlarm5 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm5'])));
  $NeuPushSchwarmAlarm6 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm6'])));
  $NeuPushSchwarmAlarm7 = htmlentities(strip_tags(stripslashes($_POST['neupushschwarmalarm7'])));

  $NeuTriggerAlarmEmail1 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmemail1'])));
  $NeuTriggerAlarmEmail2 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmemail2'])));
  $NeuTriggerAlarmEmail3 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmemail3'])));
  $NeuTriggerAlarmEmail4 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmemail4'])));
  
  $NeuTriggerAlarmPush1 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpush1'])));
  $NeuTriggerAlarmPush2 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpush2'])));
  $NeuTriggerAlarmPush3 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpush3'])));
  $NeuTriggerAlarmPush4 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpush4'])));

  $NeuTriggerAlarmSensor1 = $_POST['neutriggeralarmsensor1'];
  $NeuTriggerAlarmSensor2 = $_POST['neutriggeralarmsensor2'];
  $NeuTriggerAlarmSensor3 = $_POST['neutriggeralarmsensor3'];
  $NeuTriggerAlarmSensor4 = $_POST['neutriggeralarmsensor4'];
  
  $NeuTriggerAlarmZeichen1 = $_POST['neutriggeralarmzeichen1'];
  $NeuTriggerAlarmZeichen2 = $_POST['neutriggeralarmzeichen2'];
  $NeuTriggerAlarmZeichen3 = $_POST['neutriggeralarmzeichen3'];
  $NeuTriggerAlarmZeichen4 = $_POST['neutriggeralarmzeichen4'];

  $NeuTriggerAlarmWert1 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmwert1'])));
  $NeuTriggerAlarmWert2 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmwert2'])));
  $NeuTriggerAlarmWert3 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmwert3'])));
  $NeuTriggerAlarmWert4 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmwert4'])));

  $NeuTriggerAlarmPause1 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpause1'])));
  if ($NeuTriggerAlarmPause1 < 1000) $NeuTriggerAlarmEmail1 = "deaktiviert";
  $NeuTriggerAlarmPause2 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpause2'])));
  if ($NeuTriggerAlarmPause2 < 1000) $NeuTriggerAlarmEmail2 = "deaktiviert";
  
  $NeuTriggerAlarmPause3 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpause3'])));
  if ($NeuTriggerAlarmPause3 < 1000) $NeuTriggerAlarmEmail3 = "deaktiviert";
  
  $NeuTriggerAlarmPause4 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmpause4'])));
  if ($NeuTriggerAlarmPause4 < 1000) $NeuTriggerAlarmEmail4 = "deaktiviert";
  

    $NeuTriggerAlarmAnmerkung1 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmanmerkung1'])));
  $NeuTriggerAlarmAnmerkung2 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmanmerkung2'])));
  $NeuTriggerAlarmAnmerkung3 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmanmerkung3'])));
  $NeuTriggerAlarmAnmerkung4 = htmlentities(strip_tags(stripslashes($_POST['neutriggeralarmanmerkung4'])));

  

  $NeuPushToken = htmlentities(strip_tags(stripslashes($_POST['neupushtoken'])));
  $NeuPushUser = htmlentities(strip_tags(stripslashes($_POST['neupushuser'])));

  $NeuBeep = htmlentities(strip_tags(stripslashes($_POST['neubeep'])));
  $NeuBeep1 = htmlentities(strip_tags(stripslashes($_POST['neubeep1'])));
  $NeuBeep2 = htmlentities(strip_tags(stripslashes($_POST['neubeep2'])));
  $NeuBeep3 = htmlentities(strip_tags(stripslashes($_POST['neubeep3'])));
  $NeuBeep4 = htmlentities(strip_tags(stripslashes($_POST['neubeep4'])));
  $NeuBeep5 = htmlentities(strip_tags(stripslashes($_POST['neubeep5'])));
  $NeuBeep6 = htmlentities(strip_tags(stripslashes($_POST['neubeep6'])));
  $NeuBeep7 = htmlentities(strip_tags(stripslashes($_POST['neubeep7'])));


  $NeuBeepId = htmlentities(strip_tags(stripslashes($_POST['neubeepid'])));
  $NeuBeepId1 = htmlentities(strip_tags(stripslashes($_POST['neubeepid1'])));
  $NeuBeepId2 = htmlentities(strip_tags(stripslashes($_POST['neubeepid2'])));
  $NeuBeepId3 = htmlentities(strip_tags(stripslashes($_POST['neubeepid3'])));
  $NeuBeepId4 = htmlentities(strip_tags(stripslashes($_POST['neubeepid4'])));
  $NeuBeepId5 = htmlentities(strip_tags(stripslashes($_POST['neubeepid5'])));
  $NeuBeepId6 = htmlentities(strip_tags(stripslashes($_POST['neubeepid6'])));
  $NeuBeepId7 = htmlentities(strip_tags(stripslashes($_POST['neubeepid7'])));


  $NeuiBeekeeper = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper'])));
  $NeuiBeekeeper1 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper1'])));
  $NeuiBeekeeper2 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper2'])));
  $NeuiBeekeeper3 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper3'])));
  $NeuiBeekeeper4 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper4'])));
  $NeuiBeekeeper5 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper5'])));
  $NeuiBeekeeper6 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper6'])));
  $NeuiBeekeeper7 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeper7'])));


  $NeuiBeekeeperUId = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid'])));
  $NeuiBeekeeperUId1 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid1'])));
  $NeuiBeekeeperUId2 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid2'])));
  $NeuiBeekeeperUId3 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid3'])));
  $NeuiBeekeeperUId4 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid4'])));
  $NeuiBeekeeperUId5 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid5'])));
  $NeuiBeekeeperUId6 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid6'])));
  $NeuiBeekeeperUId7 = htmlentities(strip_tags(stripslashes($_POST['neuibeekeeperuid7'])));

//Openweathermap für externe User
  if (!$CommunityUser) $NeuOpenweathermapKey = htmlentities(strip_tags(stripslashes($_POST['neuopenweathermapkey'])));
  $NeuWetterIcons = $_POST['neuwettericons'];
  
  //$NeuExWetterDaten = $_POST['neuexwetterdaten'];



//MAP1--------------------------------------------------------------
  $NeubeeloggerMap = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap'])));
  $NeubeeloggerMapId1 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermapid1'])));
  $NeubeeloggerMapId2 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermapid2'])));
  $NeubeeloggerMapLocation = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermaplocation'])));
  $NeubeeloggerMapStatus = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermapstatus'])));


  $NeubeeloggerMap1ToMap2 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap1tomap2'])));

//MAP2--------------------------------------------------------------
  $NeubeeloggerMap2 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2'])));
  $NeubeeloggerMap2ID = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2id'])));

  $NeubeeloggerMap2Key= htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2key'])));
  $NeubeeloggerMap2BeeID= htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2beeid'])));
  $NeubeeloggerMap2Location = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2location'])));
  $NeubeeloggerMap2Lat = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2lat'])));
  $NeubeeloggerMap2Lon = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2lon'])));
  $NeubeeloggerMap2BeeloggerType = htmlentities(strip_tags(stripslashes($_POST['beeloggermap2beeloggertype'])));
  $NeubeeloggerMap2Connect = htmlentities(strip_tags(stripslashes($_POST['beeloggermap2connect'])));
  $NeubeeloggerMap2URL = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2url'])));
  $NeubeeloggerMap2Email = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2email'])));

  $NeubeeloggerMap2Teilnahme = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2teilnahme'])));
  $NeubeeloggerMap2Status = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2status'])));
  $NeubeeloggerMapWaage = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermapwaage'])));

  $NeubeeloggerMap2Sensor1 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor1'])));
   $NeubeeloggerMap2Sensor2 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor2'])));
    $NeubeeloggerMap2Sensor3 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor3'])));
      $NeubeeloggerMap2Sensor4 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor4'])));
        $NeubeeloggerMap2Sensor5 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor5'])));
          $NeubeeloggerMap2Sensor6 = htmlentities(strip_tags(stripslashes($_POST['neubeeloggermap2sensor6'])));
  $beeloggerMap2LoescheBeeID = htmlentities(strip_tags(stripslashes($_POST['beeloggermap2loeschebeeid'])));
//--------------------------------------------------------------------------


  $NeuInfo = htmlentities(strip_tags(stripslashes($_POST['neuinfo'])));
  $NeubeeloggerSketchID = htmlentities(strip_tags(stripslashes($_POST['beeloggersketchid'])));

  $NeumobileWatch_Show = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_show'])));
  $NeumobileWatch_Sort = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_sort'])));
  $NeumobileWatch_sensor1 = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_sensor1'])));
  $NeumobileWatch_sensor2 = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_sensor2'])));
  $NeumobileWatch_roll = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_roll'])));
  $NeumobileWatch_tageswertanzeige = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_tageswertanzeige'])));
  $NeumobileWatch_tage = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_tage'])));
  $NeumobileWatch_spalten = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_spalten'])));
  $NeumobileWatch_legende = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_legende'])));
  $NeumobileWatch_notes = htmlentities(strip_tags(stripslashes($_POST['neumobilewatch_notes'])));




  for ($a=1;$a<=$AnzahlSensoren;$a++)
    {
    $NeuSensor[$a] = htmlentities(strip_tags(stripslashes($_POST["sensor".$a])));
    $NeuSensor[$a] = str_replace("'", "",$NeuSensor[$a]);
    $NeuUseIcon[$a] = htmlentities(strip_tags(stripslashes($_POST["useicon".$a])));
    $NeuIcon[$a] = htmlentities(strip_tags(stripslashes($_POST["icon".$a])));
    $NeuFarbe[$a] = htmlentities(strip_tags(stripslashes($_POST["farbe".$a])));
    $NeuAnzeige[$a] = htmlentities(strip_tags(stripslashes($_POST["anzeige".$a])));
    $NeuAchse[$a] = htmlentities(strip_tags(stripslashes($_POST["achse".$a])));
    $NeuAchse[$a] = "'".$NeuAchse[$a]."'";
   // if (strlen($NeuAchse[$a]) > 10)   $NeuAchse[$a] = "'".$NeuAchse[$a]."',showInRangeSelector: true";
    $NeuEinheit[$a] = htmlentities(strip_tags(stripslashes($_POST["einheit".$a])));
    
    $SensorLoeschen[$a] = htmlentities(strip_tags(stripslashes($_POST["loeschen".$a])));
    $NeuTageswertOption[$a] = htmlentities(strip_tags(stripslashes($_POST["tageswertoption".$a])));
    }

  //falls neuer Sensor eingegeben wurde
  $NeuSensorNeu = htmlentities(strip_tags(stripslashes($_POST["sensorneu"])));
  if ($NeuSensorNeu != "") $AnzahlSensoren = $AnzahlSensoren +1 ; //es gibt einen neuen Sensor
  $NeuSensor[$a] = htmlentities(strip_tags(stripslashes($_POST["sensorneu"])));
  $NeuSensor[$a] = str_replace("'", "",$NeuSensor[$a]);
  $NeuUseIcon[$a] = htmlentities(strip_tags(stripslashes($_POST["useiconneu"])));
  $NeuIcon[$a] = htmlentities(strip_tags(stripslashes($_POST["iconneu"])));
  $NeuFarbe[$a] = htmlentities(strip_tags(stripslashes($_POST["farbeneu"])));
  $NeuAnzeige[$a] = htmlentities(strip_tags(stripslashes($_POST["anzeigeneu"])));
  $NeuAchse[$a] = htmlentities(strip_tags(stripslashes($_POST["achseneu"])));
  $NeuAchse[$a] = "'".$NeuAchse[$a]."'";
  if (strlen($NeuAchse[$a]) > 10) $NeuAchse[$a] = "'".$NeuAchse[$a]."',showInRangeSelector: true";
  $NeuEinheit[$a] = htmlentities(strip_tags(stripslashes($_POST["einheitneu"])));
  $NeuTageswertOption[$a] = htmlentities(strip_tags(stripslashes($_POST["tageswertoptionneu"])));
  }

$DirektCSV = htmlentities(strip_tags(stripslashes($_GET["direktcsv"])));
$Csvdatei = htmlentities(strip_tags(stripslashes($_POST['csvdatei'])));
if (isset($DirektCSV)) $Csvdatei = $DirektCSV;
//POST-Variablen fürs Passwort
$Passwort = htmlentities(strip_tags(stripslashes($_POST['passwort'])));

if ($Passwort == "") $Passwort = $_SESSION["Spasswort"];

    $_SESSION["Spasswort"] = $Passwort;
  

$Tracht = htmlentities(strip_tags(stripslashes($_GET['tracht'])));
$NeuKoeAnzeige = $_POST['neukoeanzeige'];
$NeuKoeInfo = htmlentities(strip_tags(stripslashes($_POST['neukoeinfo'])));

if (!$Tracht == "zeigen"){

$RealFarben = array("black","gray","silver","maroon","red","orange","green","lime","olive","orangered","yellow","gold","navy","blue","deepskyblue","purple","fuchsia","teal","aqua");
$IconBezeichner = array(
array("1","n_1 Leerrahmen hinzu.png","1 empty frame added","1 cadre vide"),
array("2","n_2 Leerrahmen hinzu.png","2 empty frame added","2 cadre vide"),
array("3","n_Akkutausch.png","Battery exchange","changement de batterie"),
array("4","n_Ameisensaeurebehandlung.png","formic acid treatment","traitement acide formique"),
array("5","n_Bienenflucht eingesetzt.png","Bee escape inserted","essaimage"),
array("6","n_Brutwabe entnommen.png","Brood-comb removed","retrait cadre couvain"),
array("7","n_Durchsicht - Achtung.png","Review - Attention","a revoir attention"),
array("8","n_Durchsicht - Alles OK.png","Review - All OK","revu tout est OK"),
array("9","n_Durchsicht.png","Inspection","a verifier"),
array("10","n_Fuetterung.png","Feeding","nourrisement"),
array("11","n_Futterwabe entnommen.png","Honeycomb removed","retrait couvain"),
array("12","n_Honigernte.png","Honey harvesting","récolte miel"),


array("13","n_Honigraum entfernt.png","Honey room removed","retrait hausse"),
array("14","n_Honigraum hinzu.png","Honey room added","pose hausse"),
array("15","n_Koenigin blau gezeichnet.png","Queen drawn blue","marquage reine bleue"),
array("16","n_Koenigin gelb gezeichnet.png","Queen drawn yellow","marquage reine jaune"),
array("17","n_Koenigin gruen gezeichnet.png","Queen drawn green","marquage reine vert"),
array("18","n_Koenigin rot gezeichnet.png","Queen drawn red","marquage reine rouge"),
array("19","n_Koenigin weiss gezeichnet.png","Queen drawn white","marquage reine blanc"),
array("20","n_Oxalsaeurebehandlung.png","Oxalic acid treatment","traitement acide oxalique"),
array("21","n_Schwarmabgang.png","Swarm departure","essaimage"),
array("22","n_Totale Brutentnahme.png","Total brood removal","élimination totale du couvain"),
array("23","n_Varoaeinschub.png","Varoa slot","insertion varoa"),
array("24","n_Wetter (Schnee-Hagel-Wind).png","Weather (snow-hail-wind)","tempête (neige,grèle,vent)"),
array("25","n_Absperrgitter.png","Barrier grid","Grille de barrières"),
);

$Message = "";
  

// Config sichern
if (!file_exists('beelogger_ini.php')) 
  { // noch keine beelogger_ini.php vorhanden
  $ConfigSichern = "1";  // damit Speicherung stattfindet

  // DefaultDaten für beelogger_ini.php Speicherung einsetzen
  $AnzahlSensoren = 13; //default für beelogger und Unterordnerbeelogger


    switch ($MultiType) 
      {
      case ("2"): //beelogger Multi
      $AnzahlSensoren = 18;
      break;

      case ("3"): //beelogger Multi
      $AnzahlSensoren = 22;
      break;

      case ("4"): //beelogger Multi
      $AnzahlSensoren = 26;
      break;

      case ("5"): //beelogger Multi
      $AnzahlSensoren = 30;
      break;

      case ("6"): //beelogger Multi
      $AnzahlSensoren = 34;
      break;

      case ("7"): //beelogger Multi
      $AnzahlSensoren = 38;
      break;
      }
    

    $NeuBeeloggerShowPasswort = "Show";
  if (file_exists("../beelogger1/beelogger_ini.php"))
    { // nicht der erste beelogger
    if (file_exists("pw.php")) 
      {
      include("pw.php");
      $NeuBeeloggerShowPasswort = $pw;
      unlink("pw.php");
      }
    }

  $NeuBeeloggerLogPasswort = "Log";
  $NeuBienenvolkbezeichnung = "auto";
  $NeuStandardCSVDatei = "week.csv";
  $NeuPunktAnzeige = "false";
  $NeuTageswertAnzeige = "false";
  $NeuLegende      = "immer";
  $NeuRollPeriod = "1";

  $NeuBasisGewichtBeute = "";
  $NeuBasisGewichtBeute1 = "";
  $NeuBasisGewichtBeute2 = "";
  $NeuBasisGewichtBeute3 = "";
  $NeuBasisGewichtBeute4 = "";
  $NeuBasisGewichtBeute5 = "";
  $NeuBasisGewichtBeute6 = "";
  $NeuBasisGewichtBeute7 = "";

  $NeuAutoAnmerkungenErzeugen = "deaktiviert";
  $NeuAnmerkungGewichtsDifferenz = "1";
  $NeuAnmerkungZeitDifferenz = "15";
  $NeuAutoServiceAnmerkung = false;
  $NeuAutoServiceAnmerkungZeit = 5;


  $NeuIntervallSendeSteuerung = "zeitgesteuert";
  $NeuSommerBeginn = "3";
  $NeuSommerTagZeit = "6";
  $NeuSommerSendeIntervallTag = "15";
  $NeuSommerNachtZeit = "22";
  $NeuSommerSendeIntervallNacht = "60";
  $NeuWinterBeginn = "deaktiviert";
  $NeuWinterSendeIntervall = "90";

  $NeuEESendeIntervall = "B";

  $NeuKorrekturwert = "0.00";
  $NeuKorrekturwert1 = "0.00";
  $NeuKorrekturwert2 = "0.00";
  $NeuKorrekturwert3 = "0.00";
  $NeuKorrekturwert4 = "0.00";
  $NeuKorrekturwert5 = "0.00";
  $NeuKorrekturwert6 = "0.00";
  $NeuKorrekturwert7 = "0.00";


  $NeuKalibrierTemperatur = "";

  $NeuWatchdog = "deaktiviert";
  $NeuGeneralAutoWatch = "deaktiviert";
  $NeuGeneralAutoWatchTime = "Alle";

  $NeuBeutenLeergewicht = "0.00";
  $NeuHonigraum1Anzahl = "0";
  $NeuHonigraum1Leergewicht = "0.00";
  $NeuHonigraum2Anzahl = "0";
  $NeuHonigraum2Leergewicht = "0.00";

  $NeuAkkuLeerSchwelle = "3.8";
  $NeuAkkuVollSchwelle = "4.0";

  $NeuEmailSchwarmAlarm = "deaktiviert";
  $NeuEmailSchwarmAlarm1 = "deaktiviert";
  $NeuEmailSchwarmAlarm2 = "deaktiviert";
  $NeuEmailSchwarmAlarm3 = "deaktiviert";
  $NeuEmailSchwarmAlarm4 = "deaktiviert";
  $NeuEmailSchwarmAlarm5 = "deaktiviert";
  $NeuEmailSchwarmAlarm6 = "deaktiviert";
  $NeuEmailSchwarmAlarm7 = "deaktiviert";

  $NeuReferenzZeit = "30";
  $NeuReferenzZeit1 = "31";
  $NeuReferenzZeit2 = "32";
  $NeuReferenzZeit3 = "33";
  $NeuReferenzZeit4 = "34";
  $NeuReferenzZeit5 = "35";
  $NeuReferenzZeit6 = "36";
  $NeuReferenzZeit7 = "37";

  $NeuDifferenzGewicht = "2000";
  $NeuDifferenzGewicht1 = "2001";
  $NeuDifferenzGewicht2 = "2002";
  $NeuDifferenzGewicht3 = "2003";
  $NeuDifferenzGewicht4 = "2004";
  $NeuDifferenzGewicht5 = "2005";
  $NeuDifferenzGewicht6 = "2006";
  $NeuDifferenzGewicht7 = "2007";

  $NeuEmpfaenger_Email = "empfaenger@meineDomain.de";
  $NeuAbsender_Email = "absender@meineDomain.de";

  $NeuPushSchwarmAlarm = "deaktiviert";
  $NeuPushSchwarmAlarm1 = "deaktiviert";
  $NeuPushSchwarmAlarm2 = "deaktiviert";
  $NeuPushSchwarmAlarm3 = "deaktiviert";
  $NeuPushSchwarmAlarm4 = "deaktiviert";
  $NeuPushSchwarmAlarm5 = "deaktiviert";
  $NeuPushSchwarmAlarm6 = "deaktiviert";
  $NeuPushSchwarmAlarm7 = "deaktiviert";

  $NeuPushToken = "PushToken";
  $NeuPushUser = "PushUser";

  $NeuBeep = "deaktiviert";
  $NeuBeep1 = "deaktiviert";
  $NeuBeep2 = "deaktiviert";
  $NeuBeep3 = "deaktiviert";
  $NeuBeep4 = "deaktiviert";
  $NeuBeep5 = "deaktiviert";
  $NeuBeep6 = "deaktiviert";
  $NeuBeep7 = "deaktiviert";

  $NeuBeepId = "meine BeepID";
  $NeuBeepId1 = "meine BeepID";
  $NeuBeepId2 = "meine BeepID";
  $NeuBeepId3 = "meine BeepID";
  $NeuBeepId4 = "meine BeepID";
  $NeuBeepId5 = "meine BeepID";
  $NeuBeepId6 = "meine BeepID";
  $NeuBeepId7 = "meine BeepID";

  $NeuiBeekeeper = "deaktiviert";
  $NeuiBeekeeper1 = "deaktiviert";
  $NeuiBeekeeper2 = "deaktiviert";
  $NeuiBeekeeper3 = "deaktiviert";
  $NeuiBeekeeper4 = "deaktiviert";
  $NeuiBeekeeper5 = "deaktiviert";
  $NeuiBeekeeper6 = "deaktiviert";
  $NeuiBeekeeper7 = "deaktiviert";

  $NeuiBeekeeperUId = "meine UId";
  $NeuiBeekeeperUId1 = "meine UId";
  $NeuiBeekeeperUId2 = "meine UId";
  $NeuiBeekeeperUId3 = "meine UId";
  $NeuiBeekeeperUId4 = "meine UId";
  $NeuiBeekeeperUId5 = "meine UId";
  $NeuiBeekeeperUId6 = "meine UId";
  $NeuiBeekeeperUId7 = "meine UId";

  $NeubeeloggerMap2 = "";
  $NeubeeloggerMap2Location = "";
  $NeubeeloggerMap2Status = "";
  $NeubeeloggerMapWaage = 0;
  $NeubeeloggerMap2Lat = "00.00";
  $NeubeeloggerMap2Lon = "00.00";


  $NeuInfo = $SAs[0]; //Informationen und Notizen  
  $NeuKoeAnzeige = "deaktiviert";
  
  $NeumobileWatch_Show = "aktiviert";
  $NeumobileWatch_Sort = "Ordner";

  $NeuSprache = "1"; //default Deutsch

  //default
  $NeuSensor = array("",$SAs[1],$SAs[2],$SAs[3],$SAs[4],$SAs[5],$SAs[6],$SAs[7],$SAs[8],"Service","10","11","12","13");
  $NeuFarbe = array("","blue","deepskyblue","red","orangered","gold","black","teal","purple","fuchsia","silver","gray","gray","gray");
  $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false");
  $NeuAchse = array ("","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
  $NeuEinheit = array ("","[°C]","[°C]","[%]","[%]","[lux]","[kg]","[V]","[V]","[s/dBm]","[kg]","[hPa]","[l/m^2]","[]");
  $NeuIcon = array('','temp_i.png','temp_o.png','hum_i.png','hum_o.png','sun.png','weight.png','bat.png','solar.png','service.png','weight.png','press.png','rain.png','no.png');
  $NeuUseIcon = array('false','false','false','false','false','false','true','true','true','true');


  if ($MultiType == 2)
    {
    $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-D_1",$SAs[6]."-D_2",$SAs[1]."-D_1",$SAs[1]."-D_2",$SAs[3]."-D_1",$SAs[3]."-D_2",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","orangered","orangered","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[°C]","[°C]","[%]","[%]","[V]","[V]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
    $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','press.png','rain.png','no.png');
    $NeuUseIcon = array('false','false','false','false','true','true','true','true');
    }

  if ($MultiType == 3)
    {
      $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-T_1",$SAs[6]."-T_2",$SAs[6]."-T_3",$SAs[1]."-T_1",$SAs[1]."-T_2",$SAs[1]."-T_3",$SAs[3]."-T_1",$SAs[3]."-T_2",$SAs[3]."-T_3",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.",$SAs[6]."(2A) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","black","orangered","orangered","orangered","deepskyblue","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[kg]","[°C]","[°C]","[°C]","[%]","[%]","[%]","[V]","[V]","[kg]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
    $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','weight.png','temp_i.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','weight.png','press.png','rain.png','no.png');
    $NeuUseIcon = array('false','false','false','false','true','true','true','true','true');
    }

  if ($MultiType == 4)
    {
      $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-Q_1",$SAs[6]."-Q_2",$SAs[6]."-Q_3",$SAs[6]."-Q_4",$SAs[1]."-Q_1",$SAs[1]."-Q_2",$SAs[1]."-Q_3",$SAs[1]."-Q_4",$SAs[3]."-Q_1",$SAs[3]."-Q_2",$SAs[3]."-Q_3",$SAs[3]."-Q_4",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.",$SAs[6]."(2A) unkomp.",$SAs[6]."(2B) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","black","black","orangered","orangered","orangered","orangered","deepskyblue","deepskyblue","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[kg]","[kg]","[°C]","[°C]","[°C]","[°C]","[%]","[%]","[%]","[%]","[V]","[V]","[kg]","[kg]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
   $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','weight.png','weight.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','weight.png','weight.png','press.png','rain.png','no.png');
   $NeuUseIcon = array('false','false','false','false','true','true','true','true','true','true');
    }

      if ($MultiType == 5)
    {
    $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-P_1",$SAs[6]."-P_2",$SAs[6]."-P_3",$SAs[6]."-P_4",$SAs[6]."-P_5",$SAs[1]."-P_1",$SAs[1]."-P_2",$SAs[1]."-P_3",$SAs[1]."-P_4",$SAs[1]."-P_5",$SAs[3]."-P_1",$SAs[3]."-P_2",$SAs[3]."-P_3",$SAs[3]."-P_4",$SAs[3]."-P_5",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.",$SAs[6]."(2A) unkomp.",$SAs[6]."(2B) unkomp.",$SAs[6]."(3A) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","black","black","black","orangered","orangered","orangered","orangered","orangered","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y2'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[kg]","[kg]","[kg]","[°C]","[°C]","[°C]","[°C]","[°C]","[%]","[%]","[%]","[%]","[%]","[V]","[V]","[kg]","[kg]","[kg]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
    $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','weight.png','weight.png','weight.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','weight.png','weight.png','weight.png','press.png','rain.png','no.png');
    $NeuUseIcon = array('false','false','false','false','true','true','true','true','true','true','true');
    }

  if ($MultiType == 6)
    {
    $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-S_1",$SAs[6]."-S_2",$SAs[6]."-S_3",$SAs[6]."-S_4",$SAs[6]."-S_5",$SAs[6]."-S_6",$SAs[1]."-S_1",$SAs[1]."-S_2",$SAs[1]."-S_3",$SAs[1]."-S_4",$SAs[1]."-S_5",$SAs[1]."-S_6",$SAs[3]."-S_1",$SAs[3]."-S_2",$SAs[3]."-S_3",$SAs[3]."-S_4",$SAs[3]."-S_5",$SAs[3]."-S_6",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.",$SAs[6]."(2A) unkomp.",$SAs[6]."(2B) unkomp.",$SAs[6]."(3A) unkomp.",$SAs[6]."(3B) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","black","black","black","black","orangered","orangered","orangered","orangered","orangered","orangered","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[°C]","[°C]","[°C]","[°C]","[°C]","[°C]","[%]","[%]","[%]","[%]","[%]","[%]","[V]","[V]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
    $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','press.png','rain.png','no.png');
    $NeuUseIcon = array('false','false','false','false','true','true','true','true','true','true','true','true','true');
    }

  if ($MultiType == 7)
    {
    $NeuSensor = array("",$SAs[9],$SAs[2],$SAs[4],"Service",$SAs[5],$SAs[6]."-S_1",$SAs[6]."-S_2",$SAs[6]."-S_3",$SAs[6]."-S_4",$SAs[6]."-S_5",$SAs[6]."-S_6",$SAs[6]."-S_7",$SAs[1]."-S_1",$SAs[1]."-S_2",$SAs[1]."-S_3",$SAs[1]."-S_4",$SAs[1]."-S_5",$SAs[1]."-S_6",$SAs[1]."-S_7",$SAs[3]."-S_1",$SAs[3]."-S_2",$SAs[3]."-S_3",$SAs[3]."-S_4",$SAs[3]."-S_5",$SAs[3]."-S_6",$SAs[3]."-S_7",$SAs[7],$SAs[8],$SAs[6]."(1A) unkomp.",$SAs[6]."(1B) unkomp.",$SAs[6]."(2A) unkomp.",$SAs[6]."(2B) unkomp.",$SAs[6]."(3A) unkomp.",$SAs[6]."(3B) unkomp.",$SAs[6]."(4A) unkomp.","Aux1","Aux2","Aux3");
    $NeuFarbe = array("","orange","red","blue","teal","yellow","black","black","black","black","black","black","black","orangered","orangered","orangered","orangered","orangered","orangered","orangered","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","deepskyblue","fuchsia","purple","gray","gray","gray","gray","gray","gray","gray","gray","gray","gray");
    $NeuAnzeige = array("","true","true","true","true","true","true","true","true","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false","false");
    $NeuAchse = array ("","'y'","'y'","'y'","'y2'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'","'y2'");
    $NeuEinheit = array ("","[°C]","[°C]","[%]","[s/dBm]","[lux]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[°C]","[°C]","[°C]","[°C]","[°C]","[°C]","[°C]","[%]","[%]","[%]","[%]","[%]","[%]","[%]","[V]","[V]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[kg]","[hPa]","[l/m^2]","[]");
    $NeuIcon = array('','temp_w.png','temp_o.png','hum_o.png','service.png','sun.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','temp_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','hum_i.png','bat.png','solar.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','weight.png','press.png','rain.png','no.png');
    $NeuUseIcon = array('false','false','false','false','true','true','true','true','true','true','true','true','true');
    }

    if (file_exists("../general_ini.php")) 
      {
      include("../general_ini.php"); //falls bereits general_ini.php vorhanden 
      //- dann die Werte übernehmen....
      $NeuSprache = $Sprache; 
      $NeumobileWatch_tage = $mW_tage;
      $NeumobileWatch_spalten = $mW_spalten;
      $NeumobileWatch_Sort = $mW_sort;
      }
  }


if ($ConfigSichern == "1")
  {
  if (file_exists('beelogger_map.php')) include('beelogger_map.php');

function Curler($ServerCurl)
  {
  global $CurlArray;
  for ($c=6; $c >= 0; $c--) 
    {
    $CurlArray[$c+1] =  $CurlArray[$c]; //verschieben
    }
    $ServerCurl = str_replace('"','', $ServerCurl); //Anführungszeichen verboten
  $CurlArray[0] = $ServerCurl;
  }

  //Map2URL-link ermitteln
  if ($NeubeeloggerMap2URL == "aktiviert")
    {
    if ($beeloggerMapWaage == 0) $NeubeeloggerMap2URL = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    if ($beeloggerMapWaage >= 1) 
      {
      $actual_linkpos = strripos(strstr("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'/beelogger_show',TRUE),"/");
      $NeubeeloggerMap2URL= substr(strstr("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",'/beelogger_show',TRUE), 0,$actual_linkpos);
      $NeubeeloggerMap2URL .= '/beelogger'.$MultiSign.$ServerMultiNumber."_".$NeubeeloggerMapWaage.'/beelogger_show.php';
      }
    }
    else $NeubeeloggerMap2URL = "deaktiviert";

    //beeid ermitteln
    $NeubeeloggerMap2BeeID = sprintf("%06d",($MultiType.sprintf("%02d",$ServerMultiNumber)));

    $NeubeeloggerMap2ID = $beeloggerMap2ID; //aus general_ini falls da
    $NeubeeloggerMap2Key = $beeloggerMap2Key;  //aus general_ini falls da

    $NeubeeloggerMap2Sensoren = array ($NeubeeloggerMap2Sensor1,$NeubeeloggerMap2Sensor2,$NeubeeloggerMap2Sensor3,$NeubeeloggerMap2Sensor4,$NeubeeloggerMap2Sensor5,$NeubeeloggerMap2Sensor6);

  
  if ($NeubeeloggerMap1ToMap2 == "yes") //diese Aktion gibt es nur einmal
    { 
    //beeloggerMAP-Portierung erwünscht
    if (strlen($beeloggerMap2ID) != 8 OR strlen($beeloggerMap2Key) != 6)
      {
      //Noch keine mapid und mapkey in der general_ini vorhanden
      $ch = curl_init("https://map2.beelogger.de/log.php?mapid=7259464&do=994561"); // ueber cURL Initialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $CurlAntwort = curl_exec($ch); // 
      curl_close($ch); // Objekt schliessen und Ressourcen freigeben
      Curler("beelogger_show: {".htmlentities($CurlAntwort)."}_vom: ".date("m/d H:i:s")."_neue_MapID_und_Key_angefordert"); //Curlarray befüllen
      $NeubeeloggerMap2ID = substr($CurlAntwort,0,8);
      $NeubeeloggerMap2Key = substr($CurlAntwort,8);
      }


    //alte Daten abrufen
    $curler =  "http://map.beelogger.de/log.php?key1=".$beeloggerMapId1."&key2=".$beeloggerMapId2."&loc=".str_replace(' ','%20',$beeloggerMapLocation)."&move=7765425";
    $ch = curl_init($curler); // ueber cURL Initialisieren
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    $CurlAntwort = curl_exec($ch); // 
    curl_close($ch); // Objekt schliessen und Ressourcen freigeben
    Curler("beelogger_show: {".$curler."}_{".htmlentities($CurlAntwort)."}_vom :".date("Y/m/d H:i:s")."_Daten_von_MAP1_angefordert"); //Curlarray befüllen
    $ServerAntwort = trim($CurlAntwort, "\[]");
    $ServerAntwort = explode( ",",$ServerAntwort);
    $NeubeeloggerMap2Location = $ServerAntwort[0];
    $NeubeeloggerMap2Lon = $ServerAntwort[1];
    $NeubeeloggerMap2Lat = $ServerAntwort[2];
    if ($ServerAntwort[3] == 1) $NeubeeloggerMap2BeeloggerType = "S";
    if ($ServerAntwort[4] == 1) $NeubeeloggerMap2BeeloggerType = "E";
    $NeubeeloggerMap2Connect = "undefined"; //da keine Info von Map1
    $NeubeeloggerMap2Email = $ServerAntwort[7];
    $NeubeeloggerMap2 = "aktiviert"; //und direkt aktivieren
    }


  if ($NeubeeloggerMap2Teilnahme == "yes") //diese Aktion gibt es nur einmal pro beelogger
    { 
    //beeloggerMAP-Teilnahme erwünscht
    if (strlen($beeloggerMap2ID) != 8 OR strlen($beeloggerMap2Key) != 6) // general_ini.php bereits included
      { //Noch keine mapid und mapkey in der general_ini vorhanden 
      $ch = curl_init("https://map2.beelogger.de/log.php?mapid=7259464&do=994561"); // ueber cURL Initialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $CurlAntwort = curl_exec($ch); // 
      curl_close($ch); // Objekt schliessen und Ressourcen freigeben
      Curler("beelogger_show: {".htmlentities($CurlAntwort)."}_vom :".date("m/d H:i:s")."_neue_IDs_bezogen"); //Curlarray befüllen
      $NeubeeloggerMap2ID = substr($CurlAntwort,0,8);
      $NeubeeloggerMap2Key = substr($CurlAntwort,8);
      }
    }
    
    
//Standardabfrage falls auf MAP2 - checke, ob Map2 aktiviert werden darf?
    $helpme = $NeubeeloggerMap2Location;
    if ($NeubeeloggerMap2Lat != "" AND $NeubeeloggerMap2Lat != "00.00" AND $NeubeeloggerMap2Lon != "00.00" AND $NeubeeloggerMap2BeeloggerType != "" AND $NeubeeloggerMap2Connect != "" AND $NeubeeloggerMap2 == "aktiviert" AND $helpme != "") $NeubeeloggerMap2 = "aktiviert";
    else 
      {
      if  ($beeloggerMap != "aktiviert") $NeubeeloggerMap2 = "deaktiviert"; // nicht alle Daten OK und schon auf MAP2
      else $NeubeeloggerMap2 = ""; //noch MAP1 aktiv
      }


//Nutzer-Daten auf Ewig löschen - BeeID löschen
    if ($beeloggerMap2LoescheBeeID == "loeschen") 
      {  
      //BeeID und Daten auf MapServer löschen
      $curler =  "https://map2.beelogger.de/log.php?mapid=".$NeubeeloggerMap2ID."&key=".$NeubeeloggerMap2Key."&do=957248&beeid=".$NeubeeloggerMap2BeeID;
      $ch = curl_init($curler); // ueber cURL Initialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $CurlAntwort = curl_exec($ch); // [DISABLED]   - beeid deaktiviert
      curl_close($ch); // Objekt schliessen und Ressourcen freigeben
      Curler("beelogger_show: {".$curler."}_{".htmlentities($CurlAntwort)."}_vom :".date("m/d H:i:s")."_BeeID_gelöscht"); //Curlarray befüllen
         // Daten aus beelogger_map.php löschen  
      $NeubeeloggerMap2BeeID = "";
      $NeubeeloggerMap2Lat = "";
      $beeloggerMap2Status = "";
      $NeubeeloggerMap2Lon = "";
      $NeubeeloggerMap2Location = "";
      $NeubeeloggerMap2 = "";
      $NeubeeloggerMap2BeeloggerType = "";
      $NeubeeloggerMap2Connect = "";
      $NeubeeloggerMap2URL = "";
      $NeubeeloggerMap2Email = "";
      $NeubeeloggerMap2Sensor1 = "";
      $NeubeeloggerMap2Sensor2 = "";
      $NeubeeloggerMap2Sensor3 = "";
      $NeubeeloggerMap2Sensor4 = "";
      $NeubeeloggerMap2Sensor5 = "";
      $NeubeeloggerMap2Sensor6 = "";
      $NeubeeloggerMap2Status = $CurlAntwort;
      }


//alle Daten vorhanden und beelogger aktiviert!beeID anmelden und Daten übertragen
    if ($NeubeeloggerMap2 == "aktiviert") 
      { 
      //beeID Update-Daten übertragen
      $curlerString =  "https://map2.beelogger.de/log.php?mapid=".$NeubeeloggerMap2ID."&key=".$NeubeeloggerMap2Key."&do=348527&beeid=".$NeubeeloggerMap2BeeID."&name=".html_entity_decode(str_replace(' ', '%20',$NeubeeloggerMap2Location))."&lat=".$NeubeeloggerMap2Lat."&lon=".$NeubeeloggerMap2Lon."&typ=".$NeubeeloggerMap2BeeloggerType."&connect=".$NeubeeloggerMap2Connect."&url=".str_replace(' ', '%20',$NeubeeloggerMap2URL)."&email=".str_replace(' ','%20',$NeubeeloggerMap2Email); 
      $ch = curl_init($curlerString); // ueber cURL Initialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $CurlAntwort = curl_exec($ch); // [NEW] - beeid mit neuen Daten angelegt ODER [CHANGED] - Daten der beeid geändert
      $NeubeeloggerMap2Status = $CurlAntwort;  
      curl_close($ch); // Objekt schliessen und Ressourcen freigeben
      Curler("beelogger_show: {".$curlerString."}_{".htmlentities($CurlAntwort)."}_vom :".date("m/d H:i:s")."_BeeID_mit_aktuellen_Daten_angelegt"); //Curlarray befüllen
      if ($CurlAntwort == "[INCOMPLETE]") $NeubeeloggerMap2 = "deaktiviert"; //Daten waren unvollständig
      else //alles ok mit der Anmeldung
        {
        if ($beeloggerMap2 == "deaktiviert" OR $beeloggerMap2 == "")
          {
          //BeeID- aktivieren falls (wieder) aktiviert wurde
          $curler = "https://map2.beelogger.de/log.php?mapid=".$NeubeeloggerMap2ID."&key=".$NeubeeloggerMap2Key."&do=140849&beeid=".$NeubeeloggerMap2BeeID;
          $ch = curl_init($curler);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
          $CurlAntwort = curl_exec($ch); // [ENABLED]    - beeid aktiviert
          curl_close($ch);
          Curler("beelogger_show: {".$curler."}_{".htmlentities($CurlAntwort)."}_vom :".date("m/d H:i:s")."_BeeID_aktiviert"); //Curlarray befüllen
          $NeubeeloggerMap2Status = $CurlAntwort; 
          

          //einmalige Übertragung der Map2-Daten ----------------------------
          //Trachtsituation der letzten 7 Tage berechnen
          $Wochendatei = "week.csv";
          if ($NeubeeloggerMapWaage != "")
            {
            if (file_exists('../beelogger'.$MultiSign.$ServerMultiNumber."_".$NeubeeloggerMapWaage.'/week.csv')) $Wochendatei = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$NeubeeloggerMapWaage.'/week.csv';
            } 
          if (file_exists($Wochendatei)) 
            { //eine week.csv gefunden
            $array = file($Wochendatei);
            $i = sizeof($array);
            $what = trim($array[$i-1]);    //help curler
            $LetzteZeile = explode( ",", $what); // help curler
            while ($i--) 
              { 
              $what = trim($array[$i]);    
              $x = explode( ",", $what );

              $s = sizeof($x);
              if ($x[$s-1] !='') 
                  { 
                  $LastTrachtGewicht = $x[6]; //letzter Wert
                  break;
                  }
              } //while

            $what = trim($array[0]);  //erster Wert  
            $x = explode( ",", $what );
            $FirstTrachtGewicht = $x[6];
            if ($LastTrachtGewicht > 150 ) $Trachtdurchschnitt = round((($LastTrachtGewicht - $FirstTrachtGewicht)/7000),2);
            else $Trachtdurchschnitt = round(($LastTrachtGewicht - $FirstTrachtGewicht)/7,2);
            } 
          $curler = "https://map2.beelogger.de/log.php?mapid=".$NeubeeloggerMap2ID."&beeid=".$NeubeeloggerMap2BeeID."&do=164052&tracht=".$Trachtdurchschnitt;
          for ($c=0; $c < 7; $c++) 
            { 
            if ($NeubeeloggerMap2Sensoren[$c] != "") $curler .= "&s".($c+1)."=".$LetzteZeile[($NeubeeloggerMap2Sensoren[$c]+1)];
            }

          $ch = curl_init($curler);
          curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
          $ServerAntwort = curl_exec($ch); // Ausführen
          curl_close($ch); // Objekt schließen und Ressourcxen freigeben
          Curler("beelogger_show: {".$curler."}_{".$ServerAntwort."}_vom :".date("m/d H:i:s")."_Daten_ausgesendet"); //Curlarray befüllen
          $beeloggerMap2Status = $ServerAntwort;

          // diesen beelogger in der alten MAP1 deaktivieren
          $NeubeeloggerMapLocation = str_replace("'", "",$NeubeeloggerMapLocation);
          $NeubeeloggerMapLocation = str_replace('"', "",$NeubeeloggerMapLocation);
          //beeloggerMapInitialisierung
          $SendNeubeeloggerMapLocation = str_replace(" ","%20",$NeubeeloggerMapLocation); // Leerzeichen mit "%20" ersetzen
          
          if (strlen($NeubeeloggerMapId1) == 8 AND strlen($NeubeeloggerMapId2) == 6)
            {
            $curler = "https://map.beelogger.de/log.php?key1=".$NeubeeloggerMapId1."&key2=".$NeubeeloggerMapId2."&loc=".html_entity_decode($SendNeubeeloggerMapLocation)."&aktiv=0"; 
            $ch = curl_init($curler); // cURL Ã­nitialisieren
            curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $Antwort = curl_exec($ch); // Ausführen
            curl_close($ch); // Objekt schlieÃen und Ressourcen freigeben 
            Curler("beelogger_show: {".$curler."}_{".$Antwort."}_vom :".date("m/d H:i:s")."_Map1_deaktiviert"); //Curlarray befüllen
            }
          } 
        }            
      }

 //beelogger noch aktiviert - wieder deaktivieren
    if ($NeubeeloggerMap2 == "deaktiviert" AND $beeloggerMap2 == "aktiviert") 
      {  //BeeID deaktivieren
      $curler = "https://map2.beelogger.de/log.php?mapid=".$NeubeeloggerMap2ID."&key=".$NeubeeloggerMap2Key."&do=528145&beeid=".$NeubeeloggerMap2BeeID;
      $ch = curl_init($curler); // ueber cURL Initialisieren
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      $CurlAntwort = curl_exec($ch); // [DELETED]   - beeid deaktiviert
      curl_close($ch); // Objekt schliessen und Ressourcen freigeben
      Curler("beelogger_show: {".$curler."}_{".$CurlAntwort."}_vom :".date("m/d H:i:s")."_Map2_deaktiviert"); //Curlarray befüllen
      $NeubeeloggerMap2Status = $CurlAntwort; 
      }

//ende beeloggerMapInitialsierung


  $i = 0;
  $IniArray [$i] = "<?php\n";
  $i++;
  $IniArray [$i] = "//     ----------  Benutzereinstellungen: ---------\n\n";
  $i++;
  $IniArray [$i] = '$BeeloggerShowPasswort = "'.$NeuBeeloggerShowPasswort.'";'."\n";
  $i++;

  if (strpos($beelogger,"beeloggerT") === false AND strpos($beelogger,"beeloggerQ") === false)
    {
    $IniArray [$i] = '$BeeloggerLogPasswort = "'.$NeuBeeloggerLogPasswort.'";'."\n\n";$i++;
    }
  if (htmlentities($NeuBienenvolkbezeichnung) == "") $NeuBienenvolkbezeichnung = "auto";
  $IniArray [$i] = '$Bienenvolkbezeichnung = '."'".htmlentities($NeuBienenvolkbezeichnung)."';\n";$i++;
  $IniArray [$i] = '$StandardCSV = '."'".$NeuStandardCSVDatei."';\n";$i++;
  $IniArray [$i] = '$KoeAnzeige = '."'".$NeuKoeAnzeige."';\n";$i++;
  $IniArray [$i] = '$KoeInfo = '."'".$NeuKoeInfo."';\n";$i++;
  if ($NeuPunktAnzeige != "true") $NeuPunktAnzeige = "false";
  $IniArray [$i] = '$PunktAnzeige = '."'".$NeuPunktAnzeige."';\n";$i++;
  $IniArray [$i] = '$TageswertAnzeige = '."'".$NeuTageswertAnzeige."';\n";$i++;
  $IniArray [$i] = '$Legende = '."'".$NeuLegende."';\n";
  $i++;
  $IniArray [$i] = '$RollPeriod = '.'"'.$NeuRollPeriod.'";'."\n\n";
  $i++;

  if ($MultiType < 2) //für alle Einzelansichten
    { 
    $IniArray [$i] = '$AutoAnmerkungenErzeugen = "'.$NeuAutoAnmerkungenErzeugen.'";'."\n";$i++;
    $IniArray [$i] = '$AnmerkungGewichtsDifferenz = "'.$NeuAnmerkungGewichtsDifferenz.'";'."\n";$i++;
    $IniArray [$i] = '$AnmerkungZeitDifferenz = "'.$NeuAnmerkungZeitDifferenz.'";'."\n";$i++;
    }

    $IniArray [$i] = '$AutoServiceAnmerkung = "'.$NeuAutoServiceAnmerkung.'";'."\n";$i++;
    $IniArray [$i] = '$AutoServiceAnmerkungZeit = "'.$NeuAutoServiceAnmerkungZeit.'";'."\n\n";$i++; 

  if ($MultiType >= 1)
    {
    $IniArray [$i] = '$IntervallSendeSteuerung = "'.$NeuIntervallSendeSteuerung.'";'."\n";$i++;
    $IniArray [$i] = '$SommerBeginn = "'.$NeuSommerBeginn.'";'."\n";$i++;
    $IniArray [$i] = '$SommerTagZeit = "'.$NeuSommerTagZeit.'";'."\n";$i++;
    $IniArray [$i] = '$SommerSendeIntervallTag = "'.$NeuSommerSendeIntervallTag.'";'."\n";$i++;
    $IniArray [$i] = '$SommerNachtZeit = "'.$NeuSommerNachtZeit.'";'."\n";$i++;
    $IniArray [$i] = '$SommerSendeIntervallNacht = "'.$NeuSommerSendeIntervallNacht.'";'."\n";$i++;
    $IniArray [$i] = '$WinterBeginn = "'.$NeuWinterBeginn.'";'."\n";$i++;
    $IniArray [$i] = '$WinterSendeIntervall = "'.$NeuWinterSendeIntervall.'";'."\n\n";$i++;
    $IniArray [$i] = '$EESendeIntervall = "'.$NeuEESendeIntervall.'";'."\n\n";$i++;
    }


  if ($MultiType == 1)
    { 
    $IniArray [$i] = '$Korrekturwert = "'.$NeuKorrekturwert.'";'."\n";$i++;
    $IniArray [$i] = '$KalibrierTemperatur = "'.$NeuKalibrierTemperatur.'";'."\n\n";$i++;
    }

  if ($MultiType > 1)
    {

    $IniArray [$i] = '$KorrekturwertArray = array("'.$NeuKorrekturwert1.'","'.$NeuKorrekturwert2.'","'.$NeuKorrekturwert3.'","'.$NeuKorrekturwert4.'","'.$NeuKorrekturwert5.'","'.$NeuKorrekturwert6.'","'.$NeuKorrekturwert7.'");'."\n";$i++;
    $IniArray [$i] = '$KalibrierTemperatur = "'.$NeuKalibrierTemperatur.'";'."\n\n";$i++;
    }



  if ($MultiType < 2)  
    { 
    $IniArray [$i] = '$BeutenLeergewicht = "'.$NeuBeutenLeergewicht.'";'."\n";$i++;
    $IniArray [$i] = '$Honigraeume = array("'.$NeuHonigraum1Anzahl.'","'.(round ($NeuHonigraum1Leergewicht,2)).'","'.$NeuHonigraum2Anzahl.'","'.(round ($NeuHonigraum2Leergewicht,2)).'");'."\n";$i++;
    $IniArray [$i] = '$BeutenUtils = array(array("'.$NeuUtil1.'","'.(round ($NeuUtil1Leergewicht,2)).'"),array("'.$NeuUtil2.'","'.(round ($NeuUtil2Leergewicht,2)).'"),array("'.$NeuUtil3.'","'.(round ($NeuUtil3Leergewicht,2)).'"));'."\n";$i++;
    }


  $IniArray [$i] = '$Watchdog = "'.$NeuWatchdog.'";'."\n\n";$i++;


  if ($MultiType > 1)
    {
    if ($NeuEmailSchwarmAlarm1 != "aktiviert") $NeuEmailSchwarmAlarm1 = "deaktiviert";
    if ($NeuEmailSchwarmAlarm2 != "aktiviert") $NeuEmailSchwarmAlarm2 = "deaktiviert"; 
    if ($NeuEmailSchwarmAlarm3 != "aktiviert") $NeuEmailSchwarmAlarm3 = "deaktiviert"; 
    if ($NeuEmailSchwarmAlarm4 != "aktiviert") $NeuEmailSchwarmAlarm4 = "deaktiviert"; 
    if ($NeuEmailSchwarmAlarm5 != "aktiviert") $NeuEmailSchwarmAlarm5 = "deaktiviert"; 
    if ($NeuEmailSchwarmAlarm6 != "aktiviert") $NeuEmailSchwarmAlarm6 = "deaktiviert"; 
    if ($NeuEmailSchwarmAlarm7 != "aktiviert") $NeuEmailSchwarmAlarm7 = "deaktiviert";

    $IniArray [$i] = '$EmailSchwarmAlarmArray = array("'.$NeuEmailSchwarmAlarm1.'","'.$NeuEmailSchwarmAlarm2.'","'.$NeuEmailSchwarmAlarm3.'","'.$NeuEmailSchwarmAlarm4.'","'.$NeuEmailSchwarmAlarm5.'","'.$NeuEmailSchwarmAlarm6.'","'.$NeuEmailSchwarmAlarm7.'");'."\n";$i++;
    $IniArray [$i] = '$ReferenzZeitArray = array("'.$NeuReferenzZeit1.'","'.$NeuReferenzZeit2.'","'.$NeuReferenzZeit3.'","'.$NeuReferenzZeit4.'","'.$NeuReferenzZeit5.'","'.$NeuReferenzZeit6.'","'.$NeuReferenzZeit7.'");'."\n";$i++;
    $IniArray [$i] = '$DifferenzGewichtArray = array("'.$NeuDifferenzGewicht1.'","'.$NeuDifferenzGewicht2.'","'.$NeuDifferenzGewicht3.'","'.$NeuDifferenzGewicht4.'","'.$NeuDifferenzGewicht5.'","'.$NeuDifferenzGewicht6.'","'.$NeuDifferenzGewicht7.'");'."\n";$i++;
    if ($NeuPushSchwarmAlarm1 != "aktiviert") $NeuPushSchwarmAlarm1 = "deaktiviert";
    if ($NeuPushSchwarmAlarm2 != "aktiviert") $NeuPushSchwarmAlarm2 = "deaktiviert"; 
    if ($NeuPushSchwarmAlarm3 != "aktiviert") $NeuPushSchwarmAlarm3 = "deaktiviert"; 
    if ($NeuPushSchwarmAlarm4 != "aktiviert") $NeuPushSchwarmAlarm4 = "deaktiviert"; 
    if ($NeuPushSchwarmAlarm5 != "aktiviert") $NeuPushSchwarmAlarm5 = "deaktiviert"; 
    if ($NeuPushSchwarmAlarm6 != "aktiviert") $NeuPushSchwarmAlarm6 = "deaktiviert"; 
    if ($NeuPushSchwarmAlarm7 != "aktiviert") $NeuPushSchwarmAlarm7 = "deaktiviert";
    $IniArray [$i] = '$PushSchwarmAlarmArray = array("'.$NeuPushSchwarmAlarm1.'","'.$NeuPushSchwarmAlarm2.'","'.$NeuPushSchwarmAlarm3.'","'.$NeuPushSchwarmAlarm4.'","'.$NeuPushSchwarmAlarm5.'","'.$NeuPushSchwarmAlarm6.'","'.$NeuPushSchwarmAlarm7.'");'."\n";$i++;
    }

  if ($MultiType == 1)
    {
    if ($NeuEmailSchwarmAlarm != "aktiviert") $NeuEmailSchwarmAlarm = "deaktiviert";
    if ($NeuPushSchwarmAlarm != "aktiviert") $NeuPushSchwarmAlarm = "deaktiviert";  
    $IniArray [$i] = '$EmailSchwarmAlarm = "'.$NeuEmailSchwarmAlarm.'";'."\n";$i++;
    $IniArray [$i] = '$ReferenzZeit = "'.$NeuReferenzZeit.'";'."\n";$i++;
    $IniArray [$i] = '$DifferenzGewicht = "'.$NeuDifferenzGewicht.'";'."\n";$i++;
    $IniArray [$i] = '$PushSchwarmAlarm = "'.$NeuPushSchwarmAlarm.'";'."\n";$i++;
    }


  if ($MultiType >= 1)
    {
    $IniArray [$i] = '$TriggerAlarmArray=array(
    array("Triggernummer","per Mail?","per Pushover?","Triggersensor","<,= oder >","Triggerwert","Sendepause","Autoanmerkung?"),
    array("Triggeralarm1","'.$NeuTriggerAlarmEmail1.'","'.$NeuTriggerAlarmPush1.'","'.$NeuTriggerAlarmSensor1.'","'.$NeuTriggerAlarmZeichen1.'","'.$NeuTriggerAlarmWert1.'","'.$NeuTriggerAlarmPause1.'","'.$NeuTriggerAlarmAnmerkung1.'"),
    array("Triggeralarm2","'.$NeuTriggerAlarmEmail2.'","'.$NeuTriggerAlarmPush2.'","'.$NeuTriggerAlarmSensor2.'","'.$NeuTriggerAlarmZeichen2.'","'.$NeuTriggerAlarmWert2.'","'.$NeuTriggerAlarmPause2.'","'.$NeuTriggerAlarmAnmerkung2.'"),
    array("Triggeralarm3","'.$NeuTriggerAlarmEmail3.'","'.$NeuTriggerAlarmPush3.'","'.$NeuTriggerAlarmSensor3.'","'.$NeuTriggerAlarmZeichen3.'","'.$NeuTriggerAlarmWert3.'","'.$NeuTriggerAlarmPause3.'","'.$NeuTriggerAlarmAnmerkung3.'"),
    array("Triggeralarm4","'.$NeuTriggerAlarmEmail4.'","'.$NeuTriggerAlarmPush4.'","'.$NeuTriggerAlarmSensor4.'","'.$NeuTriggerAlarmZeichen4.'","'.$NeuTriggerAlarmWert4.'","'.$NeuTriggerAlarmPause4.'","'.$NeuTriggerAlarmAnmerkung4.'"),
                                              );'."\n";$i++;
    $IniArray [$i] = '$PushToken = "'.htmlentities($NeuPushToken).'";'."\n";$i++;
    $IniArray [$i] = '$PushUser = "'.htmlentities($NeuPushUser).'";'."\n\n";$i++; 
    $IniArray [$i] = '$Empfaenger_Email = "'.htmlentities($NeuEmpfaenger_Email).'";'."\n";$i++;
    $IniArray [$i] = '$Absender_Email = "'.htmlentities($NeuAbsender_Email).'";'."\n\n";$i++;
    $IniArray [$i] = '$NextService = "'.$NeuNextService.'";'."\n\n";$i++;
    }


  if ($MultiType > 1)
      {
      if ($NeuBeep1 != "aktiviert") $NeuBeep1 = "deaktiviert";
      if ($NeuBeep2 != "aktiviert") $NeuBeep2 = "deaktiviert"; 
      if ($NeuBeep3 != "aktiviert") $NeuBeep3 = "deaktiviert"; 
      if ($NeuBeep4 != "aktiviert") $NeuBeep4 = "deaktiviert";  
      if ($NeuBeep5 != "aktiviert") $NeuBeep5 = "deaktiviert"; 
      if ($NeuBeep6 != "aktiviert") $NeuBeep6 = "deaktiviert"; 
      if ($NeuBeep7 != "aktiviert") $NeuBeep7 = "deaktiviert"; 

      $IniArray [$i] = '$BeepArray = array("'.$NeuBeep1.'","'.$NeuBeep2.'","'.$NeuBeep3.'","'.$NeuBeep4.'","'.$NeuBeep5.'","'.$NeuBeep6.'","'.$NeuBeep7.'");'."\n";$i++;
      $IniArray [$i] = '$BeepIdArray = array("'.$NeuBeepId1.'","'.$NeuBeepId2.'","'.$NeuBeepId3.'","'.$NeuBeepId4.'","'.$NeuBeepId5.'","'.$NeuBeepId6.'","'.$NeuBeepId7.'");'."\n\n";$i++;
      }
  elseif ($MultiType == 1)
      { 
      if ($NeuBeep != "aktiviert") $NeuBeep = "deaktiviert";   
      $IniArray [$i] = '$Beep = "'.htmlentities($NeuBeep).'";'."\n";$i++;
      $IniArray [$i] = '$BeepId = "'.htmlentities($NeuBeepId).'";'."\n\n";$i++;
      }

if ($MultiType > 1)
      {

      if ($NeuiBeekeeper1 != "aktiviert") $NeuiBeekeeper1 = "deaktiviert";
      if ($NeuiBeekeeper2 != "aktiviert") $NeuiBeekeeper2 = "deaktiviert"; 
      if ($NeuiBeekeeper3 != "aktiviert") $NeuiBeekeeper3 = "deaktiviert"; 
      if ($NeuiBeekeeper4 != "aktiviert") $NeuiBeekeeper4 = "deaktiviert"; 
      if ($NeuiBeekeeper5 != "aktiviert") $NeuiBeekeeper5 = "deaktiviert"; 
      if ($NeuiBeekeeper6 != "aktiviert") $NeuiBeekeeper6 = "deaktiviert"; 
      if ($NeuiBeekeeper7 != "aktiviert") $NeuiBeekeeper7 = "deaktiviert"; 

      $IniArray [$i] = '$iBeekeeperArray = array("'.$NeuiBeekeeper1.'","'.$NeuiBeekeeper2.'","'.$NeuiBeekeeper3.'","'.$NeuiBeekeeper4.'","'.$NeuiBeekeeper5.'","'.$NeuiBeekeeper6.'","'.$NeuiBeekeeper7.'");'."\n";$i++;
      $IniArray [$i] = '$iBeekeeperUIdArray = array("'.$NeuiBeekeeperUId1.'","'.$NeuiBeekeeperUId2.'","'.$NeuiBeekeeperUId3.'","'.$NeuiBeekeeperUId4.'","'.$NeuiBeekeeperUId5.'","'.$NeuiBeekeeperUId6.'","'.$NeuiBeekeeperUId7.'");'."\n\n";$i++;
      }
  elseif ($MultiType == 1)
      {
      if ($NeuiBeekeeper != "aktiviert") $NeuiBeekeeper = "deaktiviert";  
      $IniArray [$i] = '$iBeekeeper = "'.htmlentities($NeuiBeekeeper).'";'."\n";$i++;
      $IniArray [$i] = '$iBeekeeperUId = "'.htmlentities($NeuiBeekeeperUId).'";'."\n\n";$i++;
      }


  $IniArray [$i] = '$WetterIcons = "'.$NeuWetterIcons.'";'."\n";$i++;
//  $IniArray [$i] = '$ExWetterDaten   = "'.$NeuExWetterDaten  .'";'."\n\n";$i++;
     
  


  if ($MultiType > 0)
    {
    $IniArray [$i] = '$AkkuLeerSchwelle = "'.$NeuAkkuLeerSchwelle.'";'."\n";$i++;
    $IniArray [$i] = '$AkkuVollSchwelle = "'.$NeuAkkuVollSchwelle.'";'."\n";$i++; 

    $m = 0;
    $MapArray [$m] = "<?php\n";$m++; 
    //MAP1
    if ($beeloggerMap2 != "aktiviert" AND $beeloggerMap2 != "deaktiviert")
      {
      $MapArray [$m] = "//MAP1:\n".'$beeloggerMap = "'.$NeubeeloggerMap.'"; //Sollen Daten an beeloggerMap gesendet werden? '."\n";$m++;  
      $MapArray [$m] = '$beeloggerMapId1 = "'.htmlentities($NeubeeloggerMapId1).'"; //Bekommt man als angemeldeter User automatisch von beelogger.de '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapId2 = "'.htmlentities($NeubeeloggerMapId2).'"; //Bekommt man als angemeldeter User automatisch von beelogger.de '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapLocation = "'.htmlentities($NeubeeloggerMapLocation).'"; //Ort des beeloggers '."\n";$m++;
      $MapArray [$m] = '$beeloggerMapStatus = "'.htmlentities($NeubeeloggerMapStatus).'"; //Ist das Profil komplett? '."\n";$m++;
      }

    //MAP2
    $MapArray [$m] = "//MAP2:\n".'$beeloggerMap2 = "'.htmlentities($NeubeeloggerMap2).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Location = "'.htmlentities($NeubeeloggerMap2Location).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2BeeID = "'.htmlentities($NeubeeloggerMap2BeeID).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Status = "'.htmlentities($NeubeeloggerMap2Status).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Lat = "'.htmlentities($NeubeeloggerMap2Lat).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Lon = "'.htmlentities($NeubeeloggerMap2Lon).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2BeeloggerType = "'.htmlentities($NeubeeloggerMap2BeeloggerType).'";'."\n";$m++;
    $MapArray [$m] = '$beeloggerMap2Connect = "'.htmlentities($NeubeeloggerMap2Connect).'";'."\n";$m++;

    //if ($NeubeeloggerMap2URL != "aktiviert") $NeubeeloggerMap2URL = "deaktiviert";
    $MapArray [$m] = '$beeloggerMap2URL = "'.htmlentities($NeubeeloggerMap2URL).'";'."\n";$m++;
    if (substr(htmlentities($NeubeeloggerMap2URL), -19) != "/beelogger_show.php" AND htmlentities($NeubeeloggerMap2URL) != "") 
      {
      $MapArray [$m] = '$beeloggerMap2URLfail = "true";'."\n";$m++; 
      }
    $MapArray [$m] = '$beeloggerMap2Email = "'.htmlentities($NeubeeloggerMap2Email).'";'."\n";$m++;
    if ($MultiType > 1)
      { 
    $MapArray [$m] = '$beeloggerMapWaage = "'.htmlentities($NeubeeloggerMapWaage).'";'."\n\n";$m++;
      } //Variable für MAP1 und MAP2
    $MapArray [$m] = '$beeloggerMap2Sensoren = array("'.$NeubeeloggerMap2Sensor1.'","'.$NeubeeloggerMap2Sensor2.'","'.$NeubeeloggerMap2Sensor3.'","'.$NeubeeloggerMap2Sensor4.'","'.$NeubeeloggerMap2Sensor5.'","'.$NeubeeloggerMap2Sensor6.'");'."\n";$m++;

    $MapArray [$m] = '$CurlArray = array('."\n".'"'.$CurlArray[0].'"'.",\n".'"'.$CurlArray[1].'"'.",\n".'"'.$CurlArray[2].'"'.",\n".'"'.$CurlArray[3].'"'.",\n".'"'.$CurlArray[4].'"'.",\n".'"'.$CurlArray[5].'"'.",\n".'"'.$CurlArray[6].'"'.",\n".'"'.$CurlArray[7].'"'.",\n".'"'.$CurlArray[8].'");'."\n";$m++;

    $MapArray [$m] = '$beeloggerSketchID = "'.htmlentities($NeubeeloggerSketchID).'";'."\n";$m++;
    $MapArray [$m] = '$InfoSketchID = "'.$InfoSketchID.'";'."\n";$m++;   
      

    $fp = fOpen("beelogger_map.php", "w+");
    foreach($MapArray as $values){ fputs($fp, $values);}
    $OK = fputs($fp,"?>");
    fclose($fp);  

    $IniArray [$i] = '$Info = "'.htmlentities($NeuInfo).'";'."\n\n";$i++;
    }

 // General_ini.php
      if ($NeumobileWatch_tage == "") $NeumobileWatch_tage = 14; //INIT?
      if ($NeumobileWatch_spalten == "") $NeumobileWatch_spalten = 2; //INIT?
      if ($NeumobileWatch_Sort == "") $NeumobileWatch_Sort = "Ordner"; //INIT?

    $w = 0; 
    $general_ini_Array [$w] = '<?php'."\n";$w++;
    $general_ini_Array [$w] = '$mW_tage = "'.htmlentities($NeumobileWatch_tage).'";'."\n";$w++;
    $general_ini_Array [$w] = '$mW_spalten = "'.htmlentities($NeumobileWatch_spalten).'";'."\n";$w++;
    $general_ini_Array [$w] = '$mW_sort = "'.htmlentities($NeumobileWatch_Sort).'";'."\n\n";$w++;
    $general_ini_Array [$w] = '$beeloggerMap2ID = "'.htmlentities($NeubeeloggerMap2ID).'";'."\n";$w++;
    $general_ini_Array [$w] = '$beeloggerMap2Key = "'.htmlentities($NeubeeloggerMap2Key).'";'."\n\n";$w++;

    $general_ini_Array [$w] = '$GeneralAutowatch = "'.$NeuGeneralAutoWatch.'";'."\n";$w++;
    $general_ini_Array [$w] = '$GeneralAutowatchTime = "'.$NeuGeneralAutoWatchTime.'";'."\n\n";$w++;

    if (!$CommunityUser)
      {
      $general_ini_Array [$w] = '$OpenweathermapKey = "'.$NeuOpenweathermapKey.'";'."\n\n";$w++;
      }

    $general_ini_Array [$w] = '$Sprache = "'.$NeuSprache.'";'."\n\n";$w++;   

    $fp = fOpen("../general_ini.php", "w+");
    foreach($general_ini_Array as $values){ fputs($fp, $values);}
    $OK = fputs($fp,"?>");
    fclose($fp);  

// Triggeralarme.php
    if ($NeuTriggerAlarmEmail1 == "aktiviert" OR $NeuTriggerAlarmPush1 == "aktiviert" AND $TriggerGesendetArray[1] == "deaktiviert") $TriggerGesendetArray[1] = "";
    if ($NeuTriggerAlarmEmail2 == "aktiviert" OR $NeuTriggerAlarmPush2 == "aktiviert" AND $TriggerGesendetArray[2] == "deaktiviert")$TriggerGesendetArray[2] = "";
    if ($NeuTriggerAlarmEmail3 == "aktiviert" OR $NeuTriggerAlarmPush3 == "aktiviert" AND $TriggerGesendetArray[3] == "deaktiviert")$TriggerGesendetArray[3] = "";
    if ($NeuTriggerAlarmEmail4 == "aktiviert" OR $NeuTriggerAlarmPush4 == "aktiviert" AND $TriggerGesendetArray[4] == "deaktiviert")$TriggerGesendetArray[4] = "";
    $TriggerDatei [0] = "<?php // Hilfs-Datei zur Steurerung der Triggeralarm\n";
    $TriggerDatei [1] = '$TriggerGesendetArray = array("WannGesendet?","'.$TriggerGesendetArray[1].'","'.$TriggerGesendetArray[2].'","'.$TriggerGesendetArray[3].'","'.$TriggerGesendetArray[4].'");'."\n?>";

    $aktion = fOpen("Triggeralarm.php", "w+");
    foreach($TriggerDatei as $values) fputs($aktion,$values);
    fClose($aktion);


    if ($NeumobileWatch_sensor1 == "") //falls Init nötig
      {  
      $NeumobileWatch_sensor1 = 6;
      $NeumobileWatch_sensor2 = 0;
      $NeumobileWatch_roll = 3;
      }

    if ($NeumobileWatch_Show != "aktiviert") $NeumobileWatch_Show = "deaktiviert";
    if ($NeumobileWatch_tageswertanzeige != "aktiviert") $NeumobileWatch_tageswertanzeige = "deaktiviert";
    if ($NeumobileWatch_notes != "aktiviert") $NeumobileWatch_notes = "deaktiviert";

    $IniArray [$i] = '$mobileWatch_Show = "'.$NeumobileWatch_Show.'";'."\n";$i++;
    $IniArray [$i] = '$mW_sensor1 = "'.htmlentities($NeumobileWatch_sensor1).'";'."\n";$i++;
    $IniArray [$i] = '$mW_sensor2 = "'.htmlentities($NeumobileWatch_sensor2).'";'."\n";$i++;
    $IniArray [$i] = '$mW_roll = "'.htmlentities($NeumobileWatch_roll).'";'."\n";$i++;
    $IniArray [$i] = '$mW_tageswertanzeige = "'.htmlentities($NeumobileWatch_tageswertanzeige).'";'."\n";$i++;
    $IniArray [$i] = '$mW_legende = "'.htmlentities($NeumobileWatch_legende).'";'."\n";$i++;
    $IniArray [$i] = '$mW_notes = "'.htmlentities($NeumobileWatch_notes).'";'."\n";$i++;


  $IniArray [$i] = "\n\n".'$Sensoren = array('."\n";$i++;
  // Sensorcheck: Es darf keine Sensoren mit gleichem Namen geben !

  for ($b=1;$b<=$AnzahlSensoren;$b++)
    {
    if ($NeuSensor[$b] == "") $NeuSensor[$b] = $b; // falls leer --> Zahl
    for ($c=1;$c<=$AnzahlSensoren;$c++)
      {
      if (($NeuSensor[$b] == $NeuSensor[$c]) AND ($b != $c)) $NeuSensor[$b] .= $b; //falls gleicher Sensorname? - Index dran
      }
    } //Ende Sensorcheck



  for ($b=1;$b<=$AnzahlSensoren;$b++)
    {  
    if  ($SensorLoeschen[$b] != "1")
      {
      if ($NeuAnzeige[$b] != "true") $NeuAnzeige[$b] = "false"; //aus Checkbox kein false
      if ($b == 6)   
        { 
        $HELP = $NeuAchse[6].",showInRangeSelector: true";
        $IniArray[$i] = '"'.$NeuSensor[$b].'","'.$NeuFarbe[$b].'","'.$NeuAnzeige[$b].'","'.$HELP.'","'.$NeuEinheit[$b].'",'."\n";
        $i++;
        }
      else 
        {
        $IniArray[$i] = '"'.$NeuSensor[$b].'",';$i++;
        $IniArray[$i] = '"'.$NeuFarbe[$b].'",';$i++;
        $IniArray[$i] = '"'.$NeuAnzeige[$b].'",';$i++;
        $IniArray[$i] = '"'.$NeuAchse[$b].'",';$i++;
        $IniArray[$i] = '"'.$NeuEinheit[$b].'",'."\n";$i++;
        } 

      }                           
    else 
      {//der Sensor wird gelöscht...
      }
    }


  $IniArray[$i] = ");\n";

  //Tageswertoptionen

  $i++;
  $IniArray[$i] = '$TageswertOptionArray = '."array(";
  for ($b=1;$b<=$AnzahlSensoren;$b++)
    {
    $IniArray[$i] .= "'".$NeuTageswertOption[$b]."',";
    }
  if ($NeuTageswertOption[$b] != "") $IniArray[$i] .= "'".$NeuTageswertOption[$b]."');\n\n";
    else $IniArray[$i] .= "'false');\n\n"; //für neuen Sensor als Default



  // Icon vom Sensor festlegen
  $i++;
  $IniArray[$i] = '$Icon = '."array(";
  for ($b=1;$b<$AnzahlSensoren;$b++)
    {
    $IniArray[$i] .= "'".$NeuIcon[$b]."',";
    }
  if ($NeuIcon[$b] != "") $IniArray[$i] .= "'".$NeuIcon[$b]."');\n\n";
    else $IniArray[$i] .= "'no.png');\n\n"; //für neuen Sensor als Default

// Icon statt Name verwenden
  $i++;
  $IniArray[$i] = '$UseIcon = '."array(";
  for ($b=1;$b<$AnzahlSensoren;$b++)
    {
    if ($NeuUseIcon[$b] != "true") $NeuUseIcon[$b] = "false"; 
    $IniArray[$i] .= "'".$NeuUseIcon[$b]."',";
    }
  if ($NeuUseIcon[$b] != "") $IniArray[$i] .= "'".$NeuUseIcon[$b]."');\n\n";
    else $IniArray[$i] .= "'false');\n\n"; //für neuen Sensor als Default  

  $fp = fOpen("beelogger_ini.php", "w+");
  foreach($IniArray as $values){ fputs($fp, $values);}
  $OK = fputs($fp,"?>"); //test ob Datei zu öffnen war
  fclose($fp);

  if ($OK == 0 ) $Message = $SAs[10]."!";



  //Formular zum Splitten der beeloger.csv Datei wurde abgesendet
  if ($SplittenSichern == "1")
    {
    $input = 'beelogger.csv';  
    $SplitArray = file($input);
    $i = sizeof($SplitArray);
    while ($i--) 
      {
      $what = trim($SplitArray[$i]);
      $x = explode( ",", $what );
      $s = sizeof($x); 
      if ($x[$s-1] !='') 
        {   //letzte Spalte = Zeitstempel abfragen
        $AktualisierungDate=$x[$s-1];
        break;
        }
      }
    $Aktualisierung = date("d.m.Y H:i:s",$AktualisierungDate);

    if (($ArchivDateiName == "") OR ($ArchivDateiName == $SAs[129])) $ArchivDateiName = "Archiv".date("ymd",$ErstesSplitDatum)."-".date("ymd",$LetztesSplitDatum); //Datei-Name wurde nicht eingegeben
    $filename = $ArchivDateiName.".csv";
    //Daten zurückschreiben
    $fp = fOpen($filename, "w");
    foreach($SplitArray as $val) 
      {
      $what = trim($val);
      $x = explode( ",", $what );
      $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv  
      if ($x[$s-1] >= $ErstesSplitDatum)fputs($fp, $val);
      if ($x[$s-1] == $LetztesSplitDatum){fputs($fp, $val);break;}
      }
    fclose($fp);

    //Daten zurückschreiben
    $filename = "beelogger.csv";
    $fp = fOpen($filename, "w");
    foreach($SplitArray as $val) 
      {
      $what = trim($val);
      $x = explode( ",", $what );
      $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv 
      if ($x[$s-1] >= $AktuellesSplitDatum) fputs($fp, $val);
      if ($x[$s-1] == $AktualisierungDate){fputs($fp, $val);break;}
      }
    fclose($fp);
    CSVbuilder();
    $Message = $SAs[11].": ".$filename." ".$SAs[12];
    }
  //Ende Splitten der beeloger.csv Datei


// csv-Dateiloeschen
if ($CsvLoeschDatei != "")
    {
        if (file_exists($CsvLoeschDatei)) unlink($CsvLoeschDatei); 
        $Message = $SAs[13].": ".$CsvLoeschDatei." ".$SAs[14];
    }

//bestehenden beelogger löschen (Werte ankommend: N+Zahl und MDuo1 oder MTriple3)

function OrdnerLoeschen($verzeichnis)
  {
  $help = opendir($verzeichnis);
  if($help)
    {
      while ( false !== ($file = readdir($help)) )
      {
        if ( $file != "." and $file != ".." )
        {
        unlink($verzeichnis."/".$file); //alle Dateien aus dem Ordner löschen
        }
      }
    }
  rmdir($verzeichnis); //Den Ordner selbst löschen
  }  

if (strpos($BeeloggerLoeschen, "N") === 0)
    {
    $LoeschNummer = intval(str_replace("N", "", $BeeloggerLoeschen));
    if (file_exists("../beelogger".$LoeschNummer)) OrdnerLoeschen("../beelogger".$LoeschNummer);
    $Message = $SAs[15].": beelogger".$LoeschNummer." ".$SAs[14]; 
    }

if (strpos($BeeloggerLoeschen, "M") === 0)
    {
      $BeeloggerLoeschen = substr($BeeloggerLoeschen, 1); //ohne M
      $BeeloggerLoeschenSign = substr($BeeloggerLoeschen,0,1); //erster Buchstabe
      $BeeloggerLoeschenNumber = substr($BeeloggerLoeschen,-1); //letztes Zeichen = Zahl
    if (file_exists("../".$BeeloggerLoeschen)) OrdnerLoeschen("../".$BeeloggerLoeschen);
    $i=1;
      while (file_exists("../beelogger".$BeeloggerLoeschenSign.$BeeloggerLoeschenNumber."_".$i)) 
      { 
      OrdnerLoeschen("../beelogger".$BeeloggerLoeschenSign.$BeeloggerLoeschenNumber."_".$i);
      $i++;
      }
    $Message = $SAs[16].": ".$BeeloggerLoeschen." ".$SAs[17]." beelogger".$BeeloggerLoeschenSign.$BeeloggerLoeschenNumber."_1 ".$SAs[18]." beelogger".$BeeloggerLoeschenSign.$BeeloggerLoeschenNumber."_".($i-1)." ".$SAs[19];   
    }    


// Neuen beeloggerOrdner anlegen
if ($NeubeeloggerAnlage == "N")
    {
    if ($NeubeeloggerAnlagePasswort != "")
      {  
      if (file_exists("../general_ini.php")) include ("../general_ini.php");//Sprache festlegen 
      $beeloggerNumber=2;  
      while (file_exists("../beelogger".$beeloggerNumber)) 
        {
        $beeloggerNumber++;
        }
      mkdir("../beelogger".($beeloggerNumber), 0777, true);
      copy('../beelogger1/beelogger_show.php', "../beelogger".$beeloggerNumber."/beelogger_show.php");
      copy('../beelogger1/beelogger_config.php', "../beelogger".$beeloggerNumber."/beelogger_config.php");
      copy('../beelogger1/beelogger_log.php', "../beelogger".$beeloggerNumber."/beelogger_log.php");
      if (file_exists('../beelogger1/beelogger_wetter.php')) copy('../beelogger1/beelogger_wetter.php', "../beelogger".$beeloggerNumber."/beelogger_wetter.php");
      
      $pw = '<?php $pw = "'.$NeubeeloggerAnlagePasswort.'"; ?>';
      $aktion = fOpen('../beelogger'.$beeloggerNumber.'/pw.php',"w");
                fWrite($aktion , $pw);
                fClose($aktion);

      $Message = $SAs[20].": beelogger".$beeloggerNumber." ".$SAs[21];    
      }
    else $Message = $SAs[134]; 
    }


elseif ($NeubeeloggerAnlage > 1)
    {
    if ($NeubeeloggerAnlagePasswort != "")
      {   
      switch ($NeubeeloggerAnlage) {
          case '2':
          $NeuMultiType = "Duo";
          break;
          
          case '3':
          $NeuMultiType = "Triple";
          break;

          case '4':
          $NeuMultiType = "Quad";
          break;

          case '5':
          $NeuMultiType = "Penta";
          break;

          case '6':
          $NeuMultiType = "Hexa";
          break;

          case '7':
          $NeuMultiType = "Sept";
          break;
        }  
      if (file_exists("../general_ini.php")) include ("../general_ini.php");//Sprache festlegen 
      $NeuMultiNumber=1;  
      while (file_exists("../".$NeuMultiType.$NeuMultiNumber)) 
        {
        $NeuMultiNumber++;
        }
      mkdir("../".$NeuMultiType.$NeuMultiNumber, 0777, true);
      copy('../beelogger1/beelogger_show.php', "../".$NeuMultiType.$NeuMultiNumber."/beelogger_show.php");
      copy('../beelogger1/beelogger_log.php', "../".$NeuMultiType.$NeuMultiNumber."/beelogger_log.php");
      copy('../beelogger1/beelogger_config.php', "../".$NeuMultiType.$NeuMultiNumber."/beelogger_config.php");
      if (file_exists('../beelogger1/beelogger_wetter.php')) copy('../beelogger1/beelogger_wetter.php', "../".$NeuMultiType.$NeuMultiNumber."/beelogger_wetter.php");
      $pw = '<?php $pw = "'.$NeubeeloggerAnlagePasswort.'"; ?>';
      $aktion = fOpen('../'.$NeuMultiType.$NeuMultiNumber.'/pw.php',"w");
                fWrite($aktion , $pw);
                fClose($aktion);
      
      for ($i=1; $i <= $NeubeeloggerAnlage; $i++) 
          { 
          mkdir("../beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_".$i, 0777, true);
          copy('../beelogger1/beelogger_show.php', "../beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_".$i."/beelogger_show.php");
          copy('../beelogger1/beelogger_config.php', "../beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_".$i."/beelogger_config.php");
          if (file_exists('../beelogger1/beelogger_wetter.php')) copy('../beelogger1/beelogger_wetter.php', "../beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_".$i."/beelogger_wetter.php");
          $pw = '<?php $pw = "'.$NeubeeloggerAnlagePasswort.'"; ?>';
          $aktion = fOpen('../beelogger'.substr($NeuMultiType,0,1).$NeuMultiNumber."_".$i.'/pw.php',"w");
                fWrite($aktion , $pw);
                fClose($aktion);
      
          }
      $Message = $SAs[22].": ".$NeuMultiType.$NeuMultiNumber." ".$SAs[17]." beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_1 ".$SAs[18]." beelogger".substr($NeuMultiType,0,1).$NeuMultiNumber."_".$NeubeeloggerAnlage." ".$SAs[23]."!";    
      }
    else $Message = $SAs[134];  
    }



// Standort Loeschen
    if ($StandortLoeschen == "1")
        {
        if (file_exists("loc.php")) unlink ("loc.php");      
        }  


// Schwarmalarmloeschen
for ($is=1; $is <= $MultiType; $is++) 
    { 
    if ($SchwarmalarmLoeschenArray[$is] == "1")
        {
        if ($MultiType <= 1) $WarnFile = 'warnung.txt';//für Unter- und Normalbeelogger
        else $WarnFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$is.'/warnung.txt'; 
        if (file_exists($WarnFile)) unlink ($WarnFile);      
        }  
    }
  } //if$ configsichern


include ("beelogger_ini.php"); // Werte neu einlesen
if (file_exists("../general_ini.php")) include("../general_ini.php"); //Sprache festlegen
if (file_exists("../beelogger_sprachfiles/Show_Sprache_".$Sprache.".php")) include ("../beelogger_sprachfiles/Show_Sprache_".$Sprache.".php"); // Sprache einbinden

 // Schwarmalarm einlesen 
$SchwarmAlarmMessageArray = array("-","-","-","-","-","-","-","-"); // INIT
if (file_exists('warnung.txt')) 
        {
        include ('warnung.txt');
        $SchwarmAlarmMessageArray[0] = $SchwarmAlarmMessage;
        }

if ($MultiType > 1)
  {
  for ($is=1; $is <= $MultiType; $is++) 
      { 
      $WarnFile = '../beelogger'.$MultiSign.$ServerMultiNumber."_".$is.'/warnung.txt';
      if (file_exists($WarnFile)) 
          {
          include ($WarnFile);
          $SchwarmAlarmMessageArray[$is] = $SchwarmAlarmMessage;
          }
      }
  }

?>

<!DOCTYPE html>

<html lang="de">

<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="../dygraph21.js" charset="utf-8"></script>
<link rel="stylesheet" href="../dygraph21.css">
<link rel="shortcut icon" href="community.beelogger.de/favicon-32x32.png" />
<style type="text/css">
body { font-family: Arial, Helvetica, sans-serif; color: black; background-color: ivory}

textarea { font-size: 16px; }

#Titelbox {
  background-color: transparent;
  position: relative;
  top: 0px;
  left: 10px;
}

#Auswahlbuttons {
  background-color: transparent;
  position: relative;
  top: 10px;
  left: 10px;
  display:inline;
}

#Messagebox {
  background-color: transparent;
  position: relative;
  top: 15px;
  left: 10px;
  width: 95%;
   padding: 5px;
   background: #ffff;
   border: 1px solid black;
   border-radius: 5px;
   font-size: 16px;
  font-family: Arial, 'Times New Roman', serif;
}


#DatenArea {
  background-color: white !important;
  position: relative;
  top: -32px;
  left: 15px;
  width:95%;
  z-index: 1;
}

#DatenAreaAnmerkAend {
  background-color: white !important;
  position: relative;
  top: -32px;
  left: 15px;
  width:95%;
  z-index: 1;
}

#DatenAreaLoeschWert {
  background-color: white !important;
  position: relative;
  top: -32px;
  left: 15px;
  width:95%;
  z-index: 1;
}

#DatenAreaKonfig{
  background-color: white !important;
  position: relative;
  top: -32px;
  left: 15px;
  width:95%;
  z-index: 1;
}

#DatenAreaAendAnmrk {
  background-color: white !important;
  position: relative;
  top: -32px;
  left: 15px;
  width:95%;
  z-index: 1;
}

#Sensorbuttons { 
background-color: transparent;
position: relative;
top: 10px;
left: 5px;
height: 22px;
width: 95%;
display: -webkit-flex; /* Safari */
-webkit-flex-wrap: wrap; /* Safari 6.1+ */
display: flex;
flex-wrap: wrap;
}


<?php //SetButtons

if ($Csvdatei == '') $Csvdatei = $StandardCSV; //Standard, wenn nix angelegt wurde
if (!file_exists($Csvdatei)) $Csvdatei = "beelogger.csv"; //falls der Standard nicht existiert
CSVbuilder();

$AnzahlSensoren = (sizeof($Sensoren)/5); // Eventuell neue Anzahl an Sensoren

if (file_exists($Csvdatei)) 
            { //Letzte Werte aus beelogger.csv auslesen
            $array = file($Csvdatei);
            $what = trim($array[0]);
            $x = explode( ",", $what);
            $CsvSpaltenAnzahl = sizeof($x);
            if ($CsvSpaltenAnzahl == 0) $CsvSpaltenAnzahl = $AnzahlSensoren+4;//falls keine Daten
            $HelpAnzahlSensoren = $AnzahlSensoren;
            $HelpArraySensoren = $Sensoren;
            if ($x[$CsvSpaltenAnzahl-2] == "" AND $x[$CsvSpaltenAnzahl-3] == "") $Abzug = 4; //neue Datei
            else $Abzug = 2; // alte Datei ohne 2 Leerstellen vorm Datum
            if ($AnzahlSensoren < ($CsvSpaltenAnzahl-$Abzug))
              {
              for ($i=$AnzahlSensoren; $i <= ($CsvSpaltenAnzahl-($Abzug+1)); $i++) 
                { //es werden Quasi-Sensoren erzeugt
                $HelpArraySensoren[$i*5] = $i+1;
                $HelpArraySensoren[$i*5+1] = "black";
                $HelpArraySensoren[$i*5+2] = "false";
                $HelpArraySensoren[$i*5+3] = "'y'";
                $HelpArraySensoren[$i*5+4] = "[ ]";
                }
              $HelpAnzahlSensoren =  (sizeof($HelpArraySensoren)/5);
              }
            } //if file_exists..
            else //falls noch keine csv existiert
            {
            $CsvSpaltenAnzahl = $AnzahlSensoren+4;//falls keine Daten
            $HelpArraySensoren = $Sensoren;
            $HelpAnzahlSensoren = $AnzahlSensoren;
            }

for ($s = 0; $s < $HelpAnzahlSensoren; $s++)
{
if ($UseIcon[$s] == "true")
  {
  echo "\n".'#cbl'.$s.' {
     display: inline-block; margin-bottom:-30px; margin-left:10px;
     background-image: url("../beelogger_icons/off_'.$Icon[$s].'");
     background-repeat: no-repeat;
     opacity: 0.5;
     width: 38px;
     border-bottom: 1px solid '.$HelpArraySensoren[$s*5+1].';
     height: 30px;';
     if (is_numeric($HelpArraySensoren[$s*5])) echo "display:none;"; //Buttons die als Name nur Zahl haben
   echo'}
   #cb'.$s.':checked + #cbl'.$s.' {
     background-image: url("../beelogger_icons/off_'.$Icon[$s].'");
     opacity: 1;
     height: 30px;
     border-bottom: 7px solid '.$HelpArraySensoren[$s*5+1].';';
     if (is_numeric($HelpArraySensoren[$s*5])) echo "display:none;"; //Buttons die als Name nur Zahl haben
   echo'}
   #cb'.$s.' {
      display: none;
   }';
  } //if ($UseIcon[$s] == "true")
  else 
  {
  $SchriftWeite = round(9.3 * strlen ($HelpArraySensoren[$s*5]));
  if (strlen ($HelpArraySensoren[$s*5]) < 5) $SchriftWeite += 10; //Kurze Namen brauchen etwas größere Buttons

  // Sensor Buttons
  echo "\n#ck-button".$s." {
  position: relative; top: 10px; left: 10px;
  height:21px; margin:4px; background-color: #EFEFEF;
  overflow:hidden;float:left;
  -webkit-transition-duration: 0.4s; /* Safari */
  transition-duration: 0.4s;";

  if (($HelpArraySensoren[$s*5] == "Gewicht") and $bhome > 0) echo "display:none;";
  if (is_numeric($HelpArraySensoren[$s*5])) echo "display:none;"; //Buttons die als Name nur Zahl haben

  echo "\n  border-radius:15px; border:2px solid ".$HelpArraySensoren[$s*5+1];

  echo "  \n}";

  echo "
  #ck-button".$s." input:not(:checked) + :hover {
    background-color:lightgrey;
    color:".$HelpArraySensoren[$s*5+1].";
  }";
  echo "
  #ck-button".$s." input:checked + :hover {
    background-color:".$HelpArraySensoren[$s*5+1].";
    color:lightgrey;
  }";

  echo"
  #ck-button".$s." label {
    color:".$HelpArraySensoren[$s*5+1].";
    float:left;
    width:".$SchriftWeite."px;
  }
  #ck-button".$s." label span {
    text-align:center;
    padding:3px 0px;
    display:block;
  }
  #ck-button".$s." label input {
    position:absolute;
    top:-20px;
  }
  #ck-button".$s." input:checked + span {
    background-color:".$HelpArraySensoren[$s*5+1].";
    color:white;
  }";
  }// else
}//for ($s = 0; $s < $HelpAnzahlSensoren; $s++)

//Button Tageswerte
echo "
#ck-button".$s." {
position: relative; top: 10px; left: 10px;
height:21px; margin:4px; background-color: #EFEFEF;
overflow:hidden;float:left;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;border-radius:4px;
border:2px solid #000000;";
  if ($MultiType > 1 OR $bhome > 0)  echo "\ndisplay:none;"; 
echo "}";
echo "
#ck-button".$s." input:not(:checked) + :hover {
  background-color:grey;
  color:black;
}";
echo "
#ck-button".$s." input:checked + :hover {
  background-color:grey;
  color:white;
}";

$SchriftWeite = round(9.1 * strlen ($SAs[64])) ;
echo "
#ck-button".$s." label {
  color:black;
  float:left;
  width:".$SchriftWeite."px;
}
#ck-button".$s." label span {
  text-align:center;
  padding:3px 0px;
  display:block;
}
#ck-button".$s." label input {
  position:absolute;
  top:-20px;
}
#ck-button".$s." input:checked + span {
  background-color:black;
  color:#fff;";
echo "}";

//vvvvvvvvvvvvvvvvvvvvvvvv
echo '
 #Nummer_Box {
  background-color: transparent;
  position: relative;
  top: 17px;
  left: 10px;';
  if ($MultiType > 1 OR $bhome > 0)  echo "display:none;";
  echo '}';


echo '
 #sum_grenze {
  background-color: transparent;
  position: relative;
  top: 17px;
  left: 10px;';
  if ($MultiType > 1 OR $bhome > 0)  echo "display:none;";
  echo '}';

$s++;


// Button Summe 
echo "
#ck-button".$s." {
position: relative; top: 10px; left: 10px;
height:21px; margin:4px; background-color: #EFEFEF;
overflow:hidden;float:left;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;z-index: -1;border-radius:4px;
border:2px solid orange;";
  if ($MultiType > 1 OR $bhome > 0)  echo "\ndisplay:none;"; 
echo "}";

echo "
#ck-button".$s." input:not(:checked) + :hover {
  background-color:#FFC31E;//lightorange;
  color:grey;
}";
echo"
#ck-button".$s." input:checked + :hover {
  background:#FFC31E;//lightorange;
  color:grey;
}";
echo"
#ck-button".$s." label {
  color:orange;
  float:left;
  width:58px;
}
#ck-button".$s." label span {
  text-align:center;
  padding:3px 0px;
  display:block;
}
#ck-button".$s." label input {
  position:absolute;
  top:-20px;
}
#ck-button".$s." input:checked + span {
  background-color:orange;
  color:#fff;";
echo"}";


if ($BeutenLeergewicht > 0) echo "
#ck-button99 {
position: relative; top: 10px; left: 10px;
height:21px; margin:4px; background-color: WHITE;
overflow:hidden;float:left;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;border-radius:15px;
z-index: -1;border-radius:4px;border:2px solid grey;}

#ck-button99 label {
  color:grey;
  float:left;";
  $SchriftWeite = round(11 * strlen ($SAs[67])) ;
  if ($Honigraeume[0] < 1 AND $Honigraeume[2] < 1 AND $BeutenUtils[0][0] < 1 AND $BeutenUtils[1][0] < 1 AND $BeutenUtils[2][0] < 1) echo"  width:".($SchriftWeite+50)."px;";
  else echo"  width:".($SchriftWeite+100+(($Honigraeume[0]+$Honigraeume[2]+$BeutenUtils[0][0]+$BeutenUtils[1][0]+$BeutenUtils[2][0])*20))."px;";
  echo"
}

#ck-button99 label span {
  text-align: center;
  padding:3px 0px;
  display:block;
}
#ck-button99 label input {
  position:absolute;
  top:-20px;
}
#ck-button99 input:checked + span {
  background-color: white;
  color:#fff;
}";


//Dummy für Abstand
echo "
#ck-button999 {
position: relative; top: 10px; left: 10px;
height:25px; margin:4px; background-color: WHITE;
overflow:hidden;float:left;
-webkit-transition-duration: 0.4s; /* Safari */
transition-duration: 0.4s;
z-index: -1;border-radius:px;border:0px solid grey;}

#ck-button999 label {
  color:grey;
  float:left;";
  echo"  width:1px;";
  echo"
}

#ck-button999 label span {
  text-align: center;
  padding:0px 0px;
  display:none;
}
#ck-button999 label input {
  position:absolute;
  top:-20px;
}
#ck-button999 input:checked + span {
  background-color: white;
  color:#fff;
}";
?>




#AlleAuswahlbutton {
   position: relative;
   top: 8px;
   width:100%;
   margin:4px;
   background-color:#EFEFEF;
   border-radius:4px;
   border:1px solid #D0D0D0;
   overflow:auto;
   float:left;
}
#AlleAuswahlbutton :hover {    background:#fff;}
#AlleAuswahlbutton label {    color:black;    float:left;    width:100%;}
#AlleAuswahlbutton label span {    text-align:center;    padding:3px 0px;    display:block;}
#AlleAuswahlbutton label input {    text-align:center; position:absolute;    top:-20px;}


#NavigationAnzeige {
  background-color: transparent;
  position: relative;
  top: 10px;
  left: 10px;
  display: -webkit-flex; /* Safari */
  -webkit-flex-wrap: wrap; /* Safari 6.1+ */
  display: flex;
  flex-wrap: wrap;
}


#KonfigAnzeige {
      position: relative;
      background-color: white !important;
      top: -50px;
      left: 10px;
  }

#beelogger_grafik {
      position: relative;
      top: 20px;
      left: 10px;
}
</style>
<title>beelogger.de - Datenlogger für Imker</title>
</head>

<?php
if (file_exists("../general_ini.php")) include ("../general_ini.php"); //unter anderem Sprache festlegen
//if ($Sprache == "") $Sprache = 1;
//Ermittlung $Bienenvolkbezeichnung
if ($Bienenvolkbezeichnung == "auto")
  { //nur wenn oben kein eigener Name angegeben wurde
  $pfad = getcwd(); //  kompletter Verzeichnispfad
  $verz = strrchr($pfad, "/"); // Verzeichnisname einschließlich des / zu Beginn
  $BienenvolkbezeichnungToEcho = str_replace("/","",$verz);//  / noch entfernt 
  }
else $BienenvolkbezeichnungToEcho = html_entity_decode($Bienenvolkbezeichnung);




if (file_exists('week.csv')) 
  {//array füllen mit aktueller csv-datei
  $input = 'week.csv';  
  $array = file($input);

  $i = sizeof($array);
  while ($i--) 
    {
    $what = trim($array[$i]);    
    $x = explode( ",", $what );
    $LetzteZeile = $x; //merke Letzte Zeile für beelogger_ini.php-Ansicht
    $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
    if ($x[$s-1] !='') 
      {     // um hier die letzte Spalte = Zeitstempel abzufragen
      $AktualisierungsStamp=$x[$s-1];
      $AktualisierungStampMessage = $AktualisierungsStamp;
      if ($MultiType <= 1) 
        {
        $LetztesLicht = $x[5]; // wird gebraucht um das Sendeintervall zu ermitteln
        $LetztesVBatt = $x[7]; // wird gebraucht um den Akkuzustand anzuzeigen
        $LetztesVSolar = $x[8]; // wird gebraucht um das Sendeintervall zu ermitteln

        }
      elseif ($MultiType > 1) 
        {
        $LetztesLicht = $x[5]; // wird gebraucht um das Sendeintervall zu ermitteln
        $LetztesVBatt = $x[(9+($MultiType-1)*3)]; // wird gebraucht um das Sendeintervall zu ermitteln
        $LetztesVSolar = $x[(10+($MultiType-1)*3)]; // wird gebraucht um das Sendeintervall zu ermitteln
        }
      $LetztesGewicht = $x[6];
      break;
      }
    }
    $NummerLetzteZeile = $i;
    $ZeitFuerNeueNote = $AktualisierungsStamp; //Hilfe für neue Note bei neuer Zeitintervalleingabe
    $Aktualisierung = date("d.m.Y H:i:s",$AktualisierungsStamp);
  } 
  else 
    {
    $Message = $SAs[24];
    $Aktualisierung="-<br>";
    }

//NotesUpdater Abwärtskompatibilität - Sensornamen mit Sensornummern ersetzen
if (file_exists("notes.csv"))
  {
  $NotesArray = file("notes.csv");
  $ni = sizeof($NotesArray);


  for ($a = 0 ; $a < $ni; $a++) 
    {
    $what = trim($NotesArray[$a]);    
    $x = explode( ",", $what );
    $s = sizeof($x);
    if (!is_numeric($x[0])) 
      {
      $NewNotesArray[$a] = $NotesArray[$a]; //default  
      for ($i=0; $i < $AnzahlSensoren; $i++) 
        { 
        if ($x[0] == html_entity_decode($Sensoren[$i*5])) 
          {
          $NewNotesArray[$a] = $i.","; //erstes Element 
          for ($in=1; $in < ($s-1); $in++) 
            { 
            $NewNotesArray[$a] .= $x[$in].",";
            }
          $NewNotesArray[$a] .= $x[$in]."\r\n";  //letztes Element     
          }
        }
      } //if (!is_numeric($x[0])) 
      else   $NewNotesArray[$a] = $NotesArray[$a];
    } //for $a

  //Daten zurückschreiben
   if ($NewNotesArray != "")
    {
    $fp = fOpen("notes.csv", "w");
    foreach($NewNotesArray as $values){ fputs($fp, $values);}
    fclose($fp);
    }
  }
  else {$fp = fOpen("notes.csv", "w"); fclose($fp);}



//Formular zum Erzeugen einer neuen Anmerkung wurde abgeschickt -------------------
if ($Erzeugen == "1")
  {
  if ($Passwort != '')
    {
    $Message = $SAs[25]."!<br>"; //oder mit falschem Passwort
    if ($Passwort == $BeeloggerShowPasswort)
      {  // Passwortcheck korrekt - Neue Anmerkung erzeugen
      for ($i=0; $i < $AnzahlSensoren ; $i++) 
        { 
        if ($Sensor == html_entity_decode($Sensoren[$i*5])) $SensorNummer = $i;
        }
      $Termin = substr($Termin, 0, -3);
      $Aktualisierung = date("Y/m/d H:i:s",$Termin);
      if ($Kurztext == '') $Kurztext = '?'; // Falls Felder leer - Event nachträglich in "Anmerkungen" editierbar
      if ($Langtext == '') $Langtext = $SAs[26]; 
      $Langtext = str_replace(",",".", $Langtext);// Kommas müssen raus
      $Kurztext = str_replace(",",".", $Kurztext);// Kommas müssen raus
      $value =  $SensorNummer.",".$Aktualisierung.",";
      

      if ($Note_icon != "") 
        {
        $value .= $Note_icon;
        if ($Langtext == $SAs[26] OR $Langtext == $SAs[133]) 
          {
          if ($Sprache == "1") $value .= "&".substr(substr($Note_icon,21),0,-4);
          else 
            {
            $i=0;
             while ($i < sizeof($IconBezeichner))
              {
              $Gefunden = false; 
              if (substr($Note_icon,19) == $IconBezeichner[$i][1])
                {
                $value .= "&".$IconBezeichner[$i][$Sprache];
                $Gefunden = true;
                break; 
                }
              $i++;  
              }
            if ($Gefunden == false) $value .= "&".substr(substr($Note_icon,21),0,-4);
            }
          }
        else $value .= "&".html_entity_decode($Langtext);
        }
      else $value .= html_entity_decode($Kurztext)."&".html_entity_decode($Langtext);

      $value .= ",".$Termin."\r\n";

      $datei = "notes.csv";
      $fp = fOpen($datei, "a");//Daten zurückschreiben notes.csv öffnen und Wert anfügen oder Datei neu erstellen
      $OK = fputs($fp, $value);
      if ($OK > 0 ) $Message = $SAs[27]."!<br>";
      fclose($fp);
      } //if $ BeeloggerShowPasswort    
    }//if $Passwort
  }// if $Erzeugen


//Formular zum löschen einer Anmerkung wurde abgeschickt Übergabevariable $Termin
if ($Loeschen == "1"){   
if ($Passwort == $BeeloggerShowPasswort){
  $filename = "notes.csv";
  $narray = file($filename);
  $Counter = 0;
  $fp = fOpen($filename, "w");
  foreach($narray as $val){ 
    if ($Counter != $Termin){   
      $OK = fputs($fp, $val);
    }
    $Counter++;
  }
  fclose($fp);
 
  if ($OK > 0) $Message = $SAs[28]."!<br>";
  if ($Counter == 1) $Message = $SAs[29]."<br>"; //Der Fall, das die letzte Anmerkung gelöscht wurde
} //if $passwort
else $Message = $SAs[25]."!";   
}//if loeschen = 1



//Formular zum speichern einer veränderten Anmerkung wurde abgesendet----------
if ($Speichern == "1")
  {   
  $filename = "notes.csv";
  $narray = file($filename);
  $ni = sizeof($narray);

  $AltNeuTermin = explode("%",$Termin); 
  $AltNeuSensor = explode("%",$Sensor);

  for ($i=0; $i < $AnzahlSensoren ; $i++) 
    { 
    if ($AltNeuSensor[0] == html_entity_decode($Sensoren[$i*5])) $AltSensorNumber = $i;
    if ($AltNeuSensor[1] == html_entity_decode($Sensoren[$i*5])) $NeuSensorNumber = $i;
    }

//alte Note im narray finden und löschen
  for ($a = 0 ; $a <= $ni; $a++) 
    {
    $what = trim($narray[$a]);    
    $x = explode( ",", $what );
    $s = sizeof($x);
    if ( ($x[$s-1] == $AltNeuTermin[0]) && ( ($x[0] == $AltNeuSensor[0]) OR  ($x[0] == $AltSensorNumber) ) ) $narray[$a]= ''; 
    } //for $a

  $r=0;
  for ($b = 0; $b <= $ni; $b++)
    {
    if ($narray[$b] != '') $kleinarray[$r]=$narray[$b];
    $r++;   
    }//for $b
  $Aktualisierung = date("Y/m/d H:i:s",$AltNeuTermin[1]);
  if ($Kurztext == "") $Kurztext = "?";

  $kleinarray[$r] = $NeuSensorNumber.",".$Aktualisierung.",";
  if ($Note_icon == "") 
    {
    $kleinarray[$r] = $kleinarray[$r].html_entity_decode($Kurztext);
    if ($Langtext == $SAs[26]) $Langtext = substr(substr($Note_icon,2),0,-4);
    }
  else $kleinarray[$r] = $kleinarray[$r].$Note_icon;

  $kleinarray[$r] = $kleinarray[$r]."&".html_entity_decode($Langtext).",".$AltNeuTermin[1]."\r\n"; //veränderte Note angehängt
  //Daten zurückschreiben
  $fp = fOpen($filename, "w");
  foreach($kleinarray as $values){ fputs($fp, $values);}
  fclose($fp);
  $Message = $SAs[31]."<br>";
                    
  }//if speichern = 1
// ENDE veränderte Anmerkung speichern


//Formular zum löschen oder Ändern eines Wertes Übergabe: $Termin + $Sensor + $NeuWert
if ($Wertloeschen == "1"){
if ($Passwort != ''){
 $Message = $SAs[25]."!<br>"; //oder mit falschem Passwort
 if ($Passwort == $BeeloggerShowPasswort){  // Passwortcheck korrekt - Neue Anmerkung erzeugen
  $Termin = substr($Termin, 0, -3); //Format kommt mit 3 Stellen zuviel an
  $input = "beelogger.csv";  // die Mutterdatei wird natürlich bearbeitet 
  $array = file($input);
  
  //Stelle des Sensors im Array suchen
  $a = 0;
  while ($a < $AnzahlSensoren) {
    if  (html_entity_decode($Sensoren[$a*5]) == $Sensor) break; 
    $a++;
  }
  $Wertposition = ($a+1) ; //das Datum steht noch an Stelle 0 in der csv-Datei deshalb um einen erhöhen


  // wert im Array suchen....
  $i = sizeof($array);
  while ($i--) {
    $what = trim($array[$i]);
    $x = explode( ",", $what );
    $s = sizeof($x);     //hier wird die Anzahl von Spalten pro Zeile im beelogger.csv ermittelt
    if ($x[$s-1] == $Termin) break;
  }          
  $Arrayposition = $i; //Zeile mit dem zu ändernden Wert
  $value = "";
  for ($v = 0; $v < ($s-1); $v++){
    if ($v == $Wertposition) $value = $value.$NeuWert.",";  // Wert gelöscht oder ersetzt
    else $value = $value.$x[$v].",";
  }
  $value = $value.$x[($s-1)]."\r\n"; //letzte Position ohne Komma mit Umbruch

  $array[$Arrayposition] = $value; 

  //Daten zurückschreiben
  $filename = "beelogger.csv";
  $fp = fOpen($filename, "w");
  foreach($array as $values) {
    $send =  fputs($fp, $values);
  }
  fclose($fp);

CSVbuilder(); //month.csv und week.csv erneuern

if ($send > 0 ) $Message = $SAs[30];
} //if $ Passwort    
}//if $Passwort
}// if Wert löschen



echo'
<body onresize="my_onresize()">
<h5>  
<div id="Titelbox">';


echo "\n<table width=98%>";
  echo "\n<td>";
  echo "\n<table>";
    echo "\n<td><FONT size=5><b>beelogger.de - ".$SAs[63].'</b></FONT></td>';

    if ($show_version) echo "\n<td>&nbsp&nbsp&nbspVersion:&nbsp".$Softwareversion."</td>";
    echo "\n<td><FONT SIZE=6 font color=#088A08>"; 
    echo "&nbsp;".html_entity_decode($BienenvolkbezeichnungToEcho)."&nbsp;</FONT></td>";

    if ($KoeAnzeige != '' AND $KoeAnzeige != "deaktiviert") echo "<td><img src='../beelogger_icons/".$KoeAnzeige."' width='30' height='30' style='margin-bottom:-5px'; title='".$KoeInfo."'></td>";
 
    echo "\n</table>\n</td>";
    echo "\n<td align=right>\n<table><tr><td align=center>";

          if ($WechselMapName == "") $MapNameNow = "beelogger_map.php";
          else $MapNameNow = $WechselMapName;

          if (file_exists($MapNameNow)) 
            {
            include($MapNameNow);
            $MultiMapper = FALSE;
            if ($MapNameNow != "beelogger_map.php") // falls Unterbeelogger eines Multi
              {
              $beeloggerNummer = substr($beelogger, -1);
              if ($beeloggerMapWaage == $beeloggerNummer) $MultiMapper = TRUE;
              }
            if ($MultiType >= 1) $MultiMapper = TRUE;




            
             //MAP1
            if ($beeloggerMap != "")
               {
               if ($beeloggerMap == "aktiviert" AND $MultiMapper == TRUE) 
                  {
                  echo"\n<a href='https://beelogger.de/?page_id=196999' target='_blank'><img src='../beelogger_icons/map_aktiv.png' width='70' height='30' style='margin-bottom:-9px'; title='".$SAs[32].$ServerAntwort."- beelogger".$MultiSign.$ServerMultiNumber."_".$beeloggerMapWaage."'></a>";
                  $MapStatus = $ServerAntwort;
                  }
                } 
               elseif ($MultiMapper == TRUE AND  ($beeloggerMap == "deaktiviert" OR  $beeloggerMap == "inaktiv")) 
                {
                echo"\n<a href='https://beelogger.de/?page_id=196999' target='_blank'><img src='../beelogger_icons/map_inaktiv.png' width='70' height='30' style='margin-bottom:-9px'; title='beeloggerMap INAKTIV'></a>";
                $MapStatus = $SAs[33];
                }


            //MAP2
            else
              {
              if (($beeloggerMap2Status == "[UPDATED]" OR $beeloggerMap2Status == "[CHANGED]") AND $MultiMapper == TRUE) 
                { //nochmal gegenchecken
                $ch = curl_init("https://map2.beelogger.de/log.php?mapid=".$beeloggerMap2ID."&beeid=".$beeloggerMap2BeeID."&do=372664"); // cURL ínitialisieren
                curl_setopt($ch, CURLOPT_HEADER, 0); // Header soll nicht in Ausgabe enthalten sein
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                $ServerAntwort = curl_exec($ch); // Ausführen
                curl_close($ch); // Objekt schließen und Ressourcxen freigeben
                if ($ServerAntwort == "[ONLINE]") 
                  {
                  echo"\n<a href='https://beelogger.de/?page_id=196999' target='_blank'><img src='../beelogger_icons/map_aktiv.png' width='70' height='30' style='margin-bottom:-9px'; title='beeloggerMap: ".$ServerAntwort;
                  $MapStatus = $ServerAntwort;

                  if ($MultiType != 1) echo "- beelogger".$MultiSign.$ServerMultiNumber."_".$beeloggerMapWaage;
                  echo "'></a>";
                  }
                else 
                  {
                  echo"\n<a href='https://beelogger.de/?page_id=196999' target='_blank'><img src='../beelogger_icons/map_inaktiv.png' width='70' height='30' style='margin-bottom:-9px'; title='beeloggerMap2 ".$SAs[33]."'></a>";
                  $MapStatus = $SAs[33];
                  }
                }
              elseif ($MultiMapper == TRUE OR $beeloggerMap2 == "deaktiviert") 
                {
                echo"\n<a href='https://beelogger.de/?page_id=196999' target='_blank'><img src='../beelogger_icons/map_inaktiv.png' width='70' height='30' style='margin-bottom:-9px'; title='beeloggerMap2 ".$SAs[33]."'></a>";
                $MapStatus = $SAs[33];
                } 
              }
            } //if (file_exists($MapNameNow)) 

 
  echo "</td></tr>";
  
  $TriggerAktiv = false;
  if ($WechselIniName != "") include($WechselIniName);
  for ($i=1; $i < 5; $i++) 
    {
    if ($TriggerAlarmArray [$i][1] == "aktiviert" OR $TriggerAlarmArray [$i][2] == "aktiviert") 
      {
      $TriggerAktiv = true;
      break;
      }
    }

  if ($TriggerAktiv)
    {

    echo "<tr align=center><td>";
    for ($i=0; $i < 5; $i++) 
      { 
      if ($TriggerAlarmArray [$i][1] == "aktiviert" OR $TriggerAlarmArray [$i][2] == "aktiviert") echo '<img src="../beelogger_icons/n_Triggeralarm.png" title="Triggeralarm'.$i.': '.$Sensoren[(5*$TriggerAlarmArray[$i][3])].' '.$TriggerAlarmArray[$i][4].' '.$TriggerAlarmArray[$i][5].'" height=25 style="margin-top:0px";>';
      }

    echo "</tr>";
    }
  else echo "\n<tr align=center><td style='font-size:1.1vw'>".$MapStatus."</td></tr>";
    
  include("beelogger_ini.php"); //REINIT  
  echo "\n</table>";

  
 if (file_exists("beelogger_wetter.php")) include("beelogger_wetter.php");
 echo "\n</td></table>";

 echo "\n</div>";

if (($Config != "1") OR ($Passwort != $BeeloggerShowPasswort)){ ?>
<!------- 3 Auswahlbuttons -------->
<div id="Auswahlbuttons"> 
<b><?php echo $SAs[35] ?>: </b>
<?php  // Listbox beelogger
 $dir = "../";
 $files = scandir($dir);
 natcasesort($files);


 //Array angezeigter beelogger neu anordnen - nach Duo Triple(E), Quad(M), Hexa(N), Penta(R) und Sept
  $ba=0;
  $Passwort_Show_Counter=0;
  foreach ($files as $file) 
    {
    if ($file != "mobile" && $file != "beelogger_icons" && $file != "beelogger_sprachfiles" && $file != "." && $file != ".." && is_dir("../".$file))
      {
      $ininame = "../".$file."/beelogger_ini.php";
      $showname = "../".$file."/beelogger_show.php";
      if (file_exists($ininame)) include ($ininame);
      
      if ($BeeloggerShowPasswort == "Show") 
        {
        $AlertArray[$Passwort_Show_Counter] = $file; //Ordnernamen merken
        ++$Passwort_Show_Counter; //für Alert-Info
        }

      $file = str_replace("T","E",$file);
      $file = str_replace("Q","M",$file);
      $file = str_replace("H","N",$file);
      $file = str_replace("P","R",$file);
      $beeloggerArray[$ba] = $file;
      $ba++; 
      }
    }

  natcasesort($beeloggerArray);
  $Newfiles = $beeloggerArray;

  $ba=0;
  foreach ($Newfiles as $file) 
      {
      $file = str_replace("E","T",$file);
      $file = str_replace("M","Q",$file);
      $file = str_replace("N","H",$file);
      $file = str_replace("R","P",$file);
      $beeloggerArray[$ba] = $file;
      $ba++;
      }

// Farben zuordnen
   $GruppenFarben = array("green","red","blue","orange","cyan","deepskyblue","purple","black","lime");
   $FarbCounter=0;

   $beeloggerArraySize = sizeof($beeloggerArray);
   $MultiArray = array('Duo','Triple','Quad','Hexa','Penta','Sept');


    foreach ($MultiArray as $EachMulti) 
      {
      for ($i=0; $i < $beeloggerArraySize; $i++) 
        {
        if (strpos($beeloggerArray[$i], $EachMulti) !== FALSE)
          {
          $EachMultiNumber = intval(str_replace($EachMulti,"",$beeloggerArray[$i]));
          $beeloggerArrayColor[$i] = $GruppenFarben[$FarbCounter];
          for ($j=0; $j < $beeloggerArraySize; $j++) 
            {
            if (strpos($beeloggerArray[$j], (substr($EachMulti,0,1).$EachMultiNumber)) !== FALSE)
              { 
              $beeloggerArrayColor[$j] = $GruppenFarben[$FarbCounter];
              }
            }
          $FarbCounter++;
          }
        }
      }
//Ende Farben Zuordnen


 echo"<select name='beelogger' onChange='document.location.href=this.value'".' title="'.$SAs[34].'">';

for ($i=0; $i < $beeloggerArraySize; $i++) 
   { 
   $filename = "../".$beeloggerArray[$i]."/beelogger_show.php";
   if (file_exists($filename)) 
      {
        $ininame = "../".$beeloggerArray[$i]."/beelogger_ini.php";
        $csvname = "../".$beeloggerArray[$i]."/beelogger.csv";
      if ($beeloggerArray[$i] == basename(realpath('.'))) echo '<option style="color:'.$beeloggerArrayColor[$i].';" value='.$filename." selected>";
      else 
        {
        if (file_exists($ininame) AND file_exists($csvname)) echo '<option style="color:'.$beeloggerArrayColor[$i].';" value='.$filename.">";
        else echo '<option style="color:gray;" value='.$filename.">";
        }
        $ShowFileName = str_replace("beelogger", "", $beeloggerArray[$i]);
        $Bienenvolkbezeichnung = "auto"; //INIT     
        if (file_exists($ininame)) include ($ininame);
        if ($Bienenvolkbezeichnung != "auto") echo $ShowFileName." (".html_entity_decode($Bienenvolkbezeichnung).")";
        else echo $beeloggerArray[$i]."</option>";
      }//if (file_exists($filename))         
    }

echo "</select>";

$RollPeriod = "1"; //Default
$TageswertAnzeige = 'false'; //Default
$IntervallSendeSteuerung = "deaktiviert"; //
$AkkuLeerSchwelle = 0; //RESET
$AkkuVollSchwelle = 0;//RESET
include ("beelogger_ini.php"); // passende Werte zur Show wieder initialisieren

if ($MultiType > 1)
  {
  echo"&nbsp;&nbsp;";
  echo '<input type="button" style="background-color:#a9dfbf;" value="'.$MultiTypeName.$ServerMultiNumber.'" onclick="window.location.href=';
    echo "'../".$MultiTypeName.$ServerMultiNumber."/beelogger_show.php'".'"/>&nbsp;&nbsp;';

  for ($i=1; $i <= $MultiType; $i++) 
    {
    echo '<input type="button" style="background-color:hsl(360,0%,'.(100-$i*10).'%);" value="'.$MultiSign.$ServerMultiNumber."_".$i.'" onclick="window.location.href=';
    echo "'../beelogger".$MultiSign.$ServerMultiNumber."_".$i."/beelogger_show.php'".'"/>&nbsp;&nbsp;';
    }  
  }

  if ($MultiType == 0)
    {
    echo"&nbsp;&nbsp;";  
    $MultiBeeloggerNummer = intval(substr($beelogger,-1));
    ?>
    <input type="button" value="<?php echo $MultiTypeName.$ServerMultiNumber; ?>" style="background-color:hsl(360,0%,95%);" onclick="window.location.href='../<?php echo $MultiTypeName.$ServerMultiNumber ?>/beelogger_show.php'">
    
    <?php

    for ($i=1; $i <= $MultiTypeAnzahl; $i++) 
      {
      echo '<input type="button" style="background-color:';
      if ($MultiBeeloggerNummer == $i) echo "#a9dfbf";
      else echo "hsl(360,0%,".(100-$i*10).'%)';
      echo ';" value="'.$MultiSign.$ServerMultiNumber."_".$i.'" onclick="window.location.href=';
      echo "'../beelogger".$MultiSign.$ServerMultiNumber."_".$i."/beelogger_show.php'".'"/>&nbsp;&nbsp;';
      }  
    }

echo'&nbsp;&nbsp;<a href="../beelogger_mobileWatch.php"><img src="../beelogger_icons/watch.png" title="mobileWatch" style="margin-bottom:-9px" height=30 width=18 ></a>&nbsp;';

if ($MultiType >= 2) $GoToBeelogger = "beelogger1";
else $GoToBeelogger = $beelogger;
echo'&nbsp;&nbsp;<a href="../beelogger_mobileCheck.php?beelogger='.$GoToBeelogger.'"><img src="../beelogger_icons/mCheck.png" title="mobileCheck" style="margin-bottom:-9px" height=30 width=18 ></a>&nbsp;';

//Anmerkung Button
if (file_exists('notes.csv')) 
  {
  $input = "notes.csv";
  $narray = file($input);
  $ni = sizeof($narray);
  if ($ni > 0)  echo ' <input type="button" value="'.$SAs[37].' '.$ni.' '.$SAs[38].'" id="AnmerkungenBtn"';                                               
  else 
    {
    echo ' <input type="button" disabled value="'.$SAs[39].'" id="AnmerkungenBtn"'; 
    $MerkeDassButtonDisabled = true;
    }

  echo 'onclick="AnmerkungBtnPressed()"  title="'.$SAs[40].'"/><b>&nbsp;</b>';                                               
  } //if file exists


//Werte löschen Button

echo'<input type="button" value="'.$SAs[41].'" id="WertBtn" onclick="WertBtnPressed()"  title="'.$SAs[42].'">&nbsp;';


// Konfiguration Button
if ($Passwort == $BeeloggerShowPasswort) 
    { //Passwort wurde bereits eingegeben
    echo'<form action="beelogger_show.php" method="post" id="KonfigBtnForm" style="display:inline;">';
    echo "<input type='hidden' name = 'passwort' value = '".$Passwort."'>";
    echo'&nbsp;&nbsp;<button type="submit" id="KonfigBtnInForm" value="1" title="'.$SAs[44].'" name="config">'.$SAs[43].'</button>';
    echo '</form>';
    }
else  echo '<input type="button" id="KonfigBtn" value="'.$SAs[43].'" onclick="KonfigBtnPressed()"  title="'.$SAs[44].'">';




echo "&nbsp;".$SAs[45].":<select name='neustandardcsv' onChange='document.location.href=this.value'".' title="'.$SAs[46].'">';
// Listbox csv-dateien
foreach (glob("*.csv") as $filename)
  {
  if (file_exists($filename) AND $filename != "notes.csv") 
    {
    echo "<option value=beelogger_show.php?direktcsv=".$filename;

    if ($filename == $Csvdatei) echo " selected";
    echo ">";
    
    if ($filename == "week.csv") echo $SAs[47];
    elseif ($filename == "month.csv") echo $SAs[48];
    elseif ($filename == "beelogger.csv") echo $SAs[49];
    else echo substr($filename,0,-4);
    echo "</option>";
    }
  }
echo "</select>";
if ($WechselIniName != "") include($WechselIniName);

if ($AkkuLeerSchwelle != 0 && $AkkuVollSchwelle != 0 && $LetztesVBatt != "")
              {
              if ($LetztesVBatt > $AkkuVollSchwelle) echo'&nbsp;<img src="../beelogger_icons/Akku100.png" width="26" height="12" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              elseif ($LetztesVBatt < $AkkuLeerSchwelle) echo'&nbsp;<img src="../beelogger_icons/Akku25.png" width="26" height="12" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              else echo'&nbsp;<img src="../beelogger_icons/Akku50.png" width="26" height="12" style="margin-bottom:-2px" title="'.$LetztesVBatt.'V">';
              }
            $UseIcon = array(); //INIT Abwärtskomp falls kein UseInit vorhanden
            include ("beelogger_ini.php"); //REINIT

echo "</div>";

// ------- ^^^^^  3 Auswahlbuttons -------->    

}

    if (fmod($SommerBeginn, 1) == 0.5) $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
    else $SommerBeginnInTagen = date("z",strtotime(round($SommerBeginn)."/1"));

if ($WinterBeginn != "deaktiviert")
    {
    if (fmod($WinterBeginn, 1) == 0.5) $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn, 0, PHP_ROUND_HALF_DOWN)."/15"));
    else $WinterBeginnInTagen = date("z",strtotime(round($WinterBeginn)."/1"));
    }

    if ($IntervallSendeSteuerung == "lichtgesteuert") { //lichtgesteuerte Sendeintervalle
    if ($LetztesLicht > 1) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
    else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 
    
    if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute

    if ($LetztesLicht == "") $IntervallHelp = "zeitgesteuert"; // Lichtsteuerung ohne Werte geht nicht
    } // if lichtgesteuert

    if ($IntervallSendeSteuerung == "solarspannungsgesteuert") { //solarspannungsgesteuerte Sendeintervalle
    if ($LetztesVSolar > 0.3 ) $SendeIntervall = $SommerSendeIntervallTag; //Sonnenlicht vorhanden
    else $SendeIntervall = $SommerSendeIntervallNacht; //nachts --> Sensorwert = 0 
    if ($WinterBeginn != "deaktiviert") if ((date("z") < $SommerBeginnInTagen) OR (date("z") > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall; //im Winter immer die Winterzeit, da tagsüber keine Aktion in der Beute
    
    if ($LetztesVSolar == "") $IntervallHelp = "zeitgesteuert"; // Spannungsteuerung ohne Werte geht nicht
    } // if solarspannungsgesteuert

    if ($IntervallSendeSteuerung == "zeitgesteuert" OR $IntervallHelp ==  "zeitgesteuert") 
      { //zeitgesteuerte Sendeintervalle
      if ((intval(date("G",$AktualisierungsStamp)) >= $SommerTagZeit)  AND (intval(date("G",$AktualisierungsStamp)) < $SommerNachtZeit)) $SendeIntervall = $SommerSendeIntervallTag;//Berechnung (sehr angenähert) für den Sonnen-Auf und Untergang
      else $SendeIntervall = $SommerSendeIntervallNacht;

      if ($WinterBeginn != "deaktiviert") if ((date("z",$AktualisierungsStamp) < $SommerBeginnInTagen) OR (date("z",$AktualisierungsStamp) > $WinterBeginnInTagen)) $SendeIntervall = $WinterSendeIntervall;
      } // if zeitgesteuert




//<!-------  Messagebox -------->

// Messages erzeugen
if (($Config == "1") OR ($Aendern == "1"))
  { //der button zum konfigurieren wurde gedrückt 
  if ($Passwort != $BeeloggerShowPasswort) $Message = $SAs[25];
  }


for ($s=0; $s <= $MultiType; $s++) 
  { 
  if ($s > 0) $BEUTE = " beelogger".$MultiTypeName.$ServerMultiNumber."_".$s; 
  if ($SchwarmAlarmMessageArray[$s] != '-') $Message .= " ".$SAs[50]." ".$BEUTE.": ".$SchwarmAlarmMessageArray[$s];
  }

if ($Message == "")
  {
  if ($AktualisierungStampMessage > 1000) $Message = $SAs[51]." ".date($SAs[127],$AktualisierungStampMessage)." ".$SAs[52]." ".date("H:i",$AktualisierungStampMessage).$SAs[53]."."; // nur wenn überhaupt schon Daten vorhanden
  else $Message = $SAs[54]."!";
  



  $Mapname = "";
  if (strpos($beelogger,"beeloggerD") !== FALSE) $Mapname = "../Duo".intval(substr($beelogger,10,1))."/beelogger_map.php";
  elseif (strpos($beelogger,"beeloggerT") !== FALSE) $Mapname = "../Triple".intval(substr($beelogger,10,1))."/beelogger_map.php";
  elseif (strpos($beelogger,"beeloggerQ") !== FALSE) $Mapname = "../Quad".intval(substr($beelogger,10,1))."/beelogger_map.php";
  elseif (strpos($beelogger,"beeloggerP") !== FALSE) $Mapname = "../Penta".intval(substr($beelogger,10,1))."/beelogger_map.php";
    elseif (strpos($beelogger,"beeloggerH") !== FALSE) $Mapname = "../Hexa".intval(substr($beelogger,10,1))."/beelogger_map.php";
  elseif (strpos($beelogger,"beeloggerS") !== FALSE) $Mapname = "../Sept".intval(substr($beelogger,10,1))."/beelogger_map.php";
  if ($Mapname != "") include($Mapname);

  
  $Message .= " ".$SAs[55].":";
  if ($IntervallSendeSteuerung != "zeitgesteuert" AND $IntervallSendeSteuerung != "solarspannungsgesteuert" AND $IntervallSendeSteuerung != "lichtgesteuert") $SendeIntervall = 15; //keine Serversteuerung
  $Message .= " ".$SendeIntervall." ".$SAs[61];

  if ($IntervallSendeSteuerung == "zeitgesteuert")
    { 
    $Message .= " (";
    if (strpos($beeloggerSketchID,"EE") !== FALSE) $Message .= "EE-";
    $Message .= $SAs[56].")";
    }
  elseif ($IntervallSendeSteuerung == "solarspannungsgesteuert") $Message .= " (".$SAs[57].")";
  elseif ($IntervallSendeSteuerung == "lichtgesteuert") $Message .= " (".$SAs[58].")";
  else 
    {
    $Message .= " (".$SAs[59].")";
    }


  
  
  
  if (strpos($beeloggerSketchID,"EE") !== FALSE)
    {
    $Message .=" / ".$SAs[62].": ";

    function SendeZeit($Zeitraum) //Zeitraum in Stunden
      {
      global $AktualisierungStampMessage,$SendeIntervall; 

      if ($Zeitraum == 24) return "6:".date("i",$AktualisierungStampMessage); //immer um 6 Uhr

      $JetztStunde = date("H",$AktualisierungStampMessage);
      $StartStunde = $JetztStunde;
      $DannStunde = $JetztStunde+$Zeitraum;
      $LaufStamp = $AktualisierungStampMessage;
      while ($JetztStunde < $DannStunde) 
        {
        $LaufStamp += $SendeIntervall*60; 
        $JetztStunde = date("H",$LaufStamp);
        if ($JetztStunde < $StartStunde) $JetztStunde += 24; //24 Stundenumsprung
        }     
      return date("H:i",$LaufStamp);
      }


    if ($EESendeIntervall == "A" AND $SendeIntervall <= 30) $MessageTime .= SendeZeit(1);
    if ($EESendeIntervall == "A" AND $SendeIntervall > 30) $MessageTime .= SendeZeit(4);
    if ($EESendeIntervall == "B" AND $SendeIntervall <= 30) $MessageTime .= SendeZeit(2);
    if ($EESendeIntervall == "B" AND $SendeIntervall > 30) $MessageTime .= SendeZeit(8);
    if ($EESendeIntervall == "C" AND $SendeIntervall <= 30) $MessageTime .= SendeZeit(4);
    if ($EESendeIntervall == "C" AND $SendeIntervall > 30) $MessageTime .=SendeZeit(16);
    if ($EESendeIntervall == "D" AND $SendeIntervall <= 30) $MessageTime .=SendeZeit(8);
    if ($EESendeIntervall == "D" AND $SendeIntervall > 30) $MessageTime .=SendeZeit(24);
    
    if (date("H",$AktualisierungStampMessage) < 6 AND date("H",$MessageTime) >= 6)
      { //Achtung: selbständige Sonderaussendung ab sechster Stunde
      $StartZeit = $AktualisierungStampMessage;
      $Endzeit = date("H",$StartZeit);
      while ($Endzeit < 6) 
        {
        $StartZeit += $SendeIntervall*60;   
        $Endzeit = date("H",$StartZeit);
        }
      $MessageTime = date("H:i",($StartZeit+$SendeIntervall*60)); 
      }
    
    $Message .= $MessageTime.$SAs[53];
    } //if (strpos($beeloggerSketchID,"EE") !== FALSE)
  elseif ($IntervallSendeSteuerung == "zeitgesteuert" OR $IntervallSendeSteuerung == "solarspannungsgesteuert" OR $IntervallSendeSteuerung == "lichtgesteuert")
    {
    $Message .=" / ".$SAs[62].": ";
    $Message .= date("H:i",($AktualisierungStampMessage + ($SendeIntervall*60))).$SAs[53];
    }

  } //if ($Message == "")



echo "\n".'<div id="Messagebox">'.$Message.'</div>';



//------ die Sensoren -------->

echo '<div id="Sensorbuttons">';
// Checkboxen für SensorKurven ---------------------

$Klammern = array("[", "]");
$GewichtsEinheit = str_replace($Klammern,"",$Sensoren[5*5+4]);

for ($s = 0; $s < ($HelpAnzahlSensoren); $s++)
  {
  if ($HelpArraySensoren[5*$s] == "" AND $s < $HelpAnzahlSensoren)
    { //falls Sensor keinen Namen hat
    echo '<div id="ck-button'.$s.'"><label>';
    echo '<input type="checkbox" id="cb'.$s.'" ';
    echo 'onClick="change(this)"><span></span></label></div>'."\n";
    } // if $HelpArraySensoren
  else
    { //Sensor ist definiert
    if ($UseIcon[$s] == "true")
      { 
      echo "\n".'<div id="ck-button'.$s.'"><input id="cb'.$s.'" type="checkbox" onClick="change(this)">
         <label id="cbl'.$s.'" for="cb'.$s.'"></label></div>';
      }
    else
      {
    echo "\n".'<div id="ck-button'.$s.'"><label>';
      echo '<input type="checkbox" id="cb'.$s.'" onClick="change(this)"><span>';
      echo "<b>".$HelpArraySensoren[$s*5].'</b></span></label></div>';
      }      
    } // else
  } // for



  echo "\n".'<div id="ck-button'.$s.'"><label>';
  echo '<input type="checkbox" id="cb'.$s.'" onClick="change(this)"><span>';
  echo "<b>".$SAs[64]."</b></span></label></div>"."\n";
  $s++;


// Value in der Box ist Sensor Nummer in der Konfiguration
//  String in der Box aus beelogger_ini 
 echo "\n".'<div id="Nummer_Box" title="Tageswertanwahl">'."\n";
  echo '<select id="tagwertbox" onchange="tageswertanwahl()">';
  if ($GewichtsEinheit == "g") 
  {
  echo'
  <option value="1000.0">1000g</option>
  <option value="2000.0">2000g</option>
  <option value="3000.0">3000g</option>
  <option value="4000.0">4000g</option>
  <option value="5000.0">5000g</option>
  <option value="10000.0">10000g</option>
  <option value="20000.0">20000g</option> ';
  }
  else 
  { 
  echo'
  <option value="1.0">1 kg</option>
  <option value="2.0">2 kg</option>
  <option value="3.0">3 kg</option>
  <option value="4.0">4 kg</option>
  <option value="5.0">5 kg</option>
  <option value="10.0">10kg</option>
  <option value="20.0">20kg</option>';
  }
  if ($TageswertAnzeige == "true") $TageswertAnzeige = 5; //JJ-Abwärtskomp
  for ($t=0; $t < $HelpAnzahlSensoren; $t++) 
    { 
    if ($TageswertOptionArray[$t] == "true" AND $t != 5) 
        {
        echo "\n".'  <option value="'.($t+1).'" style="color:'.$HelpArraySensoren[$t*5+1].';"';
        if ($t == $TageswertAnzeige) echo " selected";
        echo'>'.$HelpArraySensoren[$t*5].'</option>';
        }
    }
  
echo "\n</select>\n";
echo "</div>\n";

//Wert für Tagwertbox berechnen
if ($TageswertAnzeige == '5' OR $TageswertAnzeige == 'false' OR $TageswertAnzeige == '') $TagwertboxWert = 2;
else 
  {
  $TagwertboxWert = 7; 
  for ($tw=0; $tw < $TageswertAnzeige; $tw++) 
    { 
    if ($TageswertOptionArray[$tw] == "true" AND $tw !=5) $TagwertboxWert ++;
    }  
  }

echo "\n".'<div id="ck-button'.$s.'"><label>';
echo '<input type="checkbox" id="cb'.$s.'" onClick="change(this)"><span>';
echo "<b>".$SAs[65]."</b></span></label>";
echo "</div>"."\n";
  $s++;

if ($MultiType <= 1)
  { //nur für "normale" beelogger
  if ($BeutenLeergewicht > 0)
    { 
    echo '<div id="ck-button99"><label>';
      echo '<input type="checkbox" id="99"><span>';
      if (($Honigraeume[0]+$Honigraeume[2]) > 0) 
        {
        echo "<b style='vertical-align:top'> &nbsp;".$SAs[66].":&nbsp;";
        
        echo round($LetztesGewicht-$BeutenLeergewicht-($Honigraeume[0]*$Honigraeume[1])-($Honigraeume[2]*$Honigraeume[3])-($BeutenUtils[0][0]*$BeutenUtils[0][1])-($BeutenUtils[1][0]*$BeutenUtils[1][1])-($BeutenUtils[2][0]*$BeutenUtils[2][1]),2).$GewichtsEinheit."</b>";

        echo "&nbsp;<img src='../beelogger_icons/Beute.png' width='15' height='15' title='Beutengewicht = ".$BeutenLeergewicht.$GewichtsEinheit."'>";

        for ($i=0; $i < $Honigraeume[0]; $i++) 
          {
          if ($Honigraeume[1] >10) $ShowHonigraum = $Honigraeume[1]/1000;// noch in Gramm
          else $ShowHonigraum =  $Honigraeume[1];
          echo "&nbsp;<img src='../beelogger_icons/honigraum.png' width='15' height='".($ShowHonigraum*2)."' title='Honigraumgewicht = ".$Honigraeume[1].$GewichtsEinheit."'>";
          }
        for ($j=0; $j < $Honigraeume[2]; $j++) 
          {
          if ($Honigraeume[3] >10) $ShowHonigraum = $Honigraeume[3]/1000;// noch in Gramm 
          else $ShowHonigraum =  $Honigraeume[3];
          echo "&nbsp;<img src='../beelogger_icons/honigraum.png' width='15' height='".($ShowHonigraum*2)."' title='Honigraumgewicht = ".$Honigraeume[3].$GewichtsEinheit."'>";
          }
          if ($BeutenUtils[0][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Varoaeinschub.png' width='15' height='15' title='Gewicht der Varoaschublade = ".$BeutenUtils[0][1].$GewichtsEinheit."'>";

          if ($BeutenUtils[1][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Absperrgitter.png' width='15' height='15' title='Gewicht des Absperrgitters = ".$BeutenUtils[1][1].$GewichtsEinheit."'>";

          if ($BeutenUtils[2][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Futterzarge.png' width='15' height='15' title='Gewicht der Futterzarge = ".$BeutenUtils[2][1].$GewichtsEinheit."'>";
        }
      else 
        {
        echo "<b style='vertical-align:top'>&nbsp;".$SAs[67].":";
        echo round($LetztesGewicht-$BeutenLeergewicht-($Honigraeume[0]*$Honigraeume[1])-($Honigraeume[2]*$Honigraeume[3])-($BeutenUtils[0][0]*$BeutenUtils[0][1])-($BeutenUtils[1][0]*$BeutenUtils[1][1])-($BeutenUtils[2][0]*$BeutenUtils[2][1]),2).$GewichtsEinheit."</b>";
         if ($BeutenUtils[0][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Varoaeinschub.png' width='15' height='15' title='Gewicht der Varoaschublade = ".$BeutenUtils[0][1].$GewichtsEinheit."'>";

          if ($BeutenUtils[1][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Absperrgitter.png' width='15' height='15' title='Gewicht des Absperrgitters = ".$BeutenUtils[1][1].$GewichtsEinheit."'>";

          if ($BeutenUtils[2][0] == 1) echo "&nbsp;<img src='../beelogger_icons/n_Futterzarge.png' width='15' height='15' title='Gewicht der Futterzarge = ".$BeutenUtils[2][1].$GewichtsEinheit."'>";
        }

      echo "</span></label></div>"."\n";
    }
  }
else
  { // Dummybutton um die Symbole auszurichten falls nur Symbole verwendet werden
  echo '<div id="ck-button999"><label>';
  echo '<input type="checkbox" id="999"><span>';
  echo "</span></label></div>"."\n";
  }




// <!-- ^^^^ die Sensoren -------->
echo'<div id="AlleAuswahlbutton"> <label><input type="checkbox" id="cb'.$s.'" onClick="change(this)"><span>'.$SAs[68].'</span></label></div>';


//!-- ^^^^ Navigation -------->
echo '<div id="NavigationAnzeige">
  <span title="Anzeigebereich auswählen:">&nbsp;&nbsp;</span>';
  if ($Csvdatei == "week.csv") echo '<input type="button" style="background-color:#FAFAFA;"value="1 h" onclick="zoom(86400/24);"/>';
  echo'
  <input type="button" style="background-color:#F2F2F2;" value="2 h" onclick="zoom(86400/12);">
  <input type="button" style="background-color:#E6E6E6;" value="4 h" onclick="zoom(86400/6);">
  <input type="button" style="background-color:#D8D8D8;" value="8 h" onclick="zoom(86400/3);">
  <input type="button" style="background-color:#BDBDBD;" value="'.$SAs[69].'" onclick="zoom(86400);">';
   if ($Csvdatei == "week.csv") echo '<input type="button" style="background-color:#A4A4A4;"value="1/2 Woche" onclick="zoom(3.5 * 86400);">';
   if ($Csvdatei == "month.csv" OR $Csvdatei == "beelogger.csv") echo '<input type="button" style="background-color:#BDBDBD;"value="'.$SAs[70].'" onclick="zoom(7 * 86400);">';
  if ($Csvdatei == "month.csv" OR $Csvdatei == "beelogger.csv") echo '<input type="button" style="background-color:#A4A4A4;" value="14 '.$SAs[71].'" onclick="zoom(14 * 86400);">';
  if ($Csvdatei == "beelogger.csv") echo '<input type="button" style="background-color:#848484;" value="'.$SAs[72].'" onclick="zoom(30 * 86400);">';
echo'
  <input type="button" style="background-color:#6E6E6E;" value="Reset Zoom" onclick="reset_Zoom();">
  <span title="'.$SAs[73].':">&nbsp;&nbsp;Scroll:&nbsp;</span>
  <input type="button" style="background-color:#E6E6E6;" value="|<" onclick="panend(-1);" title="'.$SAs[74].'">
  <input type="button" style="background-color:#F2F2F2;" value="<<" onclick="pan(-1);">
  <input type="button" style="background-color:#FAFAFA;" value="<"  onclick="pan(-0.5);"  title="'.$SAs[75].'">
  <input type="button" style="background-color:#FAFAFA;" value=">"  onclick="pan(0.5);"   title="'.$SAs[76].'">
  <input type="button" style="background-color:#F2F2F2;" value=">>" onclick="pan(1);">
  <input type="button" style="background-color:#E6E6E6;" value=">|" onclick="panend(1);"  title="'.$SAs[77].'">&nbsp;&nbsp;
  <input id ="123" type="button" value="'.$SAs[81].'" onclick="show_points()" title="'.$SAs[78].'">&nbsp;
  <input id ="124" type="button" value="'.$SAs[82].'" onclick="set_legend()" title="'.$SAs[79].'">                     
  </div>';

echo'<div id="beelogger_grafik"> </div>

</div>';  //<!-- div Sensorbuttons -->


//<!------- FORM Passwortabfrage zur Konfig -------->
echo'<div id="DatenAreaKonfig"> 
<form action="beelogger_show.php" method="post" id="KonfigPasswortEingabeForm" style="display:none;">
'.$SAs[80].': 
<input type="password" name = "passwort" size ="5">
&nbsp;&nbsp;
<button type="submit" value="1" name="config"><b>'.$SAs[83].'</b></button>
<button type="reset" onclick="AktionAbbruch()" style="background-color: #e7846f;" name="cancelAnmerkungBearbeiten" value="0"><b>'.$SAs[84].'</b></button>
</form>
</div> ';
//<!------- Ende FORM Passwortabfrage zur Konfig ------>



//<!------- FORM für Neue Anmerkung -------->
echo'<div id="DatenArea"> 
  <form action="beelogger_show.php" method="post" id="AnmkForm" style="display:none;">
  <span title="'.$SAs[85].'."><b>'.$SAs[86].':</b></span>
  <input id="Sensor_anno" name="sensor" type="text" style="width:103px;" value="Sensor_anno" title="'.$SAs[87].'"/>
  <input id="Datum_anno" type="text" style="width:115px;" value="" title="'.$SAs[88].'"/>
  <input id="Termin_anno" name="termin" type="hidden" />
  
  <input id="K_anno" name="kurztext" type="text" maxlength="10" size="6" value="" title="'.$SAs[89].'"/>';

 echo'<select name="note_icon">';
 echo'<option value="" selected>'.$SAs[135].'</option>';
 

 $dir = "../beelogger_icons";
 $files = scandir($dir);
  foreach ($files as $PNGs) 
    {
    if (substr($PNGs,0,2) == "n_")
      {
      echo '<option value="../beelogger_icons/'.$PNGs.'">'.$SAs[136].': ';
      if ($Sprache == "1") echo substr(substr($PNGs,2),0,-4);
      else 
        {
          //echo $IconBezeichner[0][$Sprache];
        $i=0;
        while ($i < sizeof($IconBezeichner))
          {
          $Gefunden = false; 
          if ($PNGs == $IconBezeichner[$i][1])
            {
            echo $IconBezeichner[$i][$Sprache];
            $Gefunden = true;
            break; 
            }
          $i++;  
          }
        if ($Gefunden == false) echo substr(substr($PNGs,2),0,-4);   
         }
      echo '</option>';   
      }
    }
    echo'</select>&nbsp;&nbsp;';


  echo'<input id="Edit_anno" name="langtext" type="text" value="" title="'.$SAs[90].'"/>
  &nbsp;&nbsp;';

if ($Passwort == $BeeloggerShowPasswort){ // Es wurde bereits ein korrektes Passwort in der Session eingegeben
  echo "<input type='hidden' name = 'passwort' value = '".$Passwort."'>";
}
else {
  echo $SAs[91].': <input type="password" name = "passwort" size ="5">';
}
echo'
<input type="hidden" name = "csvdatei" value = "'.$Csvdatei.'">
&nbsp;&nbsp;
<button type="submit" name="erzeugen" value="1" title="'.$SAs[92].'"><b>'.$SAs[93].'</b></button>
&nbsp;&nbsp;
<button type="reset" onclick="AktionAbbruch()" style="background-color: #e7846f;" name="cancelAnmerkungErzeugen" value="0"><b>'.$SAs[84].'</b></button>
</form>
</div>';

//<!------- Ende FORM für Neue Anmerkung -------->



//<!------- FORM zum Löschen eines Wertes -------->
echo'<div id="DatenAreaLoeschWert"> 
  <form action="beelogger_show.php" method="post" id="WertloeschenForm" style="display:none;">';
  if (($Csvdatei == "beelogger.csv") OR ($Csvdatei == "month.csv") OR ($Csvdatei == "week.csv")){
  echo'  
  <span title="'.$SAs[94].'."><b>'.$SAs[95].':</b></span>
  <input id="Sensor_annoL" name="sensor" type="text" style="width:103px;" value="" title="'.$SAs[87].'">
  <input id="Datum_annoL" type="text" style="width:115px;" value="" title="'.$SAs[88].'">
  '.$SAs[97].'
  <input id="WertL" name="neuwert" type="text" style="width:50px;" value="" title="'.$SAs[96].'">
  <input id="Termin_annoL" name="termin" type="hidden">
  <b>&nbsp;&nbsp;</b>';

if ($Passwort == $BeeloggerShowPasswort) 
  { // Es wurde bereits ein korrektes Passwort in der Session eingegeben
  echo "<input type='hidden' name = 'passwort' value = '".$Passwort."'>";
  }
else 
  {
  echo $SAs[91].': <input type="password" name = "passwort" size ="5" required>';
  }
echo "<input type='hidden' name = 'csvdatei' value = '".$Csvdatei."'>"; //falls gewechselt wurde - mitsenden

echo'
&nbsp;&nbsp;
<button type="submit" name="wertloeschen" value="1" title="'.$SAs[98].'!"><b>'.$SAs[99].'</b></button>
<button type="reset" onclick="AktionAbbruch()" name="nichts" style="background-color: #e7846f;" value=""><b>'.$SAs[84].'</b></button>';
} // falls beelogger.csv oder month.csv geladen und nicht Archivdatei
else echo '<b><font size="3">Diese Funktion ist nicht für Archivdateien vorgesehen!</b>';

echo'
</form>
</div>'; 
//<!------- Ende FORM zum Löschen eines Wertes -------->



//Formular zum Ändern einer Anmerkung wurde abgeschickt---------------------
if ($Aendern == "1"){   
 if ($Passwort == $BeeloggerShowPasswort){
echo'
 <div id="DatenAreaAnmerkAend">
 <form action="beelogger_show.php" method="post" id="FormAendAnmk"  style="display:block;">
 <b>'.$SAs[100].': </b>';

  $TerminAnzahl = 1000;  //Anzahl, wieviele Termine ausgelesen werden
  $what = trim($narray[$Termin]);
  $nx = explode( ",", $what );
  $ns = sizeof($nx);
  $note = explode("&",$nx[2]);
  echo '<select name="sensor">';
   
  if   (is_numeric($nx[0])) $Sensor = $Sensoren[$nx[0]*5];
  else $Sensor = $nx[0];  //alten Sensor gelesen
   
  for ($s = 0; $s < $AnzahlSensoren; $s ++)
    {
    echo "<option value='".$Sensor."%".$Sensoren[$s*5]."'";
    if ($Sensor == $Sensoren[$s*5]) echo " selected";
    echo ">".$Sensoren[$s*5]."</option>\n";
    }
  echo "</select>";

// DATUM
 if ($NummerLetzteZeile <= $TerminAnzahl) $TerminAnzahl = $NummerLetzteZeile;
  echo '<select name="termin">';

   for ($u = $NummerLetzteZeile ; $u >  ($NummerLetzteZeile-$TerminAnzahl) ; $u--){                
     $what = trim($array[$u]);    
     $x = explode( ",", $what );
     $s = sizeof($x);
     echo "<option value=".$nx[$ns-1]."%".$x[$s-1];
     if ($nx[$ns-1] == $x[$s-1]) echo " selected ";
     echo ">".$x[0]."</option>";//timestamp reicht aus
   } //for $u 

  echo "</select>&nbsp;&nbsp;";

// KÜRZEL  
    if (strpos($note[0],".png") > 0)
    echo $SAs[101].": <input type='text' name = 'kurztext' value ='?' size ='6' maxlength ='10'>";
    else echo $SAs[101].": <input type='text' name = 'kurztext' value ='".$note[0]."' size ='6' maxlength ='10'>";

    echo'<select name="note_icon">';   
    $dir = "../beelogger_icons";
    $files = scandir($dir);
    echo "<option value=''>".$SAs[135]."</option>";
    foreach ($files as $PNGs) 
      {
    
      if (substr($PNGs,0,2) == "n_")
        {
        $ShowIcon = '../beelogger_icons/'.$PNGs;  
        echo '<option value="../beelogger_icons/'.$PNGs.'"';
        if ($note[0] == $ShowIcon) echo " selected ";
        echo '>'.$SAs[136].': ';
        if ($Sprache == "1") echo substr(substr($PNGs,2),0,-4);
        else 
          {
            //echo $IconBezeichner[0][$Sprache];
          $i=0;
          while ($i < sizeof($IconBezeichner))
            {
            $Gefunden = false; 
            if ($PNGs == $IconBezeichner[$i][1])
              {
              echo $IconBezeichner[$i][$Sprache];
              $Gefunden = true;
              break; 
              }
            $i++;  
            }
          if ($Gefunden == false) echo substr(substr($PNGs,2),0,-4);   
           }
        echo '</option>';   
        }


      }
    echo'</select>&nbsp;&nbsp;'; 

  echo 'Beschreibung: ';
  echo "<input type='text' name = 'langtext' value ='".$note[1]."'size = '20'></td>";
  echo "<input type = 'hidden' name = 'passwort' value ='".$Passwort."''>"; //Passwort auch übertragen
  echo "<input type='hidden' name = 'csvdatei' value = '".$Csvdatei."'>"; //falls gewechselt wurde - mitsenden
  echo '<button type="submit" name="speichern" id="AendAnmkBtn" value="1"><b>'.$SAs[102].'</b></button>';
    echo'<button type="reset" onclick="AktionAbbruch()" style="background-color: #e7846f;" name="cancelAnmerkungÄndern" value="0"><b>'.$SAs[84].'</b></button>';
  echo'</form></div>';
} //if $passwort                      
}//if aendern = 1
// Ende Verarbeiten Formular ändern -----------------------------------------------



//<!------- FORM zum Ändern/Löschen einer Anmerkung -------->
echo'<div id="DatenAreaAendAnmrk">  
<form action="beelogger_show.php" method="post" id="LoeAenForm" style="display:none;"><b>';

if (file_exists('notes.csv')) {
  $input = "notes.csv";
  $narray = file($input);
  $ni = sizeof($narray);
}
else $ni = 0;

if ($ni > 0){  //nur wenn mind. eine Anmerkung existiert
 echo ' <select name="termin">';
  for ($o = $ni ; $o >= 0  ; $o--){
    $what = trim($narray[$o]);
    $x = explode( ",", $what );
    $s = sizeof($x);
    $note = explode("&",$x[$s-2],2);

    if ($s > 1) 
      {
      echo "<option value=".$o.">".$SAs[87].": ";
      if   (is_numeric($x[0])) echo $Sensoren[$x[0]*5];
      else echo $x[0];

      echo " ".$x[1]." ";
      if (strpos($note[0],".png") > 0) 
        {
        echo $SAs[136].": ";
        if ($Sprache == "1") echo substr(substr($note[0],21),0,-4);
        else 
          {

          $i=0;
          while ($i < sizeof($IconBezeichner))
            {
            $Gefunden = false; 
            if (substr($note[0],19) == $IconBezeichner[$i][1])
              {
              echo $IconBezeichner[$i][$Sprache];
              $Gefunden = true;
              break; 
              }
            $i++;  
            }
          if ($Gefunden == false) echo substr(substr($note[0],21),0,-4); 
           }
          
        }
      else echo $SAs[101].": ".$note[0];

      if ($note[1] != "" AND $note[1] != $SAs[26]) echo " ".$SAs[103].": ".$note[1]."</option>\n";
      }
  } //for $o

 echo'</select>';
 if ($Passwort == $BeeloggerShowPasswort) { // Es wurde bereits ein korrektes Passwort in der Session eingegeben
 echo "<input type='hidden' name = 'passwort' value = '".$Passwort."'>";
 }
 else {
   echo "\n".'Passwort: <input type="password" name = "passwort" size ="5">&nbsp;&nbsp;';
 }
 echo "<input type='hidden' name = 'csvdatei' value = '".$Csvdatei."'>"; //falls gewechselt wurde - mitsenden
 echo '<button type="submit" name="aendern"  value="1"><b>'.$SAs[99].'</b></button>&nbsp;&nbsp;';
 echo '<button type="submit" name="loeschen"  value="1"><b>'.$SAs[104].'</b></button>&nbsp;&nbsp;';
 echo'<button type="reset" onclick="AktionAbbruch()" style="background-color: #e7846f;" name="cancelAnmerkungBearbeiten" value="0"><b>'.$SAs[84].'</b></button>';
} //if $ni >0
// ENDE FORM Anmerkungen löschen und ändern----------------------------------
?>
</form></div>





<?php
// Konfig ändern --------------------------------------------
if ($Config == "1")
  { //der button zum konfigurieren wurde gedrückt 
  if ($Passwort == $BeeloggerShowPasswort)
    {
        if ($LogAlert)
      {
      echo '<script language="javascript">';
      echo 'alert("'.$SAs[105].': _ ! * ( ) ; : @ & = + $ , / ? % # [ ] ")';
      echo '</script>';
      } 
    include ("beelogger_config.php");

    } // if Passwort OK
  } //if config = 1
else //if config=1
  {
    if ($LogAlert)
      {
      echo '<script language="javascript">';
      echo 'alert("'.$SAs[105].': _ ! * ( ) ; : @ & = + $ , / ? % # [ ] ")';
      echo '</script>';
      }  

  if ($Passwort_Show_Counter >= 1) 
    {
    echo '<script language="javascript">';
    echo 'alert("'.$SAs[106].': ';
    for ($i=0; $i < ($Passwort_Show_Counter-1); $i++) 
      { 
      echo $AlertArray[$i].", ";
      }
    echo $AlertArray[$i];
    echo '")';
    echo '</script>';
    }

  } //else //if config=1
?>

<!-- Dygraphsbereich Start -->
      
<script type="text/javascript" charset="utf-8">



 // globale Variablen festlegen
var Werteloeschen = false;
var beelogger;  // die dygraph Objecte
var desired_range = null;   // Anzeige/Zoom Bereich für Dygraph
var move = 0;         // Anzahl der Schritte zum desired_range begrenzen
var SingleCheck = 0;    // Variable die anzeigt, wieviele Kurven angezeigt werden  
var data_d = [];      // neue Datenstruktur
var tag_setup = 0;      // Tageswerte berechnet ?
var summe_setup = 0;      // Summenwerte berechnet ?
var roll_period = <?php echo $RollPeriod; ?>;

var ann_count = 0;
var LoeschenButton = 0;   //Zustand des LoeschenButtons 0 = nicht gedrückt


var d_names = [<?php echo '"'.$SAs[107].'","'.$SAs[108].'","'.$SAs[109].'","'.$SAs[110].'","'.$SAs[111].'","'.$SAs[112].'","'.$SAs[113].'"'; ?>];
var m_names = [<?php echo '"'.$SAs[114].'","'.$SAs[115].'","'.$SAs[116].'","'.$SAs[117].'","'.$SAs[118].'","'.$SAs[119].'","'.$SAs[120].'","'.$SAs[121].'","'.$SAs[122].'","'.$SAs[123].'","'.$SAs[124].'","'.$SAs[125].'"'; ?>];     
var EinzelEinheiten = [<?php for ($e = 0; $e < $HelpAnzahlSensoren; $e++) echo "'".$HelpArraySensoren[$e*5+4]."',"; echo "'[".$GewichtsEinheit."]','[".$GewichtsEinheit."]','[".$GewichtsEinheit."]']";
?>;

<?php if ($PunktAnzeige == "true") echo "var points_show=0;";
else  echo "var points_show=1;"; ?>

var EinzelSensoren = [<?php for ($e = 0; $e < $HelpAnzahlSensoren; $e++) echo '"'.$HelpArraySensoren[$e*5].'",'; echo "'Tag+','Tag-','Summe'";?>];

<?php 
if ($Aendern == "1") //es soll eine Anmerkung geändert werden
  {
  echo "document.getElementById('WertBtn').disabled = true;";
  echo "document.getElementById('AnmerkungenBtn').disabled = true;";
  }
?>

function KonfigBtnPressed() {  // Konfiguration Button ohne Passwort gedrückt
         Werteloeschen = false;
  document.getElementById('WertBtn').disabled = true;
  document.getElementById('AnmerkungenBtn').disabled = true;

  document.getElementById('KonfigPasswortEingabeForm').style.display = "block";//FORM ZEIGEN
  document.getElementById('LoeAenForm').style.display = "none"; //FORM Löschen Ändern Anmerkung AUS
  document.getElementById('AnmkForm').style.display = "none"; //FORM Neue Anmerkung AUS
  document.getElementById('WertloeschenForm').style.display = "none"; //FORM AUS
} 

function AnmerkungBtnPressed() { // Löschen bzw Ändern von Anmerkungen Button gedrückt 
  document.getElementById('WertBtn').disabled = true;
  document.getElementById('AnmerkungenBtn').disabled = true;

  document.getElementById('LoeAenForm').style.display = "block"; //FORM Löschen ändern Anmerkung AN
  document.getElementById('AnmkForm').style.display = "none"; //FORM AUS
  document.getElementById('WertloeschenForm').style.display = "none"; //FORM AUS
}

function WertBtnPressed() { // Button Wert Löschen/ändern gedrückt
     Werteloeschen = true;
   document.getElementById('WertBtn').disabled = true;
   document.getElementById('AnmerkungenBtn').disabled = true;

   document.getElementById('WertloeschenForm').style.display = "block"; //FORM Löschen/ändern für Werte ZEIGEN
   document.getElementById('LoeAenForm').style.display = "none";//FORM AUS
   document.getElementById('AnmkForm').style.display = "none";//FORM AUS
}

function AktionAbbruch() { // Abruchbutton in Form gedrückt (Default herstellen)
       Werteloeschen = false;
  document.getElementById("WertBtn").disabled = false;
  <?php   
  if ($Passwort != $BeeloggerShowPasswort) echo "document.getElementById('KonfigBtn').disabled = false;"; //Passwort
  if ($MerkeDassButtonDisabled != true) echo"document.getElementById('AnmerkungenBtn').disabled = false;"; 
  ?>
  document.getElementById('LoeAenForm').style.display = "none"; //FORM AUS
  <?php if ($Aendern == "1") echo "document.getElementById('FormAendAnmk').style.display = 'none';"; ?>
  document.getElementById('AnmkForm').style.display = "none";
  document.getElementById('WertloeschenForm').style.display = "none";
  document.getElementById('KonfigPasswortEingabeForm').style.display = 'none';
}

//Mouseover fuer Icon erzeugen 
<?php for ($i=0; $i < $HelpAnzahlSensoren ; $i++) { 
  if ($UseIcon[$i] == "true") echo'document.getElementById("cbl'.$i.'").setAttribute("title", "'.html_entity_decode($HelpArraySensoren[$i*5]).'");';
}
?>

<?php // nicht konfig-Mode, dann grafik
if (($Config != "1") OR ($Passwort != $BeeloggerShowPasswort)){ ?>
var beelogger = new Dygraph(document.getElementById("beelogger_grafik"),
  <?php 
  if (file_exists($Csvdatei) AND file_exists("beelogger.csv")) echo "'".$Csvdatei."'";
  else echo '"2017-03-01,,,,,,,,,,,,\n" + "2017-03-06 10:00,30,,,,,,30,,,,,\n"+ "2017-03-06 22:00,,20,,,,,,,,,,\n" + "2017-03-07 10:0,11,,,,,,11,,,,,\n" + "2017-03-09,10,23,,,,,10,,,,,\n"+ "2017-03-11,10,23,10,,,,,,,,,\n" + "2017-03-13,17,17,,,,,,,,,,\n" + "2017-03-14,,,12,19,19,,,,,,,\n" + "2017-03-16,,,,,23,,,,,,,\n" + "2017-03-18,,,16,11,23,,,,,,,\n" + "2017-03-20,,,19,10,19,,,,,,,\n" + "2017-03-22,,,,,,19,,19,,,,\n"+ "2017-03-24,,,,,,23,,13,,,,\n" + "2017-03-26,,,,16,,23,,11,,,,\n" +"2017-03-28,,,,19,,19,,10,,,,\n" + "2017-04-02,,,,,,,,,,,,\n"'; ?>,
      //Optionen
      {
        //Labels
        labels: [ 'Datum',<?php for ($s = 0; $s < $HelpAnzahlSensoren-1; $s++){ echo '"'.html_entity_decode($HelpArraySensoren[$s*5]).'",'; }
          echo "'".html_entity_decode($HelpArraySensoren[$s*5])."'";?>,'Tag+','Tag-','Summe'],
         colors: [<?php for ($s = 0; $s < $HelpAnzahlSensoren-1; $s++){ echo "'".$HelpArraySensoren[$s*5+1]."',"; }
          echo "'".$HelpArraySensoren[$s*5+1]."'";?>,'#00ff01','#ff0001','orange'],
        visibility: [<?php for ($s = 0; $s < $HelpAnzahlSensoren; $s++){ echo $HelpArraySensoren[$s*5+2].","; }?>false,false,false],
    <?php echo "xlabel: '".$Label[0]."'";?>,ylabel:'',y2label:'',
        
    series : {
       <?php for ($s = 0; $s < $HelpAnzahlSensoren; $s++){
       echo '"'.html_entity_decode($HelpArraySensoren[$s*5]).'": { axis: '.$HelpArraySensoren[$s*5+3]." },"."\n"; } ?> 'Tag+': {axis:'y2' ,drawPoints:false,pointSize:1,fillGraph:true,fillAlpha:0.5 },
      'Tag-': {axis:'y2' ,drawPoints:false,pointSize:1,fillGraph:true,fillAlpha:0.5 },
      'Summe': { axis: 'y2',drawPoints:false,pointSize:1,fillGraph:true,fillAlpha:0.2 },            },
  axes: {
    x:{axisLabelFormatter:function(d,gran){
        var curr_day=d_names[d.getDay()];
        var curr_month=m_names[d.getMonth()];
    try{var w=beelogger.xAxisRange();}
    catch(ignore){var w=[0,1];}
    if((w[1]-w[0])<(86400000*3)){
        var Minuten=d.getMinutes();
        Minuten=((Minuten<10)?"0"+Minuten:Minuten);
          return curr_day+" "+d.getDate()+"<?php echo $SAs[128]?>"+curr_month+"<?php echo $SAs[128]?>"+d.getHours()+":"+ Minuten+" ";
        }
        else{return curr_day+" "+d.getDate()+"<?php echo $SAs[128]?>"+curr_month;}
      },
      axisLabelWidth:160,pixelsPerLabel:110
    },
    y:{independentTicks:true,axisLabelWidth:60,pixelsPerLabel:30,valueRange:[null,null]},
    y2:{independentTicks:true,axisLabelWidth:60,pixelsPerLabel:30}},

    hideOverlayOnMouseOut: false,
    connectSeparatedPoints:true,strokeWidth:2,
    showRoller:true,rollPeriod:<?php echo $RollPeriod; ?>,
    drawPoints:<?php if ($PunktAnzeige == "aktiviert") echo "true,";
      else echo "false,"; ?>
    displayAnnotations: true,   // ohne Annotation
    highlightSeriesOpts:{strokeWidth:2,strokeBorderWidth:1,highlightCircleSize:4},

    drawCallback:function(g,is_initial){if(!move){setTimeout(function(){konfig_Axes();},10);}},
    zoomCallback:function(a,b,c){if(!move){berechne_sum();}},

    pointClickCallback: function(event, p) {
          // Checke, ob bereits an der Stelle eine Anmerkung besteht
    if (p.annotation) return;
    beelogger.updateOptions({rollPeriod:1});  // neu
    if (Werteloeschen == false) 
      {   
      document.getElementById('AnmkForm').style.display = "block"; //Form für neue Anmerkung "einblenden"
      document.getElementById('LoeAenForm').style.display = "none"; //Form loschen oder ändern einer Anmerkung ausblenden
      document.getElementById('KonfigPasswortEingabeForm').style.display = 'none';
      document.getElementById('WertBtn').disabled = true;
      document.getElementById('AnmerkungenBtn').disabled = false;
      var anns = beelogger.annotations();
      var ann_num = anns.length;

      if (ann_num == ann_count){ //letzte Anmerkung loschen - nur eine neue pro Speicherung erlaubt
        anns.splice(ann_num-1,1);  // 
      }
      var datum = new Date(p.xval);
      var dat_str = String(datum.getFullYear()) + "/" + String("0" + (datum.getMonth()+1)).slice(-2) + "/" + String('0'+datum.getDate()).slice(-2) + " " + String('0'+datum.getHours()).slice(-2) + ":" + String('0'+datum.getMinutes()).slice(-2) + ":" + String('0'+datum.getSeconds()).slice(-2);
      var ann={series:p.name,xval:p.xval,shortText:"? Kürzel",text:"<?php echo $SAs[126] ?>"};
      anns.push(ann);
      beelogger.setAnnotations(anns);
      document.getElementById('Sensor_anno').value = p.name; //Anmerkung Sensor
      document.getElementById('Datum_anno').value = dat_str; //Anmerkung Datum
      document.getElementById('Termin_anno').value = p.xval; //Anmerkung Zeitstempel
      document.getElementById('K_anno').value = "<?php echo $SAs[132] ?>"; //Anmerkung Kurztext
      document.getElementById('Edit_anno').value = "<?php echo $SAs[133] ?>"; //Anmerkung Beschreibung lang
      //Werteloeschen = true;
      } // if Werteloeschen
    else {   
      document.getElementById('WertloeschenForm').style.display = "block"; //Form Wertloschen einblenden
          var datum = new Date(p.xval);
          var dat_str = String(datum.getFullYear()) + "/" + String("0" + (datum.getMonth()+1)).slice(-2) + "/" + String('0'+datum.getDate()).slice(-2) + " " + String('0'+datum.getHours()).slice(-2) + ":" + String('0'+datum.getMinutes()).slice(-2) + ":" + String('0'+datum.getSeconds()).slice(-2);
          document.getElementById('Sensor_annoL').value = p.name; 
          document.getElementById('Datum_annoL').value = dat_str;
          document.getElementById('WertL').value = p.yval;
          document.getElementById('Termin_annoL').value = p.xval;
          Werteloeschen = true;
        } //else
    },
  showRangeSelector:true,rangeSelectorHeight:30,
  legend:<?php if ($Legende == "immer") echo "'always'";
    else if ($Legende == "folgend") echo "'follow'";
        else echo "'never'"; ?>,labelsSeparateLines:true,labelsShowZeroValues:true,
  pointSize:3,
<?php  
// Mobilgeräte
if (preg_match("/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine
|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|
panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus
|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i", $_SERVER['HTTP_USER_AGENT'])) 
{ echo "interactionModel:{}";} 
else {echo "interactionModel: Dygraph.defaultInteractionModel";}
?>
});//new Ende

//*****************
beelogger.ready(function() {


my_onresize();

//Auswahlboxen Tageswert vorbelegen
<?php if ($MultiType < 2) echo 'document.getElementById("tagwertbox").options['.$TagwertboxWert.'].selected = true;'; ?>

//document.getElementById("tagwertbox").selectedIndex = "2"; 
// Tag und Summe aus
beelogger.setVisibility(<?php echo $HelpAnzahlSensoren; ?>,false); // tag
beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+1; ?>,false); // tag
beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+2; ?>,false); // summe
document.getElementById('cb<?php echo $HelpAnzahlSensoren; ?>').checked = false;
document.getElementById('cb<?php echo $HelpAnzahlSensoren+1; ?>').checked = false;

// erstmaligen Laden und Taste "F5"
var is_visib = beelogger.visibility();
<?php 
    for ($i=0; $i < $HelpAnzahlSensoren; $i++){
    echo "\n".'document.getElementById("cb'.$i.'").checked = is_visib['.$i.'];';
    }
?>

setup_Axes();
setTimeout(function(){pan(-1);}, 100); // force redraw
show_points();
// Anmerkungen einlesen

//Datei notes.csv nach Anmerkungen durchsuchen und im Array für die Darstellung aufbereiten-----
<?php 
  $input = "notes.csv";
  $narray = file($input);
  $ni = sizeof($narray);

$b=0;
for ($a = 0 ; $a < $ni; $a++) {

$what = trim($narray[$a]);    
      $x = explode( ",", $what );
      $s = sizeof($x);
$Sensorx=$x[0];
if (is_numeric($Sensorx)) $Sensorx = html_entity_decode($HelpArraySensoren[$Sensorx*5]);

$Anmerkung = html_entity_decode($x[$s-2]);
        $AktualisierungStamp=$x[$s-1];
        $DatumAnmerkung = date("Y/m/d H:i:s",$AktualisierungStamp);   //Zeitformat für Dygraph umgestellt !$
$anmerkungen[$b] = $Sensorx;       // Sensor im Array merken
$b++;
$anmerkungen[$b] = $DatumAnmerkung;//Datumsstempel im Array merken 
$b++;
$y =explode("&",$Anmerkung);
$anmerkungen[$b] = $y[0];          //Anmerkung kurz im Array merken
$b++;
$anmerkungen[$b] = $y[1];         //Anmerkung lang im Array merken
$b++;
                             }   //ende for-schleiffe
                                               
// Ende Anmerkungen Array erstellen 

echo "beelogger.setAnnotations([  ";

  for ($w = 0; $w < $b; $w++)
    {
    echo "\n{";
    echo "series: '".$anmerkungen[$w]."',";
    $w++;
    echo 'xval: Date.parse(\''.$anmerkungen[$w].'\'),';
    $w++;
    if (!strpos($anmerkungen[$w],".png") === false) echo 'icon: "'.$anmerkungen[$w].'",
        width: 30,
        height: 30';

    else echo 'shortText: "'.$anmerkungen[$w].'",
width: '.(6+8*strlen($anmerkungen[$w]));
    $w++;
    echo ',text: "'.$anmerkungen[$w].'",';
    echo "},";
    }//for $w
   
  echo"]);";
  
  ?>

var anns = beelogger.annotations();
ann_count = anns.length+1;

setTimeout(function(){pan(-1);}, 100); // force redraw
<?php if ($TageswertAnzeige != "false" AND $MultiType < 2) echo "setTimeout(function(){tageswertanwahl();},100);"; ?>
}); // .ready ende

// show_points: Datenpunkte zeigen
function show_points(){
if(points_show){
  beelogger.updateOptions({drawPoints:false});
  points_show=0;
  document.getElementById("123").style.backgroundColor = "lightgrey";
}
else{
  beelogger.updateOptions({drawPoints:true});
  points_show=1;
  document.getElementById("123").style.backgroundColor = "green";
}
animate();    
}

// set_legend: Legende Varianten
<?php if ($Legende == "immer") echo "var legend_state = 1;";
    else if ($Legende == "folgend") echo "var legend_state = 2;";
        else echo "var legend_state = 0;"; ?>
function set_legend(){
if(legend_state ==2){
  document.getElementById("124").style.backgroundColor = "lightgrey";
  beelogger.updateOptions({legend:'never'});
  legend_state=0;
}
else if(legend_state ==1){
  document.getElementById("124").style.backgroundColor = "grey";
  beelogger.updateOptions({legend:'follow'});
  legend_state=2;
}
else{
  document.getElementById("124").style.backgroundColor = "green";
  beelogger.updateOptions({legend:'always'});
  legend_state=1;
}
animate();    
}

// my_onresize: resize Event Grafik an Fenster anpassen
function my_onresize(){
var w = window.innerWidth;  // das Fenster an sich 
var h = window.innerHeight;
h -=250;  // Abstand zu Buttons etc
if(w<1250){h-=50;} // Wenn buttons nicht mehr in eine Reihe passen
if(w<1100){h-=50;}
if(w<675){h-=50;}
if (h <350){h =350;}
beelogger.resize(w-50,h);
}

// ** Label fuer Achsen festlegen, valueRange
function setup_Axes(){

var MainAxis=1;
var curve,n;
var y_label = " ";
var y2_label = " ";

var y2Kurven=[<?php for ($y =0; $y < $HelpAnzahlSensoren; $y++){ if(strpos($HelpArraySensoren[$y*5+3],"y2") > 0)echo "true,";else echo "false,";} echo "true,true,true";?>]; //zeigt an welche Kurven auf y oder y2 achse liegen

SingleCheck=0;
var is_visible = beelogger.visibility();
for(var i=0;i<=<?php echo $HelpAnzahlSensoren+2; ?>;i++){
if (is_visible[i]){
  SingleCheck++;curve=i;
  if (y2Kurven[curve]){
    n=y2_label.includes(EinzelEinheiten[curve]);
    if(n== 0){if(y2_label.length > 1){y2_label+=(", ")};y2_label=y2_label+EinzelEinheiten[curve];}
  }
  else{
    n=y_label.includes(EinzelEinheiten[curve]);
    if(n== 0){if(y_label.length > 1){y_label+=(", ")};y_label=y_label+EinzelEinheiten[curve];}
  } 
}
}

if (y2Kurven[curve]){MainAxis=2;}

if(SingleCheck!=1){ 
  beelogger.updateOptions({axes:{y:{valueRange : [null,null]}}}); 
  beelogger.updateOptions({ylabel:y_label});
  beelogger.updateOptions({axes:{y2:{valueRange : [null,null]}}}); 
  beelogger.updateOptions({y2label:y2_label});
  SingleCheck=0;
}
else if(SingleCheck==1){
  if(MainAxis==1){
    beelogger.updateOptions({axes:{y:{valueRange:[null,null]}}}); 
    beelogger.updateOptions({ylabel:EinzelSensoren[curve]+' in '+EinzelEinheiten[curve]});
    beelogger.updateOptions({y2label:'<?php echo $SAs[140]; ?>' + EinzelEinheiten[curve]});
  }else{
    beelogger.updateOptions({ylabel:'<?php echo $SAs[140]; ?>' + EinzelEinheiten[curve]});
    beelogger.updateOptions({axes:{y2:{valueRange:[null,null]}}}); 
    beelogger.updateOptions({y2label:EinzelSensoren[curve]+' in '+EinzelEinheiten[curve]});
    SingleCheck=2;
  }
  konfig_Axes();
}
} //setup_axes

// valueRange fuer Achsen konfigurieren
function konfig_Axes(){
if(SingleCheck==0){return;}
var range_y;
var y_offset;

if(SingleCheck==1){
  range_y=beelogger.yAxisRange(0);
  y_offset=(range_y[1]-range_y[0])*0.1;range_y[1]=range_y[1]-range_y[0]-y_offset;range_y[0]=-y_offset;
  beelogger.updateOptions({axes:{y2:{valueRange:range_y}}});
}
else if(SingleCheck==2){
  range_y=beelogger.yAxisRange(1);
  y_offset=(range_y[1]-range_y[0])*0.1;range_y[1]=range_y[1]-range_y[0]-y_offset;range_y[0]=-y_offset;
  beelogger.updateOptions ({axes: { y: {valueRange : range_y}}});
}
}// konfig_Axes

// Kurve ein - ausblenden   
function change(el) {  // handle event
  var btn_nmr = el.id;
  var box=parseInt(btn_nmr.substr(2,3));  // id="cbxy"
  var check=el.checked;
  set_btn_and_curv(box,check);
}

// set buttons and curve
function set_btn_and_curv(box,check){  

  var visib;
if (check){ //alle Kurven aus
 <?php for ($i=0; $i < $HelpAnzahlSensoren; $i++){echo '  beelogger.setVisibility('.$i.', 0);'."\n";} ?> 
  if (box < <?php echo $HelpAnzahlSensoren; ?>){
    beelogger.setVisibility(<?php echo $HelpAnzahlSensoren; ?>,false);
    beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+1; ?>,false);
    beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+2; ?>,false);
  }
} //if

<?php echo "if ((!check)||(box == ".($HelpAnzahlSensoren+2).")) {"; // aktiver Button macht Kurve an 
    for ($i=0; $i<$HelpAnzahlSensoren; $i++){
      echo "\n".'  visib=document.getElementById("cb'.$i.'").checked; beelogger.setVisibility('.$i.', visib);';
     } //for
    echo "\n".'  visib=document.getElementById("cb'.$HelpAnzahlSensoren.'").checked;';
    echo "\n".'  beelogger.setVisibility('.$HelpAnzahlSensoren.',visib); beelogger.setVisibility(cb'.($HelpAnzahlSensoren+1).',visib);';
  echo "\n".'  visib=document.getElementById("cb'.($HelpAnzahlSensoren+1).'").checked;beelogger.setVisibility(cb'.($HelpAnzahlSensoren+2).',visib);'."\n";
    ?>
  }
  
if (box < <?php echo $HelpAnzahlSensoren; ?>){beelogger.setVisibility(box, check);}

// welche Tageswertkurve
var e = document.getElementById("tagwertbox");
var Value = e.options[e.selectedIndex].value;
var Nummer=6;
if (Value.length <= 2 ) Nummer = parseFloat(Value);
Nummer--;
EinzelEinheiten[<?php echo $HelpAnzahlSensoren; ?>] = EinzelEinheiten[Nummer];
EinzelEinheiten[<?php echo $HelpAnzahlSensoren+1; ?>] = EinzelEinheiten[Nummer];
EinzelEinheiten[<?php echo $HelpAnzahlSensoren+2; ?>] = EinzelEinheiten[Nummer];

var nmr = "cb" + Nummer.toString();

if (box == <?php echo $HelpAnzahlSensoren; ?>) {  // Tageswerte
   berechne_tag(0);
   if (check){ // Wert zur Tageskurve an
     document.getElementById(nmr).checked = true; // Button 
     beelogger.setVisibility(Nummer,true);        // Kurve
     beelogger.updateOptions({});
     document.getElementById('cb<?php echo $HelpAnzahlSensoren; ?>').checked = true;
   }           
   else {
     beelogger.updateOptions({rollPeriod:roll_period});
   }
   beelogger.setVisibility(<?php echo $HelpAnzahlSensoren; ?>, check);   // Tageswert
   beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+1; ?>, check); // Summe
}
else if (box == <?php echo $HelpAnzahlSensoren+1; ?>) {  // Button Summe
   berechne_tag(0);
   if(summe_setup==0){berechne_sum();summe_setup=1;}
   if (check){ // Wert zur Tageskurve an
     document.getElementById(nmr).checked=true;   // Button
     beelogger.setVisibility(Nummer,true);        // Kurve
     beelogger.updateOptions({});
   }
   beelogger.setVisibility(<?php echo $HelpAnzahlSensoren+2; ?>, check);  // Summe
}
setup_Axes();
} //function change

// Box Tageswertanwahl
function tageswertanwahl(){

  var e = document.getElementById("tagwertbox");
  var Value = e.options[e.selectedIndex].value;
  var Nummer=6;
  if (Value.length <= 2 ) Nummer = parseFloat(Value);
  EinzelEinheiten[<?php echo $HelpAnzahlSensoren; ?>] = EinzelEinheiten[Nummer-1];
  EinzelEinheiten[<?php echo $HelpAnzahlSensoren+1; ?>] = EinzelEinheiten[Nummer-1];

  berechne_tag(Nummer); //force
  beelogger.updateOptions({});
  set_btn_and_curv('<?php echo $HelpAnzahlSensoren; ?>', true);  // btn tageswert
  beelogger.updateOptions({});
}

// Berechne Tageszunahme
function berechne_tag(Nummer) 
{   
if (typeof beelogger === 'undefined') {return;}
if (typeof beelogger.rawData_ === 'undefined') {return;}

if(roll_period > 8) beelogger.updateOptions({rollPeriod:8});
if (Nummer == 0){if(tag_setup==1){return;}else {Nummer=6;}}

var k,i,hr_akt,tx_time;
var g_alt,g_neu;
var suche_tag=1,tageswert=0;
var old_d,cur_day,day;
var df_k,diff_korr=0.0;
var datum=new Date();
var boundary=beelogger.numRows();//maximale Anzahl Punkte

data_d.length=0;
g_neu=beelogger.rawData_[0][Nummer];//Startwert Tageswert
if(g_neu===null){g_neu=0;}

datum.setTime(beelogger.rawData_[0][0]);
day=datum.getDate();

var x_ax=boundary-1;
if(Nummer==6){//Auswertung Gewicht
 var e = document.getElementById("tagwertbox");//hole Grenzwert
 var sum_grz = parseFloat(e.options[e.selectedIndex].value); 
 for(i=0;i<boundary;i++) 
   {//fuer alle Punkte
    datum.setTime(beelogger.rawData_[i][0]);
    cur_day = datum.getDate();hr_akt=datum.getHours();
    if(suche_tag) {
      suche_tag=0;k=i;if (k>0){k--;}
      diff_korr =0;day=cur_day;
       while((day==cur_day)&&(k<x_ax)){// whole day
         if(beelogger.rawData_[k][6]!==null){
           if (beelogger.rawData_[k+1][6]===null){
             var ix=1;
             while(ix<10){ix++;if(beelogger.rawData_[k+ix][6]!==null){df_k=beelogger.rawData_[k][6]-beelogger.rawData_[k+ix][6];break;}}
           }
           else{df_k=beelogger.rawData_[k][6]-beelogger.rawData_[k+1][6];}
           if (Math.abs(df_k)>sum_grz){diff_korr+=df_k;}
         }
         k++;datum.setTime(beelogger.rawData_[k][0]);day=datum.getDate();
       }
       old_d=cur_day;g_alt=g_neu;
       if(beelogger.rawData_[k-1][6] !== null){g_neu = beelogger.rawData_[k-1][6];}else {g_neu=g_alt;}
      tageswert=g_neu-g_alt+diff_korr;if(Math.abs(tageswert)<0.015)tageswert=0.0;
    }
    if(cur_day!=old_d){suche_tag=1;tageswert=0;}//next day
    if((hr_akt>=4)&&(hr_akt<20)){
    if(tageswert>0){
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?>tageswert,<?php if ($RollPeriod > 2) echo "0"?>,0]);
      }
    else if(tageswert<0){
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo"beelogger.rawData_[i][".$a."],";}?><?php if ($RollPeriod > 2) echo "0"?>,tageswert,0]);
      }
    else{
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?>0,<?php if ($RollPeriod > 2) echo "0"?>,0]);
      }
    }
  else{//0...6
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?><?php if ($RollPeriod > 2) echo "0,0"; else echo ","; ?>,0]);
    }
  }//end for  
}
else{//Auswertung Regen
 for(i=0;i<boundary;i++) 
   {//fuer alle Punkte
    datum.setTime(beelogger.rawData_[i][0]);
    cur_day = datum.getDate();hr_akt=datum.getHours();
    if(suche_tag) {
      suche_tag=0;k=i;if (k>0){k--;}
      tageswert=0;day=cur_day;
       while((day==cur_day)&&(k<x_ax)){//whole day
        if(beelogger.rawData_[k][Nummer]!==null){
          tageswert+=beelogger.rawData_[k][Nummer];//nur addieren
        }
        k++;datum.setTime(beelogger.rawData_[k][0]);day=datum.getDate();
       }
      old_d=cur_day;
      if(Math.abs(tageswert)<0.05)tageswert=0.0;    
    }

    if(cur_day!=old_d){//next day
      suche_tag=1;tageswert=0;
    }
    if((hr_akt>=3)&&(hr_akt<=22)){
     if(tageswert>0){
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?>tageswert,<?php if ($RollPeriod > 2) echo "0"?>,0]);
      }
     else if(tageswert<0){
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo"beelogger.rawData_[i][".$a."],";}?><?php if ($RollPeriod > 2) echo "0"?>,tageswert,0]);
      }
     else{
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?>0,<?php if ($RollPeriod > 2) echo "0"?>,0]);
     }
    }
    else{//0...6
      data_d.push([new Date(beelogger.rawData_[i][0]),<?php for ($a=1;$a<=$HelpAnzahlSensoren;$a++){echo "beelogger.rawData_[i][".$a."],";} ?><?php if ($RollPeriod > 2) echo "0,0"; else echo ","; ?>,0]);
    }
  }//end for
}//else

beelogger.updateOptions({'file':data_d});//neue Werte laden
setTimeout(function(){berechne_sum();},10);
tag_setup=1;
}

// Berechne Summe aktueller Anzeigebereich
function berechne_sum() {  // 

if (typeof beelogger === 'undefined') {return;}
if (typeof beelogger.rawData_ === 'undefined') {return;}
if (data_d.length == 0 ){return;} // keine Daten

var i, tim;
var datum = new Date();
var sum_gew = 0, once = 1;
i = 0;
j = beelogger.numRows();  // maximale Anzahl Punkte

for(x=0;x< <?php echo $HelpAnzahlSensoren+1; ?> ;x++){ // suche grenzen#
  try {
    i = beelogger.boundaryIds_[x][0]; // akueller Bereich
    j = beelogger.boundaryIds_[x][1];
    break;
  }catch(ignore){}//nothing
}

try {
for(;i<j;i++) {
  datum.setTime(data_d[i][0]);tim=datum.getHours();
  if(once&&(tim>=4)&&(tim<18)){
  if (!isNaN(data_d[i+1][<?php echo $HelpAnzahlSensoren+1; ?>])){ 
    sum_gew=sum_gew+data_d[i+1][<?php echo $HelpAnzahlSensoren+1; ?>];
    once=0;
  }
  if (!isNaN(data_d[i+1][<?php echo $HelpAnzahlSensoren+2; ?>])){ 
    sum_gew=sum_gew+data_d[i+1][<?php echo $HelpAnzahlSensoren+2; ?>];
    once=0;
  }
  }
  else if((tim>=22)||(tim<4)){once=1;}
  data_d[i][<?php echo $HelpAnzahlSensoren+3; ?>]=sum_gew;
} // end for
}catch(ignore){}//nothing
beelogger.updateOptions({'file':data_d});  // neue Werte laden  
}

// Button Reset Zoom
function reset_Zoom() {desired_range=null;beelogger.resetZoom();}

// Button zeige (letzten) Tag/Monat/Woche usw
function zoom(res) {
  if (res > 86400){
       beelogger.updateOptions ({axes: { x: {pixelsPerLabel: 90}}});
  }
  else {
       beelogger.updateOptions ({axes: { x: {pixelsPerLabel: 105}}});
  }
  
  var wold=beelogger.xAxisExtremes();
  var w=beelogger.xAxisRange();
  if((w[1]-res*1000)<wold[0]){w[0]=wold[0]; // ganz links
    if((w[1]+res *1000)<wold[1]){w[1]=w[1]+res*1000;} // nach rechts geht noch
    else{w[1]=wold[1];} // ganz rechts
    desired_range=[w[0],w[1]];
  }
  else{desired_range=[w[1]-res*1000,w[1]];}
  animate();
}

// Button Ansicht nach links oder rechts verschieben
function pan(dir) {  
  var wold=beelogger.xAxisExtremes();
  var w=beelogger.xAxisRange();
  var scale=(w[1]-w[0]);  // aktuelle X - Ansicht
  var amount=scale*dir; // Versatz 
  if((dir<0)&&(w[0]>wold[0])){  // Grenze links
    if((w[0]+amount)<=wold[0]){w[0]=wold[0]-amount;w[1]=w[0]+scale;}
    desired_range=[w[0]+amount,w[1]+amount];
    animate();
  }
  if((dir>0)&&(w[1]<wold[1])){  // Grenze rechts
    if((w[1]+amount)>=wold[1]){w[1]=wold[1]-amount;w[0]=w[1]-scale;}
    desired_range=[w[0]+amount,w[1]+amount];
    animate();
  }
  animate();
}

// Button Zeige gleichen Bereich am rechten oder linkem Ende
function panend(dir) {
  var wold=beelogger.xAxisExtremes();
  var w=beelogger.xAxisRange();
  var scale=(w[1]-w[0]);        // Bereich aktuelle X - Ansicht
  var multiplier;
    if(dir<0){multiplier=(wold[0]-w[0])/scale;}
  else{multiplier=(wold[1]-w[1])/scale;}
  var amount = scale * multiplier;   
  if((dir<0)&&(w[0]>wold[0])){ // Grenze links erreicht?
    if((w[0]+amount)<=wold[0]){w[0]=wold[0]-amount;w[1]=w[0]+scale;}
    desired_range=[w[0]+amount,w[1]+amount];
    animate();
  }
  if((dir>0)&&(w[1]<wold[1])){  // Grenze rechts erreicht ?
    if((w[1]+amount)>= wold[1]){w[1]=wold[1]-amount;w[0]=w[1]-scale;}

    desired_range=[w[0]+amount,w[1]+amount];
    animate();
  }
  animate();
}

function animate(){move++;setTimeout(function(){approach_range();},50);}

// Anzeigebereich festlegen 
function approach_range(){
  if (!desired_range) return;
  if(move>=8){    // (do not set another timeout.)
    beelogger.updateOptions({dateWindow: desired_range});
    berechne_sum();konfig_Axes();move=0;
  }else{  // go halfway there
    var range=beelogger.xAxisRange();
    var new_range;
    new_range = [0.5*(desired_range[0]+range[0]),0.5*(desired_range[1]+range[1])];
    beelogger.updateOptions({dateWindow:new_range});
    animate();move++;
  }
}
<?php } // nicht konfig-Mode, dann grafik
echo "
</script>
<noscript><b>JavaScript must be enabled in order for you to use this page.</b>However, it seems JavaScript is either disabled or not supported by your browser. Enable JavaScript by changing your browser options, and then try again.       
</noscript>
</body>
</html>";
} //if (!$Tracht == "zeigen")

else { //separater Trachtanzeiger....
echo'
<!DOCTYPE html>
<html lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <script type="text/javascript" src="../dygraph21.js" charset="utf-8"></script>
    <link rel="stylesheet" href="../dygraph21.css">
    <title>Tracht</title>
</head>
<body>
<img src="https://beelogger.de/beelogger_logo_small.png">
<FONT SIZE = 5>Trachtverlauf: aufsummierte Gewichtsveränderungen je Tag [kg]</FONT>';

      $k = 0;
      $Offset = "";
      if (file_exists("month.csv")) 
        { //eine month.csv gefunden
        $array = file("month.csv");
        $Size = sizeof($array);
          $i = 0;
          while ($i < $Size) 
            { 
            $what = trim($array[$i]);    
            $x = explode( ",", $what );
            $s = sizeof($x);
            if ($x[$s-1] !='') 
              {
              $AktualisierungsStamp=$x[$s-1];
              if (date('z',$AktualisierungsStamp) > date('z',$LastAktualisierungsStamp)) 
                { 
                if ($Offset == "") $Offset = $x[6];  //Offset holen
                $PrintArray[$k] = substr($x[0],0,10).",".($x[6]-$Offset);
                $k ++;
                }
              $LastAktualisierungsStamp = $AktualisierungsStamp;
              }
            $i++;
            } //while
        }

echo'
<div id="Tracht"></div>
<script type="text/javascript">';
echo"var w = window.innerWidth;  // das Fenster an sich
var h = window.innerHeight;";
   
echo "h=450;w=w*0.95;";
echo "w=parseInt(w);";
echo "\n";
echo 'g = new Dygraph(document.getElementById("Tracht"),"Datum,Gewicht\n" +';

    for ($i=1; $i < $k-1; $i++) 
      { 
      echo '"'.$PrintArray[$i].'\n" + ';
      }
      echo '"'.$PrintArray[$i].'\n"';       
      echo"
          ,
          {
          legend: 'always',
          colors: 'black',
          ylabel: 'Kumulierte korrigierte Gewichtsveränderung in [kg]',
          xlabel: '',
          drawPoints:true,
          width: w,
          height:h, 
          strokeWidth: 1
          }
          );
          </script>
          ";
          echo"
          </body>
          </html>";}


function CSVbuilder($beeOrdner = "")   //CSV Builder Version 3.0----------------------------------------
    {
    $input = $beeOrdner."beelogger.csv";
    if (file_exists($input))
      {              
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
      }
    }// Ende function csv-Builder 3.0
      
          ?>
