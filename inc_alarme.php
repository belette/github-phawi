<?php

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 0
////////// *** ABSENCE LVL 0 *** ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
         
if ($absenceLvl == 0) {
    if ($lastWifiActiveState == 0 && $lastDopActiveState == 0 &&
            $tempsDepuisAbsenceLvl > $absence1) {
        
        echo("absence de niveau 1");
        salon("off");
        update("interface", array("value" => 1, "depuis" => time()), "id", "abs");
        update("interface", array("value" => 0, "depuis" => time()), "id", "alarme");
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 1
////////// *** ABSENCE LVL 1 *** ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if ($absenceLvl == 1) {
    
    // Algo pour l'activation de la surveillance.
    if ($alarmeAuto == 1) {
       
        if ($lastDopActiveState == 0 && $tempsDepuisAbsenceLvl > $absence2 && $lastWifiActiveState == 0 && $AlarmeActivated == 0) {
            update("interface", array("value" => 2, "depuis" => time()), "id", "abs");
            tout("off");
            sleep(2);
            tout("off");
            // pas de notification d'alarme automatique entre minuit et 7h50 du matin (dodo !! )
            if ($TempsMinuteDeLaJournee > 475 && $AlarmeNotif == "0") {
                sendEmail("L'alarme vient d'être activée automatiquement", false, "PHAWI: Alarme ON");
                talk("Activation automatique de l'alarme. Appartement sous surveillance.");
            }
             update("interface", array("value" => 1, "value1" => 1, "depuis" => time()), "id", "alarme");
        }
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 2
////////// *** ABSENCE LVL 2 *** ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if ($absenceLvl == 2) {
    // /////////////////////////////////////////////////////  
    // ////// DECTECTION CAPTEUR MOUVEMENT Doppler POUR ALARME ////////////
    gpio(23, 1);
    gpio(24, 1);
    gpio(25, 1);
    gpio(24, 0);
    gpio(23, 0);
    gpio(25, 0);
    if ($lastDopActiveState == 1) {
        if ($alarmeAuto == 1 && $tempsDepuisLeDernierAlarme > 60) {
            
            update("interface", array("value" => 3, "depuis" => time()), "id", "abs");
            update("interface", array("value" => 1,  "depuis" => time()), "id", "alarme");
        }
    }
}

;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 3
////////// *** ABSENCE LVL 3 *** ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if ($absenceLvl == 3) {
    // Si alarme state 
    if ($lastAlarmeActiveState == 1) {
        //sound("1");
        // $titre = date('m')."-".date('j')."-".date('G')."-".date('i')."";
         //system('sudo raspistill -w 900 -h 800 -o /var/www/capture/'.$titre.'.jpg -co 60 -br 50 -sa 50 -ex auto -awb incandescent -q 100 -t 10');
        //capturePicture();
        if ($AlarmeNotif == 1) { // on évite d'envoyer la notif a chaques passage de reload.
           // capturePicture();
            //system('sudo raspistill -w 900 -h 800 -o /var/www/capture/'.$titre.'.jpg -co 60 -br 50 -sa 50 -ex auto -awb incandescent -q 100 -t 10');
            // tout("on");  
            echo("pr�sence non autoris� d�t�ct�. signalement envoy�. ");
            //sendEmail('ALERTE Un mouvement d�tect� dans le salon � ' . $heure . 'h' . $minutes . ' <a href=\"http://90.120.224.206/capture/index.php\" >Voir en direct</a>', "true", "PHAWI:ALERTE INTRUSION");
            update("interface", array("value1" => 0), "id", "alarme");
            update("interface", array("value2" => $nbrAlamre + 1), "id", "alarme");
        }
        //$bdd->exec ( 'INSERT INTO histo (titre, photo, time) VALUES("'.$titre.'", "'.$titre.$time.'.jpg", "'.date ( "d-m-Y G-i-s" ).'")' );
         update("interface", array("value1" => 1, "depuis" => time()), "id", "alarme");
    }
    // //////////////////////////////////////////////////////////////b
    // Si l'alarme est en branle depuis plus de 10 mn, arret automatique + envoie de notif.
    if ($tempsDepuisLeDernierAlarme > 500 && $lastAlarmeActiveState == "1") {
        sendEmail("Auto extinction de l'alarme. timeout 10mn", "true", "PHAWI:ALERTE INTRUSION");
        echo("passage en absence de niveau 2. surveillance maintenue");
        update("interface", array("value" => "2", "depuis" => time()), "id", "abs");
        update("interface", array("value1" => "0", "value" => 0, "depuis" => time()), "id", "alarme");
        tout("off");
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// > 0
////////// *** ABSENCE LVL > 0  *** ///////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
if ($lastWifiActiveState > 0) {
    update("interface", array("depuis" => time()), "id", "abs");
    if ($absenceLvl > 0) {
        // /////////////////////////////////////////////////////
        // ////// DECTECTION PRESENCE WIFI /////// / 
        // /////////////////////////////////////////////////////
        // Active la led bleue en cas de Wifi
        tout("off");
        AlarmStop();
        update("interface", array("value" => 0, "depuis" => time()), "id", "abs");
        if ($TempsMinuteDeLaJournee > 475 && $AlarmeNotif == 1) {
            sendEmail('Alarme désactivée par WIFI à ' . $heure . 'h' . $minutes . ' ', false, "PHAWI:Alerte desactivée");
            talk("Vous voilà de retour. Je désactive la surveillance.");
            if ($absenceLvl == 3) {
                talk("Vous voilà de retour. Je désactive la surveillance. Il y a eu " . $nbrAlamre . " alertes enregistrée durant votre absence");
                update("interface", array("value" => 0, "depuis" => time()), "id", "abs");
                echo "REMET A ZERO ";
                update("interface", array("value1" => 0, "depuis" => time()), "id", "alarme");
            }
        }
    }
}


// actualisation de la BDD pour le captage de mouvement Dopler : 
    if ($absenceLvl < 2 && $absenceLvl != 0) {
        if ($absenceLvl != 0) {
            
        }
       // update("interface", array("value" => "0", "depuis" => time()), "id", "abs");
    }
     