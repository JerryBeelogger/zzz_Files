<?php

$a=0.1;

$b=0.2;

$c=0.3;

if (($a+$b) == $c) echo "Kein Problem bei php";
else echo ($a+$b);


/*$files = scandir(".");

  $ba=0;
  foreach ($files as $file) 
    {
    if (is_dir($file) && $file != "." && $file != ".." && $file != "archiv"  && $file != "X_beelogger_icons" && $file != "X_beelogger_sprachfiles")
        {
        echo "<br>".$ba.".  ".$file.": ";    
        $filesX = scandir($file);
        foreach ($filesX as $fileX) 
            {
            if (is_dir($file."/".$fileX) && $fileX != "." && $fileX != ".."  && $fileX != "beelogger_icons" && $fileX != "beelogger_sprachfiles")
                {
                if (file_exists($file."/".$fileX."/beelogger_ini.php")) 
                    {
                    $Sensoren[28] = ""; //INIT   
                    include ($file."/".$fileX."/beelogger_ini.php"); // Werte neu einlesen
                    echo "<br>".$fileX."(".$Sensoren[28]."), ";

                    $handle = fopen ($file."/".$fileX."/beelogger_ini.php", "r");
                    $r = 0;
                    while ( $inhalt = fgets ($handle, 4096 ))
                        {
                          $Helparray[$r] = $inhalt;
                          $r++;
                        }
                    fclose($handle);

                    $r = 30;
                    while ($r < sizeof($Helparray)) 
                        {
                        if (strpos($Helparray[$r],"showInRangeSelector: true") !== false) 
                            {
                            echo "SO:".($r).$Helparray[$r];
                            $Helparray[$r] = str_replace("'y2',showInRangeSelector: true", "'y',showInRangeSelector: true",$Helparray[$r]);
                            echo "----> ".$Helparray[$r];
                            break;
                            }
                        $r++;    
                        }

                    $fp = fOpen($file."/".$fileX."/beelogger_ini.php", "w+");
                  foreach($Helparray as $values){ fputs($fp, $values);}
                  fclose($fp);

                    $Helparray = array(); //INIT






                    }
                else echo $fileX."("."NO INI-FIle!!!"."), ";    
                }  
            }
        $ba++;
        }
    }*/

    ?>

