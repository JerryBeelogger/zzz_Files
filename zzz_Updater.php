<?php  // Listbox beelogger
$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_show.php',FALSE, NULL,870 ,23);
echo "Update auf beelogger_show-Version: ";
echo $Version."<br>";

$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_mobileWatch.php',FALSE, NULL,896 ,23);
echo "<br>Update auf beelogger_mobileWatch-Version: ";
echo $Version."<br>";

$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_log.php',FALSE, NULL,856 ,23);
echo "<br>Update auf beelogger_log-Version: ";
echo $Version."<br>";

$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_config.php',FALSE, NULL,856 ,23);
echo "<br>Update auf beelogger_config-Version: ";
echo $Version."<br>";

$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_wetter.php',FALSE, NULL,867 ,25);
echo "<br>Update auf beelogger_wetter-Version: ";
echo $Version."<br>";

$Version = file_get_contents('../../community/zzz_Files/zzz_beelogger_mobileCheck.php',FALSE, NULL,867 ,25);
echo "<br>Update auf mobileCheck-Version: ";
echo $Version."<br>";


$dir = "../../community";
$files = scandir($dir);
natcasesort($files);
foreach ($files as $file) 
  {
  if ($file != "." && $file != ".." && $file != "zzz_Files" && $file != "X_beelogger_icons" && $file != "X_beelogger_sprachfiles" && $file != "archiv" &&  $file != "JerryBee" &&  $file == "JerryMulti")// &&  $file != "JerryBioBee") //
    {
    if (is_dir($file))
      {
      echo "<font SIZE=4 COLOR=GREEN><br><br><b>".$file.": </b></font>";
      $xdir = "../community/".$file;
      $xfiles = scandir($xdir);
      natcasesort($xfiles);
      foreach ($xfiles as $xfile) 
        {
          if ($xfile != "." && $xfile != ".." && $xfile != "beelogger_icons" && $xfile != "beelogger_sprachen")
              {
              if (is_dir("../../community/".$file."/".$xfile))
                  {
                  $ininame = "../../community/".$file."/".$xfile."/beelogger_ini.php";
                  $showname = "../../community/".$file."/".$xfile."/beelogger_show.php";
                  $logname = "../../community/".$file."/".$xfile."/beelogger_log.php";
                  $BeeloggerShowPasswort = "INIT";
                  if (file_exists($ininame)) include($ininame);
                  if ($BeeloggerShowPasswort == "Show") echo '<FONT COLOR="red">'.$xfile.',</FONT> ';
                  else echo "<b>".$xfile.", </b>";


                  if (file_exists("../../community/".$file."/beelogger_config.php")) unlink("../community/".$file."/beelogger_config.php"); //JB


                  
                  if (file_exists($showname)) 
                      {
                      // copy beelogger_show -----------------------------------      
                      copy('../../community/zzz_Files/zzz_beelogger_config.php', "../../community/".$file."/".$xfile."/beelogger_config.php");
                      echo "-C-";

                      copy('../../community/zzz_Files/zzz_beelogger_show.php', "../../community/".$file."/".$xfile."/beelogger_show.php");
                      echo "-S-";

                      copy('../../community/zzz_Files/zzz_beelogger_wetter.php', "../../community/".$file."/".$xfile."/beelogger_wetter.php");
                      echo "-W-";

                      // copy beelogger_mobileWatch -----------------------------------      
                      copy('../../community/zzz_Files/zzz_beelogger_mobileWatch.php', "../../community/".$file."/beelogger_mobileWatch.php");
                      echo "-mW-";

                     // copy beelogger_mobileCheck-----------------------------------      
                      copy('../../community/zzz_Files/zzz_beelogger_mobileCheck.php', "../../community/".$file."/beelogger_mobileCheck.php");
                      echo "-mC-";


                      

                      // eventuell copy beelogger_log-----------------------------------      
                      if (file_exists($logname)) 
                          {
                          copy('../../community/zzz_Files/zzz_beelogger_log.php', "../../community/".$file."/".$xfile."/beelogger_log.php");
                          echo "-L-";
                          }
                      }
                  } 
              }
        }

// copy beelogger_icons -----------------------------------      
        if (file_exists($file."/beelogger_icons")) 
            {
            rrmdir($file."/beelogger_icons");
            echo "- /beelogger_icons geloescht...und wieder";
            }

        if (!file_exists($file."/beelogger_icons"))
          {
          mkdir($file."/beelogger_icons", 0777, true);
          echo " angelegt / ";
          $verzeichnis = opendir ('zzz_Files/zzz_beelogger_icons');
          while ($Xfile = readdir ($verzeichnis))  // Verzeichnis öffnen und auslesen
          {
          if ($Xfile != "." AND $Xfile != "..") copy("zzz_Files/zzz_beelogger_icons/$Xfile",$file."/beelogger_icons/$Xfile"); // Datei kopieren
          }
          closedir($verzeichnis);
          }

// copy beelogger_sprachen -----------------------------------      
        if (file_exists($file."/beelogger_sprachfiles")) 
            {
            rrmdir($file."/beelogger_sprachfiles");
            echo "- /beelogger_sprachfiles geloescht...und";
            }

        if (!file_exists($file."/beelogger_sprachfiles"))
          {
          mkdir($file."/beelogger_sprachfiles", 0777, true);
          echo " angelegt";
          $verzeichnis = opendir ('zzz_Files/zzz_beelogger_sprachfiles');
          while ($Xfile = readdir ($verzeichnis))  // Verzeichnis öffnen und auslesen
          {
          if ($Xfile != "." AND $Xfile != "..") copy("zzz_Files/zzz_beelogger_sprachfiles/$Xfile",$file."/beelogger_sprachfiles/$Xfile"); // Datei kopieren
          }
          closedir($verzeichnis);
          }

      }        
    }
  }

  function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
} 
?>