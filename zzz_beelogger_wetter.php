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


$Wetterversion_ = "M.15";//vom 30.10.2020 - beelogger_wetter.php 

if ($MultiType == 0) 
    {
    $Wetter_Name = $WechselWetterName;
    $LocName = $WechselLocName;
    $MapName = $WechselMapName;
    }
else 
    {
    $Wetter_Name = "wetter_daten.php";
    $LocName = "loc.php";
    $MapName = "beelogger_map.php";
    }

if (file_exists($LocName)) 
    {
    include($LocName); //import Geodaten
    if ($lat != "") $WetterLat = $lat;
    if ($lon != "") $WetterLon = $lon;
    }
elseif (file_exists($MapName)) 
    {
    include($MapName); //import Geodaten
    if ($beeloggerMap2Lat != "" OR $beeloggerMap2Lat != "00.00" OR $beeloggerMap2Lat != "0.00") $WetterLat = $beeloggerMap2Lat;
    if ($beeloggerMap2Lon != "" OR $beeloggerMap2Lon != "00.00" OR $beeloggerMap2Lon != "0.00") $WetterLon = $beeloggerMap2Lon;
    }


if (file_exists($Wetter_Name)) include($Wetter_Name);

if ($CommunityUser) include ("../../OpenweathermapKey.php");

