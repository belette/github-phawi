<?php
  
if ($lastWifiActiveState == 1 && $absenceLvl > 0) {
    update("interface", array("value" => 0, "depuis" => time()), "id", "abs");
      
    if($absenceLvl > 1){
        sendEmail('Alarme désactivée par WIFI à ' . $heure . 'h' . $minutes . ' ', false, "PHAWI:Alerte desactivée");
    }
    
}

    
 //update("interface", array("value" => 0, "depuis" => time()), "id", "abs");
switch ($absenceLvl) {
    case 0:
   
        // Si il y'a présence de quelquun;
        if ($tempsDepuisLeDernierWifi > $absence1) {
            update("interface", array("value" => "1", "depuis" => time()), "id", "abs");
            if($chauffageAuto == 1 ){
                talk("Coupure automatique du chauffage en cas d'absence prolongé");
                chauffage(0);
                sendEmail("Chauffage auto désactivé", "false", "PHAWI: Chauffage auto OFF");
                tout("off");
            }
        }
        break;

    case 1:
    
        $chauffageAuto = 0;
        if ($tempsDepuisAbsenceLvl > $absence2 && $alarmeAuto == 1) {
            talk ('Pas de wifi depuis '.$absence2.' minutes. passage en surveillance');
            update("interface", array("value" => 2, "depuis" => time()), "id", "abs");
            sendEmail("L'alarme vient d'être activée automatiquement", "false", "PHAWI: Alarme ON");
            tout("off");
        } 
        break;

    case 2:
        $chauffageAuto = 0;
            gpio(23, 1);
            gpio(24, 1);
            gpio(25, 1);
            gpio(24, 0);
            gpio(23, 0);
            gpio(25, 0);
            
        if ($lastDopActiveState == 1 && $lastWifiActiveState == 0) {
            update("interface", array("value" => 3, "depuis" => time()), "id", "abs");
            exec("sudo raspistill -t 500 -o /var/www/html/capture/firstAlarme.jpg -w 640 -h 480");
            update("interface", array("value2" => $nbrAlamre++, "depuis" => time()), "id", "abs");
            sendEmail('ALERTE Un mouvement detecte dans le salon a ' . $heure . 'h' . $minutes . ' <a href=\"http://phawi.ddns.net/capture/firstAlarme.jpg\" >Voir en direct</a>', "true", "PHAWI:ALERTE INTRUSION");
        }
        break;
    
    case 3:
         sound(3);
        $chauffageAuto = 0;
        if ($tempsDepuisAbsenceLvl > 300 && $lastDopActiveState == 0  && $alarmeAuto == 0) {
            update("interface", array("value" => 2, "depuis" => time()), "id", "abs");
        }
        if( $alarmeAuto == 1){
        ambianceLed("on");
        talk("présence détécté");
        sound("1");
        $namePic = date("d-m-Y-H:i:s");
        //sudo raspistill -t  500 -o /var/www/html/capture/alrm_1.jpg  -w 1920 -h 1080
        exec("sudo raspistill -t  500 -o /var/www/html/capture/alrm_".$namePic.".jpg -vf -hf -w 1920 -h 1080");
        //arret aprés 10 mn
        if ($tempsDepuisAbsenceLvl > 300 && $lastDopActiveState == 0 && $tempsDepuisLeDernierMouvement > 300) {
            talk( "extinction auto");
            sendEmail("Auto extinction de l'alarme. timeout 10mn", "true", "PHAWI:ALERTE INTRUSION");
            update("interface", array("value" => 2, "depuis" => time()), "id", "abs");
        }
        ambianceLed("off");
        }
        break;
    default:
    
        break;
}
