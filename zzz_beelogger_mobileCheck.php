<?php

/*
 * (C) 2020 Jeremias Bruker, Thorsten Gurzan, Rudolf Schick - beelogger.de
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


$Softwareversion = "M.15";//vom 16.11.2020
$isHttps = (!empty($_SERVER['HTTPS']));
if($isHttps == 0){
    echo 'Webseite   ';
    echo $_SERVER['SERVER_NAME'];
    echo ' bitte mit https:// aufrufen';
    exit;
}
error_reporting(0);
  $ext_help = htmlentities(strip_tags(stripslashes($_POST['help'])));
  $ext_time = htmlentities(strip_tags(stripslashes($_POST['time'])));
  $ext_graph = htmlentities(strip_tags(stripslashes($_POST['graph'])));
  $ext_beelogger = htmlentities(strip_tags(stripslashes($_POST['beelogger'])));
  if($ext_beelogger=="") $ext_beelogger = htmlentities(strip_tags(stripslashes($_GET['beelogger']))); 
  
  if($ext_help=="") $ext_help = 0;
  if($ext_time=="") $ext_time = 1;
  if($ext_graph=="") $ext_graph = 999;
  
  if($ext_beelogger=="") {
    $dir = "./";
 	 $files = scandir($dir);
 	   natsort($files);
    foreach ($files as $file) {
     if ($file != "." && $file != ".."){
	    if (is_dir("./".$file)) {
	      $filename = "./".$file."/beelogger_interface.php";
		   if (file_exists($filename)) {
		     $ininame = "./".$file."/beelogger_ini.php";
	   		$interfacename = "./".$file."/beelogger_interface.php";         
		   	if (file_exists($ininame) AND file_exists($interfacename)) {
             $ext_beelogger = $file;				    	
			    break;
  	           }
  	         }
        }
      }
    }  
  }
  
  $pfad = "./".$ext_beelogger."/";
  
 if (file_exists($pfad."beelogger_interface.php")) {
   include($pfad."beelogger_interface.php");
 }
 
?>


<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
 
    <link rel="icon" type="image/png" sizes="16x16" href="./beelogger_icons/favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="./beelogger_icons/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="96x96" href="./beelogger_icons/favicon-96x96.png" />    
    
    <link rel="stylesheet" href="./dygraph21.css">
    <title>beelogger mobileCheck</title>
    
    <script type="text/javascript" src="./dygraph21.js" charset="utf-8"></script>
    
    <style>
      select    					{font-size:18px; width:100%; height:25px; border-radius: 10px; text-align: center; background: lightgrey;}
      input[type="submit"]   	{font-size:18px; width:100%; height:25px; border-radius: 10px; text-align: center; background: lightgrey;}
      #headline 					{font-size:20px; font-weight: bold; font-style: italic;}
      #value    					{font-size:18px; font-weight: bold;}
      #date     					{font-size:16px; font-weight: bold;} 
      #delta    					{font-size:16px; font-weight: bold; color: red;} 
      #help     					{font-size:18px;}
      #graph   					{font-size:18px; font-weight: bold;}
    </style>   
    
  </head> 
  <body bgcolor="#ffff87">
     <div id='top'></div>
    <table border="0" width="100%">  
      <tr>
        <td>

		   <table border="0" width="100%">
		     <tr>
		       <td valign="top" align="left" width="1%">
		         <image src="./beelogger_icons/m_beelogger_logo_small.png" height="22">
		       </td>
		      <td valign="top" align="left" width="1%">
		         <div id="headline">mobileCheck</div>
		       </td>
		       <td>&nbsp;</td>
		        <td valign="top" align="right" width="1%">
		          <a href="./beelogger_mobileWatch.php" target="_blank"><img src="./beelogger_icons/watch.png" height="30px"></a>        
		        </td> 
		          <form action="beelogger_mobileCheck.php" method="post">
		        <td valign="top" align="right" width="1%">
		          <?php if($ext_help==1) { ?>
		            <input type='hidden' name='help' value = '0'>
		            <input type="hidden" name="time" value="<?php echo $ext_time?>" /> 
		            <input type="hidden" name="graph" value="<?php echo $ext_graph?>" />
		            <input type="hidden" name="beelogger" value="<?php echo $ext_beelogger?>" />
		            <input type="image" name="submit" src="./beelogger_icons/m_back.png" border="0" style="height: 30px;" />   
		          <?php } else { ?>
		            <input type='hidden' name='help' value = '1'>
		            <input type="hidden" name="time" value="<?php echo $ext_time?>" /> 
		            <input type="hidden" name="graph" value="<?php echo $ext_graph?>" />
		            <input type="hidden" name="beelogger" value="<?php echo $ext_beelogger?>" />
		            <input type="image" name="submit" src="./beelogger_icons/m_help.png" border="0" style="height: 30px;" />  
		          <?php } ?>     
		        </td>
		        </form> 
		     </tr>          
		   </table> 

        </td>
      </tr>
      <tr><td height="2"><hr></td></tr>
      <tr>
        <td>
          <?php 
            if ($ext_help==0) {
          ?>  	

				  <table border="0" width="100%">
				    <tr>
				      <td>
				
				       <?php
						  	
						    $currentTime = new DateTime("now");
						    $updateTime = date_create();
						    date_timestamp_set($updateTime, $sensor_aktualisierung);
							 $currentTime->format("H:i:s d.m.Y");
							 $updateTime->format("H:i:s d.m.Y");		
							 $difference = $currentTime->diff($updateTime); 
							 $days = $difference->format("%d");
							 $hours = $difference->format("%H");
							 $minutes = $difference->format("%I");
							 $seconds = $difference->format("%S"); 
							 
							 $nexttime = ($sensor_aktualisierung + ($sensor_intervall * 60));
							 $nextupdateTime = date_create();
						    date_timestamp_set($nextupdateTime, $nexttime);
						    $nextupdateTime->format("H:i:s d.m.Y");
						    $nextdifference = $nextupdateTime->diff($currentTime); 
							 $nextdays = $nextdifference->format("%d");
							 $nexthours = $nextdifference->format("%H");
							 $nextminutes = $nextdifference->format("%I");
							 $nextseconds = $nextdifference->format("%S");
						    
							 
							 $deltacolor="red";
							 if ($days > 0) { 
							   $aktualisierung = "> 24h";
							   $next_aktualisierung = "> 24h";
							 } else {
						      $aktualisierung = $hours.":".$minutes.":".$seconds; 
						      $next_aktualisierung = $nexthours.":".$nextminutes.":".$nextseconds;
						
						      if (time() < $nexttime) {
						        $deltacolor="green";
						      } 				 
							 } 
						  ?>       
						  
						    <table border="0" width="100%">
						      <tr>
						        <form action="beelogger_mobileCheck.php" method="post">
						        <td>
						          <input type="hidden" name="time" value="<?php echo $ext_time ?>" /> 
						          <input type="hidden" name="graph" value="<?php echo $ext_graph?>" />
						          <input type="hidden" name="beelogger" value="<?php echo $ext_beelogger?>" />
						          <input type="image" name="submit" src="./beelogger_icons/m_refresh.png" border="0" style="height: 30px;" />                 
						        </td>
						        </form>
						        <td><div id="date"><?php echo date("d.m.Y - H:i:s", $sensor_aktualisierung) ?></div></td>
						        <td align="right"><image src="./beelogger_icons/m_delta.png" height="30"></td>
						        <td><div id="delta" style="color:<?php echo $deltacolor ?>;"><?php echo $aktualisierung."<br>".$next_aktualisierung ?></div></td>
						      </tr>          
						    </table> 
				
				      </td>    
				    </tr>  
				    <tr>
				      <td>
				
						 <table border="0" width="100%">
						   <tr>
						     <td width="*"><input type="image" src="./beelogger_icons/m_dia.png"  width="40" onclick="window.location.href='<?php echo $pfad ?>beelogger_show.php'"></input></td>
						     <form action="beelogger_mobileCheck.php" method="post">
						     <td width="100%">
						
									<select name='beelogger' onChange='document.location.href=this.value' title='weiterer beelogger'>
									
									<?php
									$dir = "./";
									 $files = scandir($dir);
									 natsort($files);
									 foreach ($files as $file) {
									    if ($file != "." && $file != ".."){
									      if (is_dir("./".$file))
									       {
									       $filename = "./".$file."/beelogger_interface.php";
									       if (file_exists($filename)) 
									          {
									          	$link = "beelogger_mobileCheck.php?beelogger=".$file;
									            $ininame = "./".$file."/beelogger_ini.php";
									            $interfacename = "./".$file."/beelogger_interface.php";         
									            
									          if ($file == $ext_beelogger) echo '<option style="color:lightgrey;" value='.$link." selected>";
									          else 
									            {
									
									            if (file_exists($ininame) AND file_exists($interfacename)) echo '<option style="color:black;" value='.$link.">";
									            else echo '<option style="color:lightgray;" value='.$link.">";
									            }
									            $Bienenvolkbezeichnung = "auto";    
									            if (file_exists($ininame)) include ($ininame);
									            $ShowFile = str_replace("beelogger","",$file);
									            if ($Bienenvolkbezeichnung != "auto") echo $ShowFile." (".html_entity_decode($Bienenvolkbezeichnung).")";
									            else echo $ShowFile."</option>";
									            
									          }        
									       }
									    }
									  }
									?>              
						       </select>          
						     </td>
						     </form>
						   </tr>
						 </table>
				
				      </td>    
				    </tr>
				   <tr><td height="5"><hr></td></tr> 
				    <tr>
				      <td>
				
						   <table border="0" width="100%">
							   <tr>
							     <td width="10%"></td>
							     <td width="30%" align="center"><img src="./beelogger_icons/m_time.png" height="30"></td>
							     
							     <form action="beelogger_mobileCheck.php" method="post">
							     <td width="30%" align="center">
							       <?php
							         if ($ext_time==1) {                
							           echo"
							             <input type='hidden' name='time' value='24' /> 
							             <input type='hidden' name='graph' value='".$ext_graph."' />
							             <input type='hidden' name='beelogger' value='".$ext_beelogger."' />
							             <input type='image' name='submit' src='./beelogger_icons/m_time1.png' border='0' style='height: 30px;' />
							           ";
							         } 
							         if ($ext_time==24) {                
							           echo"
							             <input type='hidden' name='time' value='1' /> 
							             <input type='hidden' name='graph' value='".$ext_graph."' />
							             <input type='hidden' name='beelogger' value='".$ext_beelogger."' />
							             <input type='image' name='submit' src='./beelogger_icons/m_time24.png' border='0' style='height: 30px;' />
							           ";
							         }                   
							         ?>    
							     </td>
							     </form>
							     
							     <td width="30%" align="center"><img src="./beelogger_icons/m_balance.png" height="30"></td>
							   </tr>   
							   <tr>
							     <td colspan="4"><hr></td>
							   </tr>    
							 <?php          
							   if ($sensor_anzahl > 0) {	
							     for($i=0; $i<=$sensor_anzahl; $i++) {          
							       if (($sensor_icon[$i]!="no.png") and ($sensor_icon[$i]!="")) { 
							       
							         if($ext_time==1) { 
							           $$sensor_wert_x = $sensor_wert_1;
							         }
							         
							         if($ext_time==24) { 
							           $$sensor_wert_x = $sensor_wert_24;
							         }
							
							          $diff = "";
							          if (($sensor_wert[$i]!="") and (is_numeric($sensor_wert[$i]))) { 
							           $diff= round($sensor_wert[$i]-${$sensor_wert_x}[$i],2);                
							       
							           $diffcolor = "black";
							           if (($sensor_wert[$i]-${$sensor_wert_x}[$i]) > 0) $diffcolor = "green";               
							           if (($sensor_wert[$i]-${$sensor_wert_x}[$i]) < 0) $diffcolor = "red"; 
							         }
							         
							           if (($sensor_bezeichnung[$i]!="Status") or ($sensor_icon[$i]!="m_info_1.png")) {  						          
							           
							           if ($i==$ext_graph) $switchgraph = 999;
							           else  $switchgraph=$i;
							           
							           if ($ext_graph==$i) $anker ="top";
							           else $anker ="graph";
							           	
							           echo"           
							           <tr>
							             <form action='beelogger_mobileCheck.php#".$anker."' method='post'>
							             <td width='10%'>
							               <input type='hidden' name='graph' value='".$switchgraph."' /> 
							               <input type='hidden' name='time' value='".$ext_time."' />
							               <input type='hidden' name='beelogger' value='".$ext_beelogger."' />
							               <input type='image' name='submit' src='./beelogger_icons/".$sensor_icon[$i]."' title='".$sensor_bezeichnung[$i]."' border='0' style='height: 30px;' />
							             </td>
							             </form>
							             <td width='30%' align='center'><div id='value'>".$sensor_wert[$i]."</div></td>
							             <td width='30%' align='center'><div id='value'>".${$sensor_wert_x}[$i]."</div></td>
							             <td width='30%' align='center'><div id='value'><font color='".$diffcolor."'>".$diff."</font></div></td>
							           </tr>  
							           <tr>
							             <td colspan='4'><hr></td>
							           </tr>"; 
                                if (($sensor_icon[$i]=="rain.png") and ($rain_for_24!="no")) {
                                
	 							           echo"           
								           <tr>
								             <form action='beelogger_mobileCheck.php#".$anker."' method='post'>
								             <td width='10%'>
								               <input type='hidden' name='graph' value='".$switchgraph."' /> 
								               <input type='hidden' name='time' value='".$ext_time."' />
								               <input type='hidden' name='beelogger' value='".$ext_beelogger."' />
								               <input type='image' name='submit' src='./beelogger_icons/rain_24.png' title='".$sensor_bezeichnung[$i]." 24' border='0' style='height: 30px;' />
								             </td>
								             </form>
								             <td width='30%' align='center'><div id='value'>".number_format($rain_for_24,2,".","")."</div></td>
								             <td width='30%' align='center'><div id='value'>&nbsp;</div></td>
								             <td width='30%' align='center'><div id='value'>&nbsp;</div></td>
								           </tr>  
								           <tr>
								             <td colspan='4'><hr></td>
								           </tr>";                                
                                }							           
							           							           
							           
							         } else {
							           echo"
							           <tr>
							             <td width='10%'><img src='./beelogger_icons/".$sensor_icon[$i]."' title='".$sensor_bezeichnung[$i]."' height='30'></td>
							             <td width='30%' align='center' colspan='3'><div id='value'>".ceil($sensor_wert[$i])."</div></td>
							           </tr>  
							           <tr>
							             <td colspan='4'><hr></td>
							           </tr></table>";                    
							         } 
							       }
							     }  
							                      
							   } else echo "<tr><td colspan='4'>Keine Sensoren aktiviert!</td></tr></table>"; 
						   ?>
				
				      </td>    
				    </tr>  
				    <tr>
				      <td colspan="4">
				        <div id='graph'></div>
				        <?php 
				        	  if (($ext_graph!=999) and ($ext_graph!=888))  { 
				             $ext_sensor=$ext_graph+1;                 
                    ?>

							<div id="beelogger_grafik"></div>
							
							<script type="text/javascript">
							var w = window.innerWidth;  
							var h = window.innerHeight;
							
							<?php
								$ext_file = "./".$ext_beelogger."/week.csv";
								
								if (file_exists($ext_file)) 
								  { //eine week.csv gefunden
								    $array = file($ext_file);
								    $i = sizeof($array);
								    $what = trim($array[$i-1]);
								    $LetzteZeile = explode( ",", $what);
								    $AnzahlSensoren = sizeof($LetzteZeile); //Anzahl an Spalten in der csv-Dat
								  }
							
								echo "h=450;w=w*0.93;";
								echo "w=parseInt(w);";
								echo "\n";
							?>
							
							
							var Graph = new Dygraph(
							  document.getElementById("beelogger_grafik"),
							      <?php if (file_exists($ext_file)) echo "'".$ext_file."'";?>,{
							    	
							      labels: [ 'Datum',<?php for ($s = 0; $s < $AnzahlSensoren-2; $s++){ echo "'".html_entity_decode($Sensoren[$s*5])."',"; } 
							        echo "'".html_entity_decode($Sensoren[$s*5])."'";?>],
							      
							      colors: [<?php for ($s = 0; $s < $AnzahlSensoren-2; $s++){ echo "'".$Sensoren[$s*5+1]."',"; }
							        echo "'".$Sensoren[$s*5+1]."'";?>],
							      
							      visibility: [<?php for ($s = 0; $s <= $AnzahlSensoren-2; $s++)
							        { if (($s+1) ==  $ext_sensor OR !file_exists($ext_file)) echo "true,";
							        else echo "false,"; }?>],
							      
							      <?php echo "xlabel: ''";?>,ylabel:'',y2label:'',
							        
							      series : {
							        <?php for ($s = 0; $s < $AnzahlSensoren-4; $s++){
							        echo "'".html_entity_decode($Sensoren[$s*5])."': { axis: 'y2' },"."\n"; } ?>            },
							
							
							      <?php echo " 
							        axes:{
								     y2: {
								       drawGrid: false,
								       independentTicks: false,
								       labelsKMB: false,
								       axisLabelWidth:60,
								     },";
							 
									 echo "
									    },
									    legend:'never',
									    hideOverlayOnMouseOut: false,
									    labelsSeparateLines: false,
									    strokeWidth:2.0,
									    width: w,
									    height:h,";
									
									    echo"
									    y2label:'".$Sensoren[($ext_sensor-1)*5]." in ".$Sensoren[($ext_sensor-1)*5+4]."',";
							
								// Mobilgeräte
								if (preg_match("/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine
								|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|
								panasonic|philips|phone|playbook|sagem|sharp|sie-|silk|smartphone|sony|symbian|t-mobile|telus
								|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i", $_SERVER['HTTP_USER_AGENT'])) { echo "   interactionModel:{}\n";} 
								else {echo "   interactionModel: Dygraph.defaultInteractionModel\n";}
								
								echo "});";
								echo"Graph";echo".ready(function(){";
								// Mozilla / Firefox
								if (preg_match("/(Firefox)/i", $_SERVER['HTTP_USER_AGENT'])) { 
								echo "setTimeout(function(){Graph";echo"_onresize();}, 200);";}
								else {echo "setTimeout(function(){Graph";echo"_onresize();}, 10);";}
								echo "});\n";
								?>
								
								function <?php echo"Graph" ?>_onresize(){
								 var w = window.innerWidth;
								 var h;
							    h=450;w=w*0.93;
								 w=parseInt(w);
								<?php echo" Graph" ?>.resize(w,h);
								}
							</script>

                   <?php

				           }
				         ?>
				      </td>    
				    </tr>        
				  </table>
          <?php

            } else {
          ?>
				 <table border="0" width="100%">
				 <tr>           
				   <td colspan="3">                     
				     <div id="help"> 
				       beelogger <b>mobileCheck</b> ermöglicht es, speziell mit mobilen Geräten, einen schnellen Blick auf die Sensordaten zu werfen. 
				       Als webbasiertes System bietet es den Komfort einer App, ist jedoch komplett plattformunabhängig und belegt keinen zusätzlichen Speicherplatz. 
				       Viele Systeme bieten die Möglichkeit, einen Shortcut, ähnlich einer App, direkt auf den Desktop zu legen.<br><br>
				       Die Konfiguration von mobileCheck findet zentral innerhalb der Webserver-Skripte statt.<br><br>
				     </div> 
				   </td>
				 </tr>
				 <tr> 
				   <td colspan="3"><hr></td> 
				 </tr>    
				 <tr>
				   <td valign="top"><input type="image" src="./beelogger_icons/m_refresh.png"  height="30"></td>   
				   <td>&nbsp;</td>               
				   <td><div id="help">Zeitstempel des aktuellen Datensatzes. Durch Klick auf das Icon wird die Seite aktualisiert und ggf. ein neuer Datensatz geladen.</div></td>
				 </tr>
				 <tr> 
				   <td colspan="3"><hr></td>               
				 </tr> 
				 <tr>
				   <td valign="top"><input type="image" src="./beelogger_icons/m_delta.png"  height="30"></td>   
				   <td>&nbsp;</td>               
				   <td><div id="help">Verstrichene Zeit seit dem letzten Upload eines Datensatzes in der ersten Zeile. Zeit bis zum geplanten Upload eines Datensatzes in der zweiten Zeile. Wird das geplante Intervall überschritten, wechselt die Anzeigefarbe von Grün nach Rot. In der zweiten Zeile wird dann die Zeit seit dem geplanten Upload angezeigt.</div></td>
				 </tr> 
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr> 
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_dia.png"  width="40"></td>   
				    <td>&nbsp;</td>               
				     <td><div id="help">Auswahl eines beelogger über ein DropDown-Menü zur Anzeige der Daten. Das Icon selbst dient als direkter Link zu den Webserver-Skripten des aktuell ausgewählten beelogger. </div></td>
				  </tr> 
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr> 
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_time.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Markiert die Spalte des aktuellen Datensatzes.</div></td>
				  </tr>   
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>    
				   <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_time1.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Markiert die Spalte des vorletzten Datensatzes, um aktuelle Veränderungen bewerten zu können. Durch Klick auf das Icon wechselt die Spalte zum Datensatz von vor 24 Stunden.</div></td>
				  </tr> 
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>    
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_time24.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Markiert die Spalte des Datensatzes von vor 24 Stunden, um tägliche Veränderungen bewerten zu können. Durch Klick auf das Icon wechselt die Spalte zum vorletzten Datensatz.</div></td>
				  </tr>                   
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>  
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_balance.png"  height="25"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Differenz von aktuellem und für die zweite Spalte ausgewählten Datensatz.</div></td>
				  </tr>   
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>    
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/m_aio.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Markiert die Zeile eines Sensors. Durch Klick auf ein Icon wird der Graph des jeweiligen Sensors ein- bzw. ausgeblendet. Die Icons können individuell in den Webserverskripten zugeordnet werden.</div></td>
				  </tr> 
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr> 				  
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/rain_24.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Wird in den Webserverskripten einem Sensor das Icon "Niederschlag" zugeordnet, wird hiervon automatisch die Summe aller Werte der letzten 24 Stunden berechnet und zusätzlich mit nebenstehendem Icon aufgeführt. </div></td>
				  </tr>				  
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>                    
				  <tr>
				    <td valign="top"><input type="image" src="./beelogger_icons/watch.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Link zum beelogger-mobileWatch</div></td>
				  </tr> 
				    <td colspan="3"><hr></td>               
				  </tr>                    
				  <tr>
				   <td valign="top"><input type="image" src="./beelogger_icons/like.png"  height="30"></td>   
				    <td>&nbsp;</td>               
				    <td><div id="help">Nutzer unseres Community-Servers erreichen den beelogger-mobileCheck auch über die Kurz-URL http://c.beelogger.de/"benutzername" ("benutzername" ist hierbei durch den eigenen Benutzernamen zu ersetzen).</div></td>
				  </tr>                                     
				  <tr> 
				    <td colspan="3"><hr></td>               
				  </tr>                                                                                                                                                                                              
				</table> 
            	
          <?php  	
            }	
          ?>      
        </td>      
      </tr>
    </table>
  </body>