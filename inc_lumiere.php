<?php

// /////////////////////////////////////////////////////
// Allumage auto de la lumiere au bureau.  //////////////// 
// ////////////////////////////////////////////////////
if ($lastDopActiveState == 1 && $stateDeviceOrdinateur == "off" && $absenceLvl == 0) {
    //sound("51", $muteBDD);
    // on laisse 1 minute avant d'allumer auto la lampe de l'ordi (confort perso ^^)
    if ($autoOnOrdinateur == 1 && $tempsDepuisOrdinateur > 60) {
        sound("45", $muteBDD);
        ordinateur("on");
    }
}

// /////////////////////////////////////////////////////
// Extinction auto de la lumiere au bureau.  //////////////// 
// ////////////////////////////////////////////////////
if ($autoOffOrdinateur == 1){
    if ($tempsDepuisLeDernierMouvement > $absence1 && $stateDeviceOrdinateur == "on" && $lastDopActiveState == 0) {
            ordinateur("off");
            talk("extinction automatique du bureau");
    }
}


// /////////////////////////////////////////////////////
// ////// EXTINCTION DE LA CHAMBRE APRES 1 HEURE //////////
// /////////////////////////////////////////////////////
// eteind les lumieres chambre apres 1 heure sans mouvement dans le cas d'un lvl d'absence sup à 1 .
if ($autoOffChambre ==1) {
    if ($stateDeviceChambre == "on" && $tempsDepuisChambre > 3600 && $absenceLvl < 1) {
         chambre("off");
        talk("extinction de la chambre");
        
       
//        $post = array();
//    $curl = curl_init();
//    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.17/room');
//    curl_setopt($curl, CURLOPT_HEADER, false);
//    curl_setopt($curl, CURLOPT_POST, true);
//    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//    $result = curl_exec($curl);
//    curl_close($curl);
    
    } 
}


// ////////////////////////////////////////////////////
// Extinction aprés auto alumage 
  if ($autoOnChambre == 1) {
        if ($pirChambreState == 0 && $stateDeviceChambre == "on" && $tempsDepuisAutoOnChambre > 30 && $tempsDepuisAutoOnChambre < 45 && $tempsDepuisModeDodo > 3600 && $tempsDepuisReveil > 3600) {
            chambre("off");
        }
  }