if (($WetterLat != "" AND $WetterLat != "00.00" AND $WetterLon != "" AND $WetterLon != "00.00" OR ($OrtsDaten[0] != "" AND $OrtsDaten[1] != "")) AND $OpenweathermapKey != "")
    { //Ortsdaten vorhanden un key vorhanden für externe server
    if (date("G,z") != $WetterDatum OR $NeuWetterIcons != "") 
        {
        //Daten abrufen, weil keine aktuellen da
        
        $url = "https://api.openweathermap.org/data/2.5/onecall?lat={$WetterLat}&lon={$WetterLon}&exclude=minutely&lang=de&units=metric&appid={$OpenweathermapKey}";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $json = curl_exec($curl); // geändert nach $jsonString
        curl_close($curl);
        $wetter = json_decode($json);

 
        $WetterHourlyArray = array($wetter->current->weather[0]->icon,$wetter->hourly[1]->weather[0]->icon,$wetter->hourly[2]->weather[0]->icon,$wetter->current->weather[0]->description,$wetter->hourly[1]->weather[0]->description,$wetter->hourly[2]->weather[0]->description);
        $TempHumHourlyArray = array(round($wetter->current->temp,1),round($wetter->hourly[1]->temp,1),round($wetter->hourly[2]->temp,1),($wetter->current->humidity),($wetter->hourly[1]->humidity),($wetter->hourly[2]->humidity));
        $WetterDailyArray = array($wetter->daily[0]->weather[0]->icon,$wetter->daily[1]->weather[0]->icon,$wetter->daily[2]->weather[0]->icon,$wetter->daily[3]->weather[0]->icon,$wetter->daily[0]->weather[0]->description,$wetter->daily[1]->weather[0]->description,$wetter->daily[2]->weather[0]->description,$wetter->daily[3]->weather[0]->description);
        $TempDailyArray = array(round($wetter->daily[0]->temp->max,1),round($wetter->daily[1]->temp->max,1),round($wetter->daily[2]->temp->max,1),round($wetter->daily[3]->temp->max,1),round($wetter->daily[0]->temp->min,1),round($wetter->daily[1]->temp->min,1),round($wetter->daily[2]->temp->min,1),round($wetter->daily[3]->temp->min,1));
        $SunArray = array($wetter->current->sunrise,$wetter->current->sunset);
        $WindArray = array($wetter->current->wind_speed, $wetter->current->wind_deg);
        


        $WetterDaten =
        '<?php
        $OrtsDaten = array("'.$WetterLat.'","'.$WetterLon.'");
        $WetterDatum = "'.date("G,z").'";
        $WetterHourlyArray = array("'.$WetterHourlyArray[0].'","'.$WetterHourlyArray[1].'","'.$WetterHourlyArray[2].'","'.$WetterHourlyArray[3].'","'.$WetterHourlyArray[4].'","'.$WetterHourlyArray[5].'");
        $TempHumHourlyArray = array("'.$TempHumHourlyArray[0].'","'.$TempHumHourlyArray[1].'","'.$TempHumHourlyArray[2].'","'.$TempHumHourlyArray[3].'","'.$TempHumHourlyArray[4].'","'.$TempHumHourlyArray[5].'");
        $WetterDailyArray = array("'.$WetterDailyArray[0].'","'.$WetterDailyArray[1].'","'.$WetterDailyArray[2].'","'.$WetterDailyArray[3].'","'.$WetterDailyArray[4].'","'.$WetterDailyArray[5].'","'.$WetterDailyArray[6].'","'.$WetterDailyArray[7].'");
        $TempDailyArray = array("'.$TempDailyArray[0].'","'.$TempDailyArray[1].'","'.$TempDailyArray[2].'","'.$TempDailyArray[3].'","'.$TempDailyArray[4].'","'.$TempDailyArray[5].'","'.$TempDailyArray[6].'","'.$TempDailyArray[7].'");
        $SunArray = array("'.$SunArray[0].'","'.$SunArray[1].'");
        $WindArray = array("'.$WindArray[0].'", "'.$WindArray[1].'");
        ?>';
        //Wetterdaten abspeichern
        $aktion = fOpen($Wetter_Name,"w");
        fWrite($aktion , $WetterDaten);
        fClose($aktion);


//////////////////  Alte Wetterdaten einlesen    /////////////////////////
        if ($ExWetterDaten) 
            {  
            }  


        }


// Icon Translation
    $opm_icon = array('01d', '01n', '02d', '02n', '03d', '03n', '04d', '04n', '09d', '09n', '10d', '10n', '11d', '11n','13d', '13n', '50d', '50n');
    $beelogger_icon = array('clear-day', 'clear-night', 'partly-cloudy-day', 'partly-cloudy-night', 'cloudy', 'cloudy', 'cloudy', 'cloudy', 'hail', 'hail', 'rain', 'rain', 'thunderstorm', 'thunderstorm','snow', 'snow', 'fog', 'fog');

    if ($WetterIcons != "2") 
        {
        for ($i=0; $i < 3; $i++) 
            { 
            $WetterHourlyArray[$i] = str_replace($opm_icon,$beelogger_icon,$WetterHourlyArray[$i]);
            $WetterDailyArray[$i] = str_replace($opm_icon, $beelogger_icon,$WetterDailyArray[$i]);
            }
        }

        /*
        Open Weather Map Icon Quelle    = https://openweathermap.org/weather-conditions
        01d = w_clear_day               = clear sky day
        01n = w_clear_night             = clear sky night
        02d = w_partly-cloudy-day       = few clouds day
        02n = w_partly-cloudy-night     = few clouds night
        03d = w_cloudy                  = scattered clouds
        03n = w_cloudy                  = scattered clouds
        04d = w_cloudy                  = broken clouds
        04n = w_cloudy                  = broken clouds
        09d = w_hail                    = shower rain
        09n = w_hail                    = shower rain
        10d = w_rain                    = rain
        10n = w_rain                    = rain
        11d = w_thunderstorm            = thunderstorm
        11n = w_thunderstorm            = thunderstorm
        13d = w_snow                    = snow
        13n = w_snow                    = snow
        50d = w_fog                     = mist
        50n = w_fog                     = mist
        */
        
    
    // Windrichtung von Gradzahl nach Kurzbeschreibung umwandeln
    $Windrichtung = array (
    ["bez" => "N", "winkel" => 0],
    ["bez" => "NO", "winkel" => 22.5],
    ["bez" => "O", "winkel" => 67.5],
    ["bez" => "SO", "winkel" => 112.5],
    ["bez" => "S", "winkel" => 157.5],
    ["bez" => "SW", "winkel" => 202.5],
    ["bez" => "W", "winkel" => 247.5],
    ["bez" => "NW", "winkel" => 292.5],
    ["bez" => "N", "winkel" => 337.5],
    ); 
 
    foreach ($Windrichtung as $richtung) 
        {
        if ($richtung['winkel'] <= $WindArray[1]) $strBez=$richtung['bez'];
        };
    
    //Sonnenaufgang
    echo "<td><table><tr>";   
    echo "<td style='font-size:11px; color:blue;' align=right>".date('H:i',$SunArray[0])."</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_sunriseset.png' style='margin-bottom:-25px' height=35 width=30></td>";
    // Windgeschwindigkeit
    echo "<td style='font-size:11px; color:blue;' align=right>".floor($WindArray[0])."km/h</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_wind.png' style='margin-bottom:-9px' height=30 title='Windgeschwindigkeit'></td>";
    //aktuelles Wetter
   echo "<td style='font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[0+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_".$WetterHourlyArray[0].".png' style='margin-bottom:-9px' height=30 title='".$WetterHourlyArray[3]."'></td>"; //aktuelles Wetter
        echo "<td style='font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[1+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_".$WetterHourlyArray[1].".png' style='margin-bottom:-9px' height=30 title='".$WetterHourlyArray[4]."'></td>";
        echo "<td style='font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[2+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_".$WetterHourlyArray[2].".png' style='margin-bottom:-9px' height=30 title='".$WetterHourlyArray[5]."'></td>";

    echo "\n<td></td>";

    $stunde = date("G"); //Ab 15 Uhr soll der nächste Tag angezeigt werden
    $ADD = 0;
    if ($stunde > 14) $ADD = 1;

    // Nächste Tageswerte Icons:
    echo "<td style='font-size:11px; color:blue;' align=right>&#8657;".$TempDailyArray[0+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_$WetterDailyArray[0].png' style='margin-bottom:-9px' height=30 title='".$WetterDailyArray[4+$ADD]."'></td>";
    echo "<td style='font-size:11px; color:blue;' align=right>&#8657;".$TempDailyArray[1+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_$WetterDailyArray[1].png' style='margin-bottom:-9px' height=30 title='".$WetterDailyArray[5+$ADD]."'></td>";
    echo "<td style='font-size:11px; color:blue;' align=right>&#8657;".$TempDailyArray[2+$ADD]."&deg;</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_$WetterDailyArray[2].png' style='margin-bottom:-9px' height=30 title='".$WetterDailyArray[6+$ADD]."'></td>";

    echo "<td></td>";

    echo "\n<tr>"; //zweite Zeile

    // aktuelle Werte Bezeichner:    
    //echo "\n<td align=center>".$MapStatus."</td>";
    // Sonnenuntergang
    echo "<td style='border-left:2px solid #000; border-bottom:1px solid #000; font-size:11px; color:blue;' align=right>".date('H:i',$SunArray[1])."</td>";
    echo "\n<td align=left></td>";
    // Windrichtung
    echo "<td style='border-left:1px solid #000; font-size:11px; color:blue;' align=center> " .$strBez. "</td>";
    echo "\n<td align=left><img src='../beelogger_icons/w_windsock.png' style='margin-bottom:-9px' height=30 title='Windrichtung'></td>";
    
    echo "<td style='border-left:2px solid #000; border-bottom:1px solid #000; font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[3]."%</td>";
    echo "\n<td align=left>".$SAs[130]."</td>";
        echo "<td style='border-left:1px solid #000; font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[4]."%</td>";
    echo "\n<td align=left>".date("G",time()+3600).$SAs[131].' </td>';
        echo "<td style='border-left:1px solid #000; font-size:11px; color:blue;' align=right>".$TempHumHourlyArray[5]."%</td>";
    echo "\n<td align=left>".date("G",time()+7200).$SAs[131].' </td>';
    echo "\n<td></td>";
    $tag = date("w");

    
    // Nächste Tageswerte Bezeichner:
        echo "<td style='border-left:2px solid #000; border-bottom:1px solid #000; font-size:11px;";
        if ($TempDailyArray[4+$ADD] > 0) echo " color:blue;";
        else echo " color:red;";
        echo "' align=right>&#8615;".$TempDailyArray[4+$ADD]."&deg;</td>";
    
    if (($tag+$ADD) > 6) $tag -= 7;        
    echo "\n<td>".$SAs[107+($tag+$ADD)].'</td>';

        echo "<td style='border-left:1px solid #000; font-size:11px;";
        if ($TempDailyArray[5+$ADD] > 0) echo " color:blue;";
        else echo " color:red;";
        echo "' align=right>&#8615;".$TempDailyArray[5+$ADD]."&deg;</td>";
    
    if (($tag+$ADD) > 5) $tag -= 7;
    echo "\n<td>".$SAs[107+($tag+1+$ADD)].'</td>';
    
        echo "<td style='border-left:1px solid #000; font-size:11px;";
        if ($TempDailyArray[6+$ADD] > 0) echo " color:blue;";
        else echo " color:red;";
        echo "' align=right>&#8615;".$TempDailyArray[6+$ADD]."&deg;</td>";
    
    if (($tag+$ADD) > 4) $tag -= 7;
    echo "\n<td>".$SAs[107+($tag+2+$ADD)].'</td>';
    echo "\n</tr>";
    echo "\n</table>";
    }
?>
