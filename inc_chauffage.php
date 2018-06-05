<?php

// ///////////////////////////////////////////////////////////////////////////////////////
// ////// TEMPERATURE SONDE DHT22 Exterieur //////// /////////////////////////////////////
// ///// Voir http://www.manuel-esteban.com/lire-une-sonde-dht22-avec-un-raspberry-pi/////
// /////n'affiche que si le rï¿½sultat de la prise de temperature renvoi un format correct./
// GESTION DU CHAUFFAGE  ///////////////////////////////////////////////////
// Explication de la table chauffage :
// "description" => Température détécté par la sonde 
// "state" => (1/0) définis si le MODE chauffage est en marche. *** $StateThermostat
// "lastActive" => (time() ) time() du dernier appel
// "activated" => (1/0) = is running => 1 sii le chauffage chauffe a cet instant *** $TemperatureThermostatActivated
// "notif" => Température de réglage du chauffage
////////////////////////////////////////////////////////////////////////
  
 $delai = 120;
 
if ($forceChauffage == 1) {
    sound(("3"));
    chauffage(1);
    update("interface", array("value" => 1, "depuis" => time()), "id", "chauffage");

    // ajustement chauffage chambre  en priorité sur le chauffage // 
} else if ($stateChauffageChambre == 1) {
    
    ///////////// REGLAGE AUTO CHAMBRE CHAUFFAGE (:HORS SALON) /////
    // on désactive le chauffage auto .
    if ($tempsDepuisThermostat > $delai) {
       
        // si la temp est inferieur au réglage du chauffage et que le chauffage est etaind : 
        if ($tempChambre < $TemperatureThermostat) {
        
            if ($StateThermostat == 0) {
                talk("Ajustement de la température de la chambre. réglé à " . $TemperatureThermostat . " degrés.");
                chauffage(1);
                update("interface", array("value" => 1, "depuis" => time()), "id", "chauffage");
            }
        } else {
            if ($StateThermostat == 1 && $tempChambre > ($TemperatureThermostat + 0.3)) {
                // Si la température ammbiente est supérieure au chauffage 
                // on coupe le chauffage:
                talk("Extinction automatique du chauffage, la chambre est désormais à " . $tempChambre . " degrés.");
                chauffage(0);
                update("interface", array("value" => 0, "depuis" => time()), "id", "chauffage");
            }
        }
    }
} else {
    /// REGLAGE AUTO SALON  (HORS CHAMBRE ) //////
    // si réglage en auto activé : 
    if ($chauffageAuto == 1 && $tempsDepuisThermostat > $delai) {
        // si la temp est inferieur au réglage du chauffage et que le chauffage est etaind : 
        if ($degre < $TemperatureThermostat) {
          
            if ($StateThermostat == 0) {
                talk("Activation du chauffage. Température de ".$degre." degré. Réglage à " . $TemperatureThermostat . " degrés.");
                chauffage(1);
                //
                //  sendEmail("Le chauffage automatique vient d'être activé. température dans le salon :" . $degre . "/". $TemperatureThermostat. "°C ", false, "PHAWI: Chauffage ON");
                update("interface", array("value" => 1, "depuis" => time()), "id", "chauffage");
            }
        } else {
            if ($StateThermostat == 1 && $degre > ($TemperatureThermostat + 0.3)) {
                
                // Si la température ammbiente est supérieure au chauffage 
                // on coupe le chauffage:
                talk("Extinction automatique du chauffage");
                chauffage(0);
                update("interface", array("value" => 0, "depuis" => time()), "id", "chauffage");
                // sendEmail("Le chauffage automatique vient d'être désactivé. température dans le salon " .$degre . "/".$TemperatureThermostat . "°C ", false, "PHAWI: Chauffage OFF");
            }
        }
    } else if ($chauffageAuto == 0 && $StateThermostat == 1) {
        chauffage(0);
        update("interface", array("value" => 0, "depuis" => time()), "id", "chauffage");
    }
}
 
// lancer l'allumage automatique du chauffage si la temperature exterieure prévue 
// est inferieur à 16 degrés.
if ($outdoorTemp < 17 && ($degre - $outdoorTemp) > 6 && $chauffageAuto != 1) {
//    sendEmail("Activation du mode automatique du chauffage. Température exterieure prévue : " .$outdoorTemp . " °C ", false, "PHAWI: Chauffage ON");
//    update("interface", array("value" => 1), "id", "autoChauffage");
}

if ($StateThermostat == 1 && $tempsDepuisThermostat > $delai && $tempsDepuisThermostat < ($delai * 2)) {
    chauffage(1);
} else if ($StateThermostat == 0 && $tempsDepuisThermostat > $delai && $tempsDepuisThermostat < ($delai * 2)) {
    chauffage(0);
}



 
  


// sécuité un peu bidon contre les feux ou les problèmes de surchauffe : on alerte quand la temp
// à l'interieur est > 31 degres (ca commence à chauffer déja !

if ($degre > 31 && $degre < 40 && $degre > 41) {
    sendEmail("ALERTE TEMPERATURE EXCESSIVE  : " . $degre . "C ! ", false, "PHAWI: Chauffage ALERTE");
    update("interface", array("value" => 0, "depuis" => time()), "id", "chauffage");
    chauffage(0);
}


// Sécurité chauffage , si durée en marche trop longue. aprés 7200 seconde soit 2 heures.
if ($StateThermostat == 1 && $tempsDepuisThermostat > 7200) {
    // Execute("gpio write 6 0"); // Forcage au cas ou 
//    update("interface", array("value" => 0, "depuis" => time()), "id", "chauffage");
//    chauffage(0);
}

