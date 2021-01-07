<?php 
/*
 * (C) 2020 Jeremias Bruker,  Rudolf Schick, Thorsten Gurzan - beelogger.de
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

// beelogger:mobile -->
$Softwareversion = "M.15";//vom 16.11.2020 - beelogger_mobileWatch.php
$Sprache = 1; // INIT falls noch keine Speicherung stattfand

$isHttps = (!empty($_SERVER['HTTPS']));
if($isHttps == 0){
    echo 'Webseite   ';
    echo $_SERVER['SERVER_NAME'];
    echo ' bitte mit https:// aufrufen';
    exit;
}
error_reporting(0);
if (file_exists("mobileWatch_ini.php")) include("mobileWatch_ini.php");//Abwärtskomp
if (file_exists("general_ini.php")) include("general_ini.php");

if (file_exists("beelogger_sprachfiles/Mobile_Sprache_".$Sprache.".php")) include ("beelogger_sprachfiles/Mobile_Sprache_".$Sprache.".php"); // Sprache einbinden


 if ($ext_tage == "") $ext_tage = $mW_tage; //Falls ini vorhanden
 if (($ext_tage == "") OR ($ext_tage < 1)) $ext_tage = 14; //default

 if ($ext_spalten == "") $ext_spalten = $mW_spalten;  //Falls ini vorhanden
 if (( $ext_spalten == "") OR ($ext_spalten > 1)) $ext_spalten = 2; //mehr als 2 (default)  sollte nicht gehen
 else $ext_spalten = 1;

$ext_autowatch = htmlentities(strip_tags(stripslashes($_GET['autowatch']))); // = p oder m oder p+m ? dann soll eine automatische Nachricht ausgesendet werden bei inaktivität von beeloggern?


?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<script type="text/javascript" src="dygraph21.js" charset="utf-8"></script>
<link rel="stylesheet" href="dygraph21.css">

<link rel="icon" type="image/png" sizes="16x16" href="./beelogger_icons/favicon-16x16.png" />
<link rel="icon" type="image/png" sizes="32x32" href="./beelogger_icons/favicon-32x32.png" />
<link rel="icon" type="image/png" sizes="96x96" href="./beelogger_icons/favicon-96x96.png" /> 

<style type="text/css">
body { font-family: Arial, Helvetica, sans-serif; color: black; background-color: ivory}
table{ 
      width:100%;
      table-layout:fixed;
      border:6px gray solid;
      border-collapse:collapse;
      text-align:center;
      }
td,tr {
      border:6px gray solid;
      border-collapse:collapse;
      text-align:center;
      }

</style>
<title>beeloggerMobileWatch</title>
</head>
<body>
<table>
  
  
<?php
$jetzt = time();
$WatchCounter=0;

$FetchPushUser = "meinUser"; // Initialiserung User
$FetchPushToken = "meinToken"; // Initialiserung Token

$FetchEmpfaenger_Email = "empfaenger@meineDomain.de"; //Initialiserung Mail
$FetchAbsender_Email = "absender@meineDomain.de"; //Initialiserung Mail

//Array angezeigter beelogger neu anordnen - nach Duo Triple, Quad, Penta und Sept
if ($mW_sort != "Bienenvolkbezeichnung")
 {
 $files = scandir("./");
 natcasesort($files);
 $ba=0;
 foreach ($files as $file) 
    {
    if ($file != "beelogger_icons"  && $file != "beelogger_sprachfiles" && $file != "." && $file != ".." && is_dir($file))
      {
      $file = str_replace("T","E",$file);
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
      $file = str_replace("R","P",$file);
      $NewbeeloggerArray[$ba] = $file;
      $ba++;
      }
  }
else
  {
  $files = scandir("./");
  $beeloggerArray = array();
  foreach ($files as $file) 
    {
    if ($file != "beelogger_icons" && $file != "beelogger_sprachfiles"&& $file != "." && $file != ".." && is_dir($file))
      {
      $SortIniname =  $file."/beelogger_ini.php";
      if (file_exists($SortIniname)) include($SortIniname);
      else $Bienenvolkbezeichnung = "auto";
      
      if ($Bienenvolkbezeichnung != "auto") $beeloggerArray[$file] = $Bienenvolkbezeichnung;
      else $beeloggerArray[$file] = $file;
      }
    }

  natcasesort($beeloggerArray);
  foreach ($beeloggerArray as $key=>$val) 
    {
    $NewbeeloggerArray[] = $key;
    }
  }    
      
$AnzahlDarstellungen=0;
$SpalteNr=0;
$InaktivCounter = 0;
$NoCsvCounter = 0;
$beeloggerCounter = 0;

foreach ($NewbeeloggerArray as $file) 
  {
  if (is_dir($file))
    {
    $filename = $file."/beelogger.csv";
    $ininame =  $file."/beelogger_ini.php";
    $notename = $file."/notes.csv";
    
    if (file_exists($filename)) 
      { //eine beelogger.csv gefunden
      $beeloggerCounter ++;
      $k = 0 ;
      $Aktuell = "";
      $array = file($filename);
      $i = sizeof($array);

      while ($i--) 
        {
        $what = trim($array[$i]);    
        $x = explode( ",", $what );
        $s = sizeof($x);
        if ($x[$s-1] !='') 
          {
          $AktualisierungsStamp=$x[$s-1];
          if ($Aktuell == "") 
            {
            $Aktuell = $AktualisierungsStamp;
            $AktuellerAkkustand = $x[7];
            if (strpos($file,"Duo") !== FALSE) $AktuellerAkkustand = $x[12];
            if (strpos($file,"Triple") !== FALSE) $AktuellerAkkustand = $x[15];
            if (strpos($file,"Quad") !== FALSE) $AktuellerAkkustand = $x[18];
            if (strpos($file,"Penta") !== FALSE) $AktuellerAkkustand = $x[21];
            if (strpos($file,"Sept") !== FALSE) $AktuellerAkkustand = $x[24];
            }
          $PrintArray[$k] = $array[$i]; //Printarray für dygraphs sichern
          $k ++;
          if ($AktualisierungsStamp <= ($Aktuell - 86400*$ext_tage)) break;   //ticks = 60*60*24 = 86400  = 1 Tag
          }// if ($x[$s-1] !='')
        } //while

      $Watchdog = "noIni"; //Reset $Watchdogzustand falls er undefiniert ist
      $BeutenLeergewicht = 0; // Reset
      $AkkuLeerSchwelle = 0; // Reset
      $AkkuVollSchwelle = 0; // Reset
      $mobileWatch_Show = "aktiviert"; //Reset
      
      if (file_exists($ininame)) include ($ininame); // Sensorname holen und Watchdog checken
      // Push User und token aus Ini auslesen
      if ($FetchPushToken == "meinToken")
        { //noch keine User-Tokeninfo gefunden
        if (($PushToken != "meinToken") AND ($PushToken != "")) $FetchPushToken = $PushToken;
        }
      if ($FetchPushUser == "meinUser")
        {//noch keine User-Info gefunden
        if (($PushUser != "meinUser") AND ($PushUser != "")) $FetchPushUser = $PushUser;
        }
      // Ende Push

      // Email Informationen aus Ini auselsen
      if ($FetchEmpfaenger_Email == "empfaenger@meineDomain.de")
        { //noch keine Emailempfänger-Info gefunden
        if (($Empfaenger_Email != "empfaenger@meineDomain.de") AND ($Empfaenger_Email != "")) $FetchEmpfaenger_Email = $Empfaenger_Email;
        }
      if ($FetchAbsender_Email == "absender@meineDomain.de")
        {//noch keine Emailabsender-Info gefunden
        if (($Absender_Email != "absender@meineDomain.de") AND ($Absender_Email != "")) $FetchAbsender_Email = $Absender_Email;
        }



      if ($mobileWatch_Show != "deaktiviert") 
        { //es liegen aktuelle Daten der letzten 30 Tage vor
        if ($ext_autowatch == "")
          { // wenn eine autowatchabfrage kommt muss keine Grafik aufgebaut werden!
            // beginne Tabellenzeile

          if ($ext_spalten == "2")
            {
            if ($SpalteNr == 0) echo "<tr>";
            } //if
          else echo "<tr>";  // Nur eine Spalte gewünscht ... 
          


          $ininameAkku = ""; //RESET
          if (strpos($file,"beeloggerD") !== FALSE) $ininameAkku = "Duo".intval(substr($file,10,1))."/beelogger_ini.php";
          elseif (strpos($file,"beeloggerT") !== FALSE) $ininameAkku = "Triple".intval(substr($file,10,1))."/beelogger_ini.php";
          elseif (strpos($file,"beeloggerQ") !== FALSE) $ininameAkku = "Quad".intval(substr($file,10,1))."/beelogger_ini.php";
          elseif (strpos($file,"beeloggerP") !== FALSE) $ininameAkku = "Penta".intval(substr($file,10,1))."/beelogger_ini.php";
          elseif (strpos($file,"beeloggerS") !== FALSE) $ininameAkku = "Sept".intval(substr($file,10,1))."/beelogger_ini.php";
          else $ininameAkku =  $file."/beelogger_ini.php";


          if ($ininameAkku != "")
            {
            include($ininameAkku);

          if (($jetzt-$Aktuell) < 32400) echo "<td style='color:black;'>\n"; //6 Stunden
          elseif (($jetzt-$Aktuell) < 36000 AND $EESendeIntervall == "B") echo "<td style='color:black;'>\n";
          elseif (($jetzt-$Aktuell) < 72000 AND $EESendeIntervall == "C") echo "<td style='color:black;'>\n";
          elseif (($jetzt-$Aktuell) < 93600 AND $EESendeIntervall == "D") echo "<td style='color:black;'>\n";
          else echo "<td style='color:red;'>\n";

          echo '<div id="hdr_'.$AnzahlDarstellungen.'">'."\n"; 

            if ($AkkuLeerSchwelle != 0 && $AkkuVollSchwelle != 0 && $AktuellerAkkustand != 0)
              {
              if ($AktuellerAkkustand > $AkkuVollSchwelle) echo'<img src="beelogger_icons/Akku100.png" width="25" height="15" style="margin-right:5px;">'; //Akku 100
              elseif ($AktuellerAkkustand < $AkkuLeerSchwelle) echo'<img src="beelogger_icons/Akku25.png" width="25" height="15" style="margin-right:5px;">'; //Akku 100Akku leer
              else echo'<img src="beelogger_icons/Akku50.png" width="25" height="15" style="margin-right:5px;">'; //Akku 100
              }
            }



          

          
          if ($ext_spalten == "2") echo '<font size="5">';
          else echo '<font size="6">';
            
          echo'<a href="'.$file.'/beelogger_show.php">';

          $mW_sensor1 = ""; //INIT
          $mW_sensor2 = ""; //INIT
          $mW_roll = ""; //INIT
          $mW_legende = "always"; //INIT
          $mW_notes = false; //INIT
          $mW_tageswertanzeige = "aktiviert"; //INIT
          $KoeAnzeige = "deaktiviert"; //INIT
          $KoeInfo = ""; //INIT
          $BeutenLeergewicht = 0; //INIT
          $Honigraeume[0] = 0; //INIT
          $Honigraeume[2] = 0; //INIT
          $BeutenUtils[0][0] = 0; //INIT
          $BeutenUtils[1][0] = 0; //INIT
          $BeutenUtils[2][0] = 0; //INIT

          if (file_exists($ininame)) include ($ininame); // Sensorname holen und Watchdog checken

          $ext_sensor1 = $mW_sensor1;
          $ext_sensor2 = $mW_sensor2;

          if ($ext_sensor2 == "0") $ext_sensor2 = "";

          $ShowFile = str_replace("beelogger","",$file);
          if ($Bienenvolkbezeichnung == "auto") echo "<b>".$file."</b>";
          else 
            {
            if ($mW_sort != "Bienenvolkbezeichnung") echo "<b>".$ShowFile."</b>(".html_entity_decode($Bienenvolkbezeichnung).")";
            else echo "<b>".html_entity_decode($Bienenvolkbezeichnung)."</b>(".$ShowFile.")";
            }
          echo'</a> ';

          if ($KoeAnzeige != '' AND $KoeAnzeige != "deaktiviert") echo "<img src='beelogger_icons/".$KoeAnzeige."' width='25' height='25' style='margin-bottom:-5px;' title='".$KoeInfo."'>";
 

          if (($jetzt-$Aktuell) > 32400) echo " ".$MAs[0]." ".date($MAs[44],$Aktuell);


          echo $MAs[45].$MAs[1]." ".date("H:i",$Aktuell).$MAs[2]." ";

          $what = trim($PrintArray[0]);
          $x = explode( ",", $what );
          $Sonderzeichen = array("[","]");


          // Gewicht muss immer sensor 1 sein
          if ($ext_sensor2 == 6)
            {
            $help = $ext_sensor1;
            $ext_sensor1 = 6;
            if ($help != 6) $ext_sensor2 = $help;
            else $ext_sensor2 = "";
            }

          if (($ext_sensor1 == "") OR ($ext_sensor1 < 1 )) $ext_sensor1 = 6; //Gewichtssensor ist default

          $ext_roll = $_GET['roll']; // der zu übermittelnde Sensor, der für alle Beelogger
          if ($ext_roll == "") $ext_roll = $mW_roll; //Falls ini vorhanden
          if (($ext_roll == "") OR ($ext_roll < 1 )) $ext_roll = 3; 


  
          if ($BeutenLeergewicht > 0) 
            {
            if (($Honigraeume[0]+$Honigraeume[2]) > 0) 
              {
              $Klammern = array("[", "]");  
              $GewichtsEinheit = str_replace($Klammern,"",$Sensoren[5*5+4]);  
              for ($i=0; $i < $Honigraeume[0]; $i++) 
                {
                if ($Honigraeume[1] >10) $ShowHonigraum = $Honigraeume[1]/1000;// noch in Gramm
                else $ShowHonigraum =  $Honigraeume[1];
                echo "&nbsp;<img src='beelogger_icons/honigraum.png' width='15' height='".($ShowHonigraum*2)."' title='".$Honigraeume[1].$GewichtsEinheit."'>";
                }
              for ($j=0; $j < $Honigraeume[2]; $j++) 
                {
                if ($Honigraeume[3] >10) $ShowHonigraum = $Honigraeume[3]/1000;// noch in Gramm 
                else $ShowHonigraum =  $Honigraeume[3];
                echo "&nbsp;<img src='beelogger_icons/honigraum.png' width='15' height='".($ShowHonigraum*2)."' title='".$Honigraeume[3].$GewichtsEinheit."'>";
                }

              if ($BeutenUtils[0][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Varoaeinschub.png' width='15' height='15' title='Gewicht der Varoaschublade = ".$BeutenUtils[0][1].$GewichtsEinheit."'>";

              if ($BeutenUtils[1][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Absperrgitter.png' width='15' height='15' title='Gewicht des Absperrgitters = ".$BeutenUtils[1][1].$GewichtsEinheit."'>";

              if ($BeutenUtils[2][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Futterzarge.png' width='15' height='15' title='Gewicht der Futterzarge = ".$BeutenUtils[2][1].$GewichtsEinheit."'>";
              }

            else 
              {
              echo'<img src="beelogger_icons/honey.png" height="26" style="margin-bottom:-5px;">';

              if (($BeutenUtils[0][0] == 1 OR $BeutenUtils[1][0] == 1 OR $BeutenUtils[2][0] == 1)) echo "(";

              if ($BeutenUtils[0][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Varoaeinschub.png' width='20' height='20' title='Gewicht der Varoaschublade = ".$BeutenUtils[0][1].$GewichtsEinheit."'>";

              if ($BeutenUtils[1][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Absperrgitter.png' width='20' height='20' title='Gewicht des Absperrgitters = ".$BeutenUtils[1][1].$GewichtsEinheit."'>";

              if ($BeutenUtils[2][0] == 1) echo "&nbsp;<img src='beelogger_icons/n_Futterzarge.png' width='20' height='20' title='Gewicht der Futterzarge = ".$BeutenUtils[2][1].$GewichtsEinheit."'>";
              if (($BeutenUtils[0][0] == 1 OR $BeutenUtils[1][0] == 1 OR $BeutenUtils[2][0] == 1))echo ")";
              echo ":";
              }



            $FuTr = ($x[6] - $BeutenLeergewicht-($Honigraeume[0]*$Honigraeume[1])-($Honigraeume[2]*$Honigraeume[3])-($BeutenUtils[0][0]*$BeutenUtils[0][1])-($BeutenUtils[1][0]*$BeutenUtils[1][1])-($BeutenUtils[2][0]*$BeutenUtils[2][1]));
            echo "&nbsp;".$FuTr.str_replace($Sonderzeichen,"",$Sensoren[29])."</b>";
            if (($Honigraeume[0]+$Honigraeume[2]) > 0) $GesamtFuTr += $FuTr;
            }
          elseif ($Sensoren[($ext_sensor1-1)*5] != "") 
            {
            if ($Sensoren[($ext_sensor1-1)*5] == "Gewicht") echo'<img src="beelogger_icons/weight.png" width="30" height="20" style="margin-right:5px;">'.":";
            else echo $Sensoren[($ext_sensor1-1)*5].":";
            if ($x[$ext_sensor1] != "") echo $x[$ext_sensor1].str_replace($Sonderzeichen, "", $Sensoren[($ext_sensor1-1)*5+4]).'</b>';
            }

          echo "\n</font>\n</div>\n";
          $beeloggerGraph = "beelogger_grafik_".$file;
          echo "\n".'<div id="'.$beeloggerGraph.'"></div>';
          echo"\n";

          ?>

            <script type="text/javascript">

              var d_names = [<?php echo '"'.$MAs[25].'","'.$MAs[26].'","'.$MAs[27].'","'.$MAs[28].'","'.$MAs[29].'","'.$MAs[30].'","'.$MAs[31].'"'; ?>];
              var m_names = [<?php echo '"'.$MAs[32].'","'.$MAs[33].'","'.$MAs[34].'","'.$MAs[35].'","'.$MAs[36].'","'.$MAs[37].'","'.$MAs[38].'","'.$MAs[39].'","'.$MAs[40].'","'.$MAs[41].'","'.$MAs[42].'","'.$MAs[43].'"'; ?>]; 
            var w = window.innerWidth;  // das Fenster an sich
            var h = window.innerHeight;
            <?php    
            if ($ext_spalten == "2"){echo "h=380;w=w/2-50;";} else {echo "h=450;w=w*0.95;";}
            echo "w=parseInt(w);";
            echo "\n";
            echo"var g$AnzahlDarstellungen" ?> = new Dygraph(
              document.getElementById(<?php echo '"'.$beeloggerGraph.'"'?>),
              "<?php echo $MAs[24].",".html_entity_decode($Sensoren[($ext_sensor1-1)*5]).','; if ($ext_sensor2 != "") echo html_entity_decode($Sensoren[($ext_sensor2-1)*5]); if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert"){ echo ','.$MAs[3].'+,'.$MAs[3].'-';}?>\n" +
            <?php
            for ($l = ($k-1); $l > 0; $l--)
              {
              $what = trim($PrintArray[$l]);
              $x = explode( ",", $what );
              if ($ext_sensor1 != 6) echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].'\n" + ';
              else 
                {
                if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].',0,0\n" + '; 
                else echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].'\n" + ';
                }
              } 
            $what = trim($PrintArray[0]);
            $x = explode( ",", $what );
            if ($ext_sensor1 != 6) echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].'\n",';
            else  
              {
              if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].',0,0\n",';
              else echo '"'.$x[0].','.$x[$ext_sensor1].','.$x[$ext_sensor2].'\n",';
              }

              echo"  { //Optionen
    visibility: ['false','true',";
              if ($ext_sensor2 != "") echo "'true'";
              else echo "'false'";
              if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo ",'true','true'";
              //else echo ",'false','false'";
              echo "],
              colors: ['".$Sensoren[($ext_sensor1-1)*5+1]."','".$Sensoren[($ext_sensor2-1)*5+1]."'";
              if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo ",'#00ff01','#ff0001'";
              echo "],
              digitsAfterDecimal: '2',
              rollPeriod:".$ext_roll.",
              series: {
                '".html_entity_decode($Sensoren[($ext_sensor1-1)*5])."': {axis: 'y2'},\n";
          if ($ext_sensor2 != "") echo "
                  '".html_entity_decode($Sensoren[($ext_sensor2-1)*5])."': {axis: 'y1'},\n";

          if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo "     '".$MAs[3]."+': { axis:'y' ,drawPoints:false,pointSize:1,fillGraph:true,fillAlpha:0.5 },\n";
          if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") echo "     '".$MAs[3]."-': { axis:'y' ,drawPoints:false,pointSize:1,fillGraph:true,fillAlpha:0.5 },";
                
          echo ' 
                },
              axes:{
                x:{axisLabelFormatter:function(d,gran){
                  var curr_day=d_names[d.getDay()];
                  var curr_month=m_names[d.getMonth()];
              try{var w= g'.$AnzahlDarstellungen.'.xAxisRange();}
              catch(ignore){var w=[0,1];}
              if((w[1]-w[0])<(86400000*3)){
                  var Minuten=d.getMinutes();
                  Minuten=((Minuten<10)?"0"+Minuten:Minuten);
                    return curr_day+" "+d.getDate()+"'.$MAs[45].'"+curr_month+" "+d.getHours()+":"+ Minuten+" ";
                  }
                  else{return curr_day+" "+d.getDate()+"'.$MAs[45].'"+curr_month;}
                },
                axisLabelWidth:160,pixelsPerLabel:110
              },
              y2: {
                 drawGrid: false,
                 independentTicks: true,
                 labelsKMB: true
                },
            y1: {
                 drawGrid: false,
                 independentTicks: true,
                 labelsKMB: true
                }';

          if ($mW_legende == "") $mW_legende = "always";
          echo "
            },
            legend:'".$mW_legende."',
            hideOverlayOnMouseOut: false,
            labelsSeparateLines: true,
            strokeWidth:2.0,
            width: w,
            height:h,";

          if ($ext_sensor2 != "") echo "ylabel:'".html_entity_decode($Sensoren[($ext_sensor2-1)*5])." ".$MAs[4]." ".$Sensoren[($ext_sensor2-1)*5+4]."',";
          echo"
              y2label:'".html_entity_decode($Sensoren[($ext_sensor1-1)*5])." ".$MAs[4]." ".$Sensoren[($ext_sensor1-1)*5+4]."',";?>
          <?php
          // Mobilgeräte
          if (preg_match("/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine
          |htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|
          panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus
          |up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i", $_SERVER['HTTP_USER_AGENT'])) { echo "   interactionModel:{}\n";} 
          else {echo "   interactionModel: Dygraph.defaultInteractionModel\n";}

          echo "});\n";

          echo"g$AnzahlDarstellungen";
          echo".ready(function(){\n";
          // Mozilla / Firefox
          // if (preg_match("/(Firefox)/i", $_SERVER['HTTP_USER_AGENT'])) 
          //   { 
          if ($ext_sensor1 == 6 AND $mW_tageswertanzeige != "deaktiviert") 
            { 
            echo "g$AnzahlDarstellungen"; 
            echo"_berechne_tag();\n";
            }
          echo "setTimeout(function(){g$AnzahlDarstellungen";
          echo"_onresize();}, 200);\n";

          echo "});\n";

          ?>

          function <?php echo"g$AnzahlDarstellungen" ?>_onresize()
            {
            var w = window.innerWidth;
            var h;
            <?php 
            if ($ext_spalten == "2") echo " h=380;w=w/2-50;";
            else echo " h=450;w=w*0.95;";
            ?>
            w=parseInt(w);
            <?php echo" g$AnzahlDarstellungen" ?>.resize(w,h);
            }


          // Anmerkungen einlesen

          //Datei notes.csv nach Anmerkungen durchsuchen und im Array für die Darstellung aufbereiten-----
          <?php 
          if ($mW_notes == true)
            {
            $input = $notename; //"notes.csv";
            $narray = file($input);
            $ni = sizeof($narray);

            $b=0;
            for ($a = 0 ; $a < $ni; $a++) 
              {
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

            echo "g$AnzahlDarstellungen.setAnnotations([  ";

            for ($w = 0; $w < $b; $w++)
              {
              echo "\n{";
              echo "series: '".$anmerkungen[$w]."',";
              $w++;
              echo 'xval: Date.parse(\''.$anmerkungen[$w].'\'),';
              $w++;
              if (!strpos($anmerkungen[$w],".png") === false) echo 'icon: "'.substr($anmerkungen[$w],3).'",
                  width: 30,
                  height: 30';

              else echo 'shortText: "'.$anmerkungen[$w].'",
              width: '.(6+8*strlen($anmerkungen[$w]));
              $w++;
              echo ',text: "'.$anmerkungen[$w].'",';
              echo'attachAtBottom: true,';
              echo "},";
              }//for $w
             
            echo"]);";


            echo "
            var anns = beelogger.annotations();
            ann_count = anns.length+1;";
            }
          
          ?>
          //*******************************************
          // Berechne Tageszunahme
          function <?php echo" g$AnzahlDarstellungen" ?>_berechne_tag() 
            {   
            var k, i, hr_akt, tx_time;
            var g_alt, g_neu;
            var suche_tag=1, tageswert=0;
            var old_d, cur_day, day;
            var df_k, diff_korr = 0.0;

            var data_d = [];   // neue Datenstruktur

            var datum=new Date();
            var boundary=<?php echo"g$AnzahlDarstellungen" ?>.numRows();   // maximale Anzahl Punkte
            if(boundary===null){return;}

            var sum_grz = 3.0;
            for (i=0;i<10;i++)
              {
              try
                {
                if(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][1] > 1100){sum_grz=3000;break;}
                }catch(ignore){};
              }

            data_d.length=0;

            try{
            g_neu=<?php echo"g$AnzahlDarstellungen" ?>.rawData_[0][1];    // Startwert Gewicht
            }catch(ignore){};
            if(g_neu===null){g_neu=0;}

            datum.setTime(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[0][0]);
            day = datum.getDate();
            // Array vorbelegen
            data_d.push([new Date(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[0][0]),
                        <?php echo"g$AnzahlDarstellungen" ?>.rawData_[0][1],
                        <?php echo"g$AnzahlDarstellungen" ?>.rawData_[0][2],0,0]);
            var x_ax = boundary-1;
            for(i=0;i<boundary;i++) 
              { // fuer alle Punkte
              datum.setTime(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][0]);
              hr_akt=datum.getHours();cur_day=datum.getDate();
              if(suche_tag) 
                {
                suche_tag=0;k=i;if(k>0){k--;}diff_korr=0;
                while((day==cur_day)&&(k<x_ax))
                  {// whole day
                  if(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k][1]!==null)
                    {
                    if (<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k+1][1]!==null)
                      {
                      df_k = <?php echo"g$AnzahlDarstellungen" ?>.rawData_[k][1]- <?php echo"g$AnzahlDarstellungen" ?>.rawData_[k+1][1];
                      if (Math.abs(df_k)>sum_grz){diff_korr += df_k;}
                      }
                    else
                      {
                      var ix=1;
                      while(ix<10)
                        {
                        ix++;
                        if (<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k+ix][1]!==null)
                          {
                          df_k = <?php echo"g$AnzahlDarstellungen" ?>.rawData_[k][1] - <?php echo"g$AnzahlDarstellungen" ?>.rawData_[k+ix][1];
                          if (Math.abs(df_k)>sum_grz){diff_korr += df_k;}
                          break;
                          }
                        }
                      }
                    }
                    k++;datum.setTime(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k][0]);day=datum.getDate();
                  }
                old_d=cur_day;g_alt=g_neu;
                if(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k-1][1]===null){g_neu=g_alt;}
                else{g_neu=<?php echo"g$AnzahlDarstellungen" ?>.rawData_[k-1][1];} //next morning
                tageswert=g_neu-g_alt+diff_korr;if(Math.abs(tageswert)<0.015)tageswert=0.0;
                }
              if(cur_day!=old_d){suche_tag=1;tageswert=0;}// next day, zero value
              if((hr_akt>=4)&&(hr_akt<20))
                {
                if(tageswert>0)
                  {
                  data_d.push([new Date(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][0]),
                              <?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][1],<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][2],tageswert, ]);
                  }
                else if(tageswert<0)
                  {
                  data_d.push([new Date(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][0]),
                              <?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][1],<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][2], ,tageswert]);
                  }
                else
                  {
                  data_d.push([new Date(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][0]),
                              <?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][1],<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][2],0, ]);
                  }
                }
              else
                {
                data_d.push([new Date(<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][0]),
                              <?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][1],<?php echo"g$AnzahlDarstellungen" ?>.rawData_[i][2], , ]);
                }
              } // end for
            <?php echo"g$AnzahlDarstellungen" ?>.updateOptions({'file':data_d});  // neue Werte laden
            } //ende Function

          </script>
          <?php
          echo "</td>\n\n";
          $AnzahlDarstellungen++;
          // Tabelle Zeilenende
          if ($ext_spalten == "2")
            {
            $SpalteNr ++;  
            if ($SpalteNr == 2)
              {
              echo "</tr>";
              $SpalteNr = 0;
              } 
            }
          // Nur eine Spalte gewünscht ... 
          else echo "</tr>";
          } //if ($ext_autowatch == "")
          if ($Watchdog == "deaktiviert") $WatchCounter ++;  //deaktivierte zählen  trotz aktueller Daten
        } //((($jetzt-$Aktuell) < 86400)

      else 
        { //sammel Daten für heute Inaktive Beuten-Ausgabe
        if ($Watchdog == "aktiviert")
          { //nur wenn der Watchdog aktiviert ist wird reagiert!
            $InaktivArray[$InaktivCounter] = $ininame;
            $InaktivCounter ++;

            $InaktivArray[$InaktivCounter] = $file;
            $InaktivCounter ++;

            if ($Aktuell != "") $InaktivArray[$InaktivCounter] = $Aktuell; //falls kein Wert in beelogger.csv
            else $InaktivArray[$InaktivCounter] = $MAs[5];
            $InaktivCounter ++;
          } //if ($Watchdog == "aktiviert")
        elseif ($Watchdog == "deaktiviert") $WatchCounter ++;  //deaktivierte zählen  
        } // else { //sammel Daten für Inaktive Beuten-Ausgabe
      }  //if (file_exists($filename))
    else 
      {
      $NoCsvCounter ++; //Es wurde keine beelogger.csv-Datei gefunden
      if (file_exists($ininame))
        {
        include ($ininame); // Sensorname holen und Watchdog checken
        if ($Watchdog == "aktiviert")
          { //nur wenn der Watchdog aktiviert ist wird reagiert!
          $InaktivArray[$InaktivCounter] = $ininame;
          $InaktivCounter ++;

          $InaktivArray[$InaktivCounter] = $file;
          $InaktivCounter ++;

          $InaktivArray[$InaktivCounter] = "Nie";
          $InaktivCounter ++;
          } //if ($Watchdog == "aktiviert")
        elseif ($Watchdog == "deaktiviert") $WatchCounter ++;  //deaktivierte zählen  
        } // if (file_exists($ininame))
      } //else {$NoCsvCounter ++; //Es wurde keine beelogger.csv-Datei gefunden
    }  //if is dir file
  }// foreach


//Zusatzinfos ausgeben
if ($beeloggerCounter == 0) echo $MAs[6];

if ($WatchCounter > 0)
  {
  echo "<tr><td><font size ='5' color=#FF000>";

  if ($WatchCounter != 1) echo $WatchCounter." ".$MAs[7];
  else echo $MAs[8];

  echo "</font></td>";
  }

if ($ext_spalten != "2") echo "</tr><tr>";

if ($NoCsvCounter > 0)
  {
  echo "<td>";
  echo "<font size ='5' color=#FF0000>";

  if ($NoCsvCounter != 1) echo $NoCsvCounter." beelogger ".$MAs[9];
  else echo $MAs[10];

  echo "</font></td></tr>";
  }

if ($GesamtFuTr != "" AND $GesamtFuTr > 0) echo "<tr><td>".$MAs[11].": ".$GesamtFuTr.str_replace($Sonderzeichen,"",$Sensoren[29])."</td></tr>";            

echo "</table>";

if ($InaktivCounter != 0) 
  { // es gibt inaktive beelogger

  $Message = $MAs[12].": ";  // Vorbereitung AutoWatchFunktion

  echo '<b><font size="5">mobileWatch-Version: '.$Softwareversion.'<br>Watchdog - '.$MAs[13].': </b>';
  for ($b=0;$b<$InaktivCounter;$b++)
    {
    include ($InaktivArray[$b]); // Sensorname holen über beelogger_ini.php
    $b++;
    echo '<br><font size="5"><b>';
    echo'<a href="'.$InaktivArray[$b].'/beelogger_show.php">';
    if ($Bienenvolkbezeichnung == "auto") {
    echo $InaktivArray[$b];
     $Message .= $InaktivArray[$b].", ";}
    else {
      echo $InaktivArray[$b]." (".html_entity_decode($Bienenvolkbezeichnung).")";
      $Message .= $InaktivArray[$b]." (".html_entity_decode($Bienenvolkbezeichnung)."), ";}
    echo'</a> ';
    $b++;
    if ($InaktivArray[$b] == "Nie") echo '<font color=#FF0000>- '.$MAs[14].'! </font>';
    else echo '<font color=#FF0000>- '.$MAs[17].': </font>'.date("d.m.y",$InaktivArray[$b]).' '.$MAs[1].' '.date("H:i",$InaktivArray[$b]);
    } //for ($b=0;$b<$InaktivCounter;$b++)
  } //if ($InaktivCounter != 0)

?>
</body>
</html>
<?php



// Automatischer Watchdogbereich

//Pushnachricht erzeugen 
if ($ext_autowatch != "")
  {
  if ($Message != "")
    {

    $Message = substr($Message,0,-2); //letztes Komma der Beutenaufzählung entfernen  

    if (($ext_autowatch == "p") OR ($ext_autowatch == "pm") OR ($ext_autowatch == "mp"))
      {
      $PushUser = $FetchPushUser;
      $PushToken = $FetchPushToken;

      if (($PushUser != "meinUser") AND ($PushUser != ""))
        { //falls User eingegeben wurde

        curl_setopt_array($ch = curl_init(), array(
        CURLOPT_URL => "https://api.pushover.net/1/messages.json",
        CURLOPT_POSTFIELDS => array(
        "token" => $PushToken,
        "user" => $PushUser,
        "message" => $Message,
                                   ),
        CURLOPT_SAFE_UPLOAD => true,
        CURLOPT_RETURNTRANSFER => true,
                                                  )
                         );
        curl_exec($ch);
        curl_close($ch);

        echo "<br><br>".$MAs[15].".";
        echo "<br>".$MAs[16].": ".$Message;
        } //if ($PushUser != ""){ //falls User eingegeben wurde

      } //if (($ext_autowatch == "p") OR ($ext_autowatch == "p+m"))

    //Mailnachricht erzeugen

    if (($ext_autowatch == "m") OR ($ext_autowatch == "pm") OR ($ext_autowatch == "mp"))  
      {
      $Absender_Email = $FetchAbsender_Email;
      $Empfaenger_Email = $FetchEmpfaenger_Email;

      if (($Empfaenger_Email != "empfaenger@meineDomain.de") AND ($Empfaenger_Email != "") AND ($Absender_Email != "absender@meineDomain.de") AND ($Absender_Email != ""))
        {
        $Mailbetreff = "beelogger-".$MAs[18].": ";
        $Absender_Name = "beelogger-watchdog";
        $Header  = "From:".$Absender_Name." <".$Absender_Email.">\n";
        
        mail($Empfaenger_Email, $Mailbetreff, $Message, $Header);
        echo "<br><br>".$MAs[19].".";
        echo "<br>".$MAs[20].": ".$Absender_Email." ".$MAs[21].": ".$Empfaenger_Email;
        echo " ".$MAs[22].": <br>".$Message;
        
        }//if ($Empfaenger_Email != "empfaenger@meineDomain.de")
        else echo "<br>".$MAs[23]."!";

      } // if (($ext_autowatch == "m") OR ($ext_autowatch == "p+m"))

    } //if ($Message != !!)
  } //if ($ext_autowatch != "")
?>

