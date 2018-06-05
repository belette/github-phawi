<?php

// /////////////////////////////////////////////////////
// ////// EXTRA ET AUTRES ACTIONS DIVERS ///////////////
// /////////////////////////////////////////////////////
// ////// DIT BONJOUR 1800 /////////////////////////////
// /////////////////////////////////////////////////////

if ($tempsDepuisLeDernierMouvement > 1700 &&
        $lastDopActiveState == 1 && $lastAlarmeActive == 0 && $lastAlarmeActive < 160) {
    if ($TempsMinuteDeLaJournee > 420) {
        Execute("mpg321 /var/www/html/sound/button-37.mp3");
        talk(" bonsoir benoit, vous êtes de retour, j'espère que vous allait bien");
    }
}

// ///////////////////// ////////////////////////////////////
// REVEIL : & RADIO  //////////////////////////////////////////////
reveil($reveilheure, $reveilMinute, $stateReveil, $radioReveil);
if ($tempsDepuisRadio > $radioTimer && $stateRadio == 1) {
    radio(0, $radioReveil);
}


// ALERTE EN CAS DE SURCHAUFFE DU CPU
if ($temppiSize > 59 && $temppiSize < 65) {
    talk("Le processeur atteind une température trop élevée. " . $temppiSize . " degrés");
    sendEmail("CPU en surchauffe : température relevée à " . $temppiSize . "C ", false, "PHAWI: ALERTE CPU");
}
if ($temppiSize > 65) {
    talk("Le processeur en surchauffe. extinction de la carte mère pour sécuritée. ");
    sendEmail("CPU en surchauffe : EXTINCTION DU RASPBERRY : température relevée à " . $temppiSize . "C ", false, "PHAWI: ALERTE CPU");
    Execute("halt");
}



// Alert l'utilisateur si la cron tab n'a pas tourné depuis un moment
// 
if ($tempsDepuisCron > 600) {
//     talk("Lancement de la tâche cron tabe, environnement en cour de scan");
//     sendEmail("La crontab a été inactif pendant ".$tempsDepuisCron."sec ", false, "PHAWI: Crontab inactif");
//     Execute("killall python");
//     Execute("python /var/www/html/longTask.py");
//     talk("Scanner terminé");
}

// SIMULATEUR DE PR2SENCE
if ($stateSimulateur == 1) {

    // LEd salon
    if ($heure > 20) {
        if ($stateLed == "off") {
            ambianceLed("on");
            croquette();
        }
//talk($heure);
        if ($tempsDepuisSimulateur > 120) {
            update("interface", array("depuis" => time()), "id", "simulation");
            $randomAction = rand(1, 3);
            if ($randomAction == 1) {
                if ($stateDeviceSalon == "off") {
                    salon("on");
                } else {
                    salon("off");
                }
            } else if ($randomAction == 2) {
                if ($stateDeviceChambre == "off") {
                    chambre("on");
                } else {
                    chambre("off");
                }
            } elseif ($randomAction == 3) {
                if ($stateDeviceOrdinateur == "off") {
                    ordinateur("on");
                } else {
                    ordinateur("off");
                }
            }
        }
    } else {
        if ($stateLed == "on") {
            ambianceLed("off");
        }
    }
}




// Arret auto de l'arrosage aprés 15 minutes.
if ($arrosageState == "1" && $tempsDepuisArrosage > 900) {
    arrosage(0);
}
