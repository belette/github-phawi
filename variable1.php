<?php
include 'inc_fonction.php';
require_once 'conf.php';
$jourDeLaSemaine = date('N');
$mois = date('m');
$annee = date('Y');
$heure = date('H');
$theDate = date("d-m-Y");
$theDateFull = date("d-m-Y H:i:s");
$seconde = date('s');
$jour = date("d");
$time = time();
$minutes = date('i');
$TempsMinuteDeLaJournee = (($heure * 60) + $minutes);
// Calcul de la temperature du CPU: //
$temp =exec("cat /sys/class/thermal/thermal_zone0/temp");
$tempconv = $temp / 1000;
$temppi = round($tempconv, 1);
$temppiSize = round($tempconv, 0);
 

$getInterfaceData = $bdd->query('SELECT * FROM interface');
$arrayKey = array();
$arrayVal = array();
while ($data = $getInterfaceData->fetch()) {
    array_push($arrayKey, $data['id']);
    array_push($arrayVal, array(
        "id" => $data['id'],
        "descr" => $data['descr'],
        "type" => $data['type'],
        "value" => $data['value'],
        "depuis" => $data['depuis'],
        "isDisplay" => $data['isDisplay'],
        "descVal1" => $data['descVal1'],
        "value1" => $data['value1'],
        "descVal2" => $data['descVal2'],
        "value2" => $data['value2'],
        "commandOn" => $data['commandOn'],
        "commandOff" => $data['commandOff']
            )
    );
}
$interfaceData = array_combine($arrayKey, $arrayVal);
$GLOBALS['interfaceData'] = $interfaceData;
$getInterfaceData->closeCursor();

////////////////////////////
$stateDeviceSalon = $interfaceData["salon"]["value"];
$SalonCommandOn = $interfaceData["salon"]["commandOn"];
$SalonCommandOff = $interfaceData["salon"]["commandOff"];
$stateDeviceChambre = $interfaceData["chambre"]["value"];
$timeChambre = $interfaceData["chambre"]["depuis"];
$ChambreCommandOn = $interfaceData["chambre"]["commandOn"];
$ChambreCommandOff = $interfaceData["chambre"]["commandOff"];
$stateDeviceOrdinateur = $interfaceData["ordinateur"]["value"];
$timeOrdinateur = $interfaceData["ordinateur"]["depuis"];
$OrdinateurCommandOn = $interfaceData["ordinateur"]['commandOn'];
$OrdinateurCommandOff = $interfaceData["ordinateur"]['commandOff'];
$modeFilmState = $interfaceData["cinema"]["value"];
$timeFilmMode = $interfaceData["cinema"]["depuis"];
$modeDodoState = $interfaceData["nuit"]["value"];
$timeModeDodo = $interfaceData["ordinateur"]["depuis"];
//
$stateLed = $interfaceData["led"]["value"];
$timeLed = $interfaceData["led"]["depuis"];
$LedCommandOn = $interfaceData["led"]['commandOn'];
$LedCommandOff = $interfaceData["led"]['commandOff'];

$stateCouverture = $interfaceData["couverture"]["value"];
$timeCouverture = $interfaceData["couverture"]["depuis"];
$timerCouverture = $interfaceData["couverture"]["value1"];
$CouvertureCommandOn = $interfaceData["couverture"]["commandOn"];
$CouvertureCommandOff = $interfaceData["couverture"]["commandOff"];

$ChauffageCommandOn = $interfaceData["chauffage"]["commandOn"];
$ChauffageCommandOff = $interfaceData["chauffage"]["commandOff"];

//    
$dateNews = $interfaceData["flash"]["value1"];
//    $salonAllume = $donnees [3] ['valueText'];

$absenceLvl = $interfaceData["abs"]["value"];
$nbrAlamre = $interfaceData["alarme"]["value2"];
$absenceLvlTime = $interfaceData["abs"]["depuis"];
$absence1 = $interfaceData["abs"]["value1"];
$absence2 = $interfaceData["abs"]["value2"];

$tempsCron = $interfaceData["cron"]["depuis"];
$scanList = $interfaceData["wifikey"]["value"];
$userDisplay = $interfaceData["wifikey"]["value1"];
$autoOnOrdinateur = $interfaceData["ordinateurAutoOn"]["value"];
$autoOffOrdinateur = $interfaceData["ordinateurAutoOff"]["value"];
$alarmeAuto = $interfaceData["alarmeAuto"]["value"];
$forceChauffage = $interfaceData["autoChauffage"]["value2"];
$autoOffChambre = $interfaceData["chambreAutoOff"]["value"];
$autoOnChambre = $interfaceData["chambreAutoOn"]["value"];
$timeautoOnChambre = $interfaceData["chambreAutoOn"]["depuis"];
$stateRadio = $interfaceData["radio"]["value"];
$timeRadio = $interfaceData["radio"]["depuis"];
$radioReveil = $interfaceData["radioReveil"]["value"];
$radioTimer = $interfaceData["radioReveil"]["depuis"];

$lastPIRActive = $interfaceData["move"]["depuis"];
$lastPIRActiveState = $interfaceData["move"]["value"];
//    // wifi : 1
$lastWifiActive = $interfaceData["wifi"]["depuis"];
$lastWifiActiveState = $interfaceData["wifi"]["value"];
$wifiKey = $interfaceData["wifikey"]["value"];
//    //FLASH INFO $stateFlash
$stateFlash = $interfaceData["flash"]["value"];
//    // alarme: 2
$lastAlarmeActive = $interfaceData["alarme"]["depuis"];
$lastAlarmeActiveState = $interfaceData["alarme"]["value"]; // etat en mode ALERT !! BIP
$AlarmeActivated = $interfaceData["alarme"]["value"]; // Etat en mode alarme surveillance
$AlarmeNotif = $interfaceData["alarme"]["value1"]; // notif pushover
//    // alarmeForced = 1 pour empecher l'activation automamtique de l'alarme.
$AlarmeForced = $interfaceData["alarmeAuto"]["value"]; // Si on veux que l'alarme s'active en auto ou non
 
 /// CHAUFFAGE 
$TemperatureThermostat = $interfaceData["autoChauffage"]["value1"];
$chauffageAuto = $interfaceData["autoChauffage"]["value"];
$tempsDepuisThermostat = ($interfaceData["chauffage"]["depuis"]);
$StateThermostat = $interfaceData["chauffage"]["value"];
$stateChauffageChambre = $interfaceData["chauffagechambre"]["value"]; 
$tempsChauffageChambre = $interfaceData["chauffagechambre"]["depuis"]; 
// 

// ZERO 
$pirChambreState = $interfaceData["zero"]["value1"];
$tempChambre = $interfaceData["zero"]["value2"]; 
$infoChambre = $interfaceData["zero"]["value"];
$tempsDepuisLeDernierZero = (time() - $interfaceData["zero"]["depuis"]);

$autodetecState = $interfaceData["ordinateurAutoOn"]["value"];
$stateReveil = $interfaceData["reveil"]["value"];
$timeReveil = $interfaceData["reveil"]["depuis"];
$reveilheure = $interfaceData["reveil"]["value1"];
$reveilMinute = $interfaceData["reveil"]["value2"];

$stateSimulateur= $interfaceData["simulation"]["value"];
$tempsDepuisSimulateur= (time() - $interfaceData["simulation"]["depuis"]);

$GLOBALS['muteBDD'] = $interfaceData["mute"]["value"];

$humidity = $interfaceData["humidity"]["value"];
$tempsDepuishumidity = ( time() - $interfaceData["humidity"]["depuis"]);
$pression = $interfaceData["pression"]["value"];
$degre = $interfaceData["tempIn"]["value"];
$outdoorTemp = $interfaceData["tempOut"]["value"];
$weather = $interfaceData["tempOut"]["value1"];
$weatherTime = $interfaceData["tempOut"]["depuis"];
$lastDopActive = $interfaceData["move"]["depuis"];
$lastDopActiveState = $interfaceData["move"]["value"];

/// SERRE 

$serreTemp = $interfaceData["serre"]["value"];
$serreArrosage = $interfaceData["serre"]["value1"];
$pluie = $interfaceData["serre"]["value2"];
$tempsDepuisSerre = (time() - $interfaceData["serre"]["depuis"]);


// ARROSAGE
$arrosageState = $interfaceData["arrosage"]["value"]; // 1 ou 0 
$tempsDepuisArrosage = (time() - $interfaceData["arrosage"]["depuis"]); 

///////////////////////////////////////////////////////////////////////////////////
// calcul des temps
$tempsDepuisLeDernierMouvement = (time() - $interfaceData["move"]["depuis"]);
$tempsDepuisLeDernierWifi = (time() - $interfaceData["wifi"]["depuis"]);
$tempsDepuisLeDernierAlarme = (time() - $interfaceData["alarme"]["depuis"]);
$tempsDepuisAbsenceLvl = (time() - $interfaceData["abs"]["depuis"]);
$tempsDepuisModeDodo = (time() - $interfaceData["nuit"]["depuis"]);
$tempsDepuisThermostat = (time() - $tempsDepuisThermostat);
$tempsDepuisChambre = (time() - $interfaceData["chambre"]["depuis"]);
$tempsDepuisOrdinateur = (time() - $interfaceData["ordinateur"]["depuis"]);
$tempsDepuisCouverture = (time() - $interfaceData["couverture"]["depuis"]);
$tempsDepuisLed = (time() - $interfaceData["led"]["depuis"]);
$tempsDepuisRadio = (time() - $interfaceData["move"]["depuis"]);
$tempsDepuisCron = (time() - $interfaceData["cron"]["depuis"]);
$tempsDepuisWeather = (time() - $weatherTime);
$tempsDepuisAutoOnChambre = (time() - $timeautoOnChambre);
$tempsDepuisReveil = ( time() - $timeReveil);
$tempsDepuisChauffageChambre = ( time() - $tempsChauffageChambre);



$writeData = 1;
if ($writeData == 1) {

    $lineXml = ( '<?php ');
    $lineXml .= ( '$jourDeLaSemaine =' . $jourDeLaSemaine . ';' . "\n");
    $lineXml .= ( '$mois =' . $mois . ';' . "\n");
    $lineXml .= ( '$annee =' . $annee . ';' . "\n");
    $lineXml .= ( '$heure =' . $heure . ';' . "\n");
    $lineXml .= ( '$theDate =' . $theDate . ';' . "\n");
    $lineXml .= ( '$seconde=' . $seconde . ';' . "\n");
    $lineXml .= ( '$jour=' . $jour . ';' . "\n");
    $lineXml .= ( '$time=' . $time . ';' . "\n");
    $lineXml .= ( '$minutes =' . $minutes . ';' . "\n");
    $lineXml .= ( '$TempsMinuteDeLaJournee =' . $TempsMinuteDeLaJournee . ';' . "\n");
    $lineXml .= ( '$temppi="' . $temppi . '";' . "\n");
    $lineXml .= ( '$temppiSize="' . $temppiSize . '";' . "\n");
    $lineXml .= ( '$stateDeviceSalon ="' . $stateDeviceSalon . '";' . "\n");
    $lineXml .= ( '$SalonCommandOn="' . $SalonCommandOn . '";' . "\n");
    $lineXml .= ( '$SalonCommandOff="' . $SalonCommandOff . '";' . "\n");
    $lineXml .= ( '$stateDeviceChambre="' . $stateDeviceChambre . '";' . "\n");
    $lineXml .= ( '$timeChambre="' . $timeChambre . '";' . "\n");
    $lineXml .= ( '$ChambreCommandOn="' . $ChambreCommandOn . '";' . "\n");
    $lineXml .= ( '$ChambreCommandOff="' . $ChambreCommandOff . '";' . "\n");
    $lineXml .= ( '$stateDeviceOrdinateur="' . $stateDeviceOrdinateur . '";' . "\n");
    $lineXml .= ( '$timeOrdinateur=' . $timeOrdinateur . ';' . "\n");
    $lineXml .= ( '$OrdinateurCommandOn ="' . $OrdinateurCommandOn . '";' . "\n");
    $lineXml .= ( '$OrdinateurCommandOff="' . $OrdinateurCommandOff . '";' . "\n");
    $lineXml .= ( '$modeFilmState ="' . $modeFilmState . '";' . "\n");
    $lineXml .= ( '$modeDodoState ="' . $modeDodoState . '";' . "\n");
    $lineXml .= ( '$timeModeDodo=' . $timeModeDodo . ';' . "\n");
    $lineXml .= ( '$stateCouverture="' . $stateCouverture . '";' . "\n");
    $lineXml .= ( '$timeCouverture = 02' . $timeCouverture . ';' . "\n");
    $lineXml .= ( '$timerCouverture = 02' . $timerCouverture . ';' . "\n");
    $lineXml .= ( '$CouvertureCommandOn="' . $CouvertureCommandOn . '";' . "\n");
    $lineXml .= ( '$CouvertureCommandOff="' . $CouvertureCommandOff . '";' . "\n");


    $lineXml .= ( '$LedCommandOn="' . $LedCommandOn . '";' . "\n");
    $lineXml .= ( '$LedCommandOff="' . $LedCommandOff . '";' . "\n");
    $lineXml .= ( '$stateLed="' . $stateLed . '";' . "\n");
    $lineXml .= ( '$timeLed="' . $timeLed . '";' . "\n");


    $lineXml .= ( '$dateNews="' . $dateNews . '";' . "\n");
    $lineXml .= ( '$absenceLvl =' . $absenceLvl . ';' . "\n");
    $lineXml .= ( '$nbrAlamre=' . $nbrAlamre . ';' . "\n");
    $lineXml .= ( '$absenceLvlTime =' . $absenceLvlTime . ';' . "\n");
    $lineXml .= ( '$tempsCron =' . $tempsCron . ';' . "\n");
    $lineXml .= ( '$scanList="' . $scanList . '";' . "\n");
    $lineXml .= ( '$userDisplay="' . $userDisplay . '";' . "\n");
    $lineXml .= ( '$autoOnOrdinateur =' . $autoOnOrdinateur . ';' . "\n");
    $lineXml .= ( '$autoOffOrdinateur =' . $autoOffOrdinateur . ';' . "\n");
    $lineXml .= ( '$alarmeAuto=' . $alarmeAuto . ';' . "\n");
    $lineXml .= ( '$forceChauffage="' . $forceChauffage . '";' . "\n");
    $lineXml .= ( '$autoOffChambre =' . $autoOffChambre . ';' . "\n");
    $lineXml .= ( '$autoOnChambre =' . $autoOnChambre . ';' . "\n");
    $lineXml .= ( '$tempsDepuisAutoOnChambre="' . $tempsDepuisAutoOnChambre . '";' . "\n");
    
    $lineXml .= ( '$stateChauffageChambre="' . $stateChauffageChambre . '";' . "\n");
    $lineXml .= ( '$tempsDepuisChauffageChambre="' . $tempsDepuisChauffageChambre . '";' . "\n");
    
    $lineXml .= ( '$stateRadio=' . $stateRadio . ';' . "\n");
    $lineXml .= ( '$timeRadio=' . $timeRadio . ';' . "\n");
    $lineXml .= ( '$radioReveil=' . $radioReveil . ';' . "\n");
    $lineXml .= ( '$radioTimer=' . $radioTimer . ';' . "\n");
    $lineXml .= ( '$lastWifiActive =' . $lastWifiActive . ';' . "\n");
    $lineXml .= ( '$lastWifiActiveState =' . $lastWifiActiveState . ';' . "\n");
    $lineXml .= ( '$wifiKey="' . $wifiKey . '";' . "\n");
    $lineXml .= ( '$stateFlash=' . $stateFlash . ';' . "\n");
    $lineXml .= ( '$lastAlarmeActive =' . $lastAlarmeActive . ';' . "\n");
    $lineXml .= ( '$lastAlarmeActiveState =' . $lastAlarmeActiveState . ';' . "\n");
    $lineXml .= ( '$AlarmeActivated =' . $AlarmeActivated . ';' . "\n");
    $lineXml .= ( '$AlarmeNotif=' . $AlarmeNotif . ';' . "\n");
    $lineXml .= ( '$AlarmeForced =' . $AlarmeForced . ';' . "\n");
    $lineXml .= ( '$StateThermostat =' . $StateThermostat . ';' . "\n");
    $lineXml .= ( '$TemperatureThermostat=' . $TemperatureThermostat . ';' . "\n");
    //$lineXml .= ( '$TemperatureThermostatLastActive=' . $TemperatureThermostatLastActive . ';' . "\n");
    $lineXml .= ( '$chauffageAuto=' . $chauffageAuto . ';' . "\n");
    $lineXml .= ( '$autodetecState =' . $autodetecState . ';' . "\n");
    $lineXml .= ( '$stateReveil =' . $stateReveil . ';' . "\n");
    $lineXml .= ( '$reveilheure=' . $reveilheure . ';' . "\n");
    $lineXml .= ( '$reveilMinute=' . $reveilMinute . ';' . "\n");
     $lineXml .= ( '$tempsDepuisReveil=' . $tempsDepuisReveil . ';' . "\n");
    $lineXml .= ( '$absence1 =' . $absence1 . ';' . "\n");
    $lineXml .= ( '$absence2 =' . $absence2 . ';' . "\n");
    $lineXml .= ( '$muteBDD =' . $muteBDD . ';' . "\n");
    
    $lineXml .= ( '$stateSimulateur =' . $stateSimulateur . ';' . "\n");  
    $lineXml .= ( '$tempsDepuisSimulateur =' . $tempsDepuisSimulateur . ';' . "\n"); 
            
    $lineXml .= ( '$tempsDepuishumidity=' . $tempsDepuishumidity . ';' . "\n");
    $lineXml .= ( '$humidity=' . $humidity . ';' . "\n");
    $lineXml .= ( '$pression=' . $pression . ';' . "\n");
    $lineXml .= ( '$degre =' . $degre . ';' . "\n");
    $lineXml .= ( '$outdoorTemp =' . $outdoorTemp . ';' . "\n");
    $lineXml .= ( '$weather ="' . $weather . '";' . "\n");
    $lineXml .= ( '$tempsDepuisWeather ="' . $tempsDepuisWeather . '";' . "\n");
    
    $lineXml .= ( '$serreTemp ="' . $serreTemp . '";' . "\n");
    $lineXml .= ( '$pluie ="' . $pluie . '";' . "\n");
    $lineXml .= ( '$serreArrosage ="' . $serreArrosage . '";' . "\n");
    $lineXml .= ( '$tempsDepuisSerre ="' . $tempsDepuisSerre . '";' . "\n");
    
    $lineXml .= ( '$tempsDepuisArrosage ="' . $tempsDepuisArrosage . '";' . "\n");
    $lineXml .= ( '$arrosageState ="' . $arrosageState . '";' . "\n");
        
    
     $lineXml .= ( '$tempChambre ="' . $tempChambre . '";' . "\n"); 
     $lineXml .= ( '$pirChambreState ="' . $pirChambreState . '";' . "\n");
     $lineXml .= ( '$infoChambre ="' . $infoChambre . '";' . "\n");
     $lineXml .= ( '$tempsDepuisLeDernierZero ="' . $tempsDepuisLeDernierZero . '";' . "\n");
    
    $lineXml .= ( '$lastDopActive =' . $lastDopActive . ';' . "\n");
    $lineXml .= ( '$lastDopActiveState="' . $lastDopActiveState . '";' . "\n");
    $lineXml .= ( '$tempsDepuisLeDernierMouvement =' . $tempsDepuisLeDernierMouvement . ';' . "\n");
    $lineXml .= ( '$tempsDepuisLeDernierWifi=' . $tempsDepuisLeDernierWifi . ';' . "\n");
    $lineXml .= ( '$tempsDepuisLeDernierAlarme =' . $tempsDepuisLeDernierAlarme . ';' . "\n");
    $lineXml .= ( '$tempsDepuisAbsenceLvl=' . $tempsDepuisAbsenceLvl . ';' . "\n");
    $lineXml .= ( '$tempsDepuisModeDodo=' . $tempsDepuisModeDodo . ';' . "\n");
    $lineXml .= ( '$tempsDepuisThermostat=' . $tempsDepuisThermostat . ';' . "\n");
    $lineXml .= ( '$tempsDepuisChambre =' . $tempsDepuisChambre . ';' . "\n");
    $lineXml .= ( '$tempsDepuisOrdinateur=' . $tempsDepuisOrdinateur . ';' . "\n");
    $lineXml .= ( '$tempsDepuisCouverture =' . $tempsDepuisCouverture . ';' . "\n");
    $lineXml .= ( '$tempsDepuisRadio=' . $tempsDepuisRadio . ';' . "\n");
    $lineXml .= ( '$tempsDepuisCron =' . $tempsDepuisCron . ';' . "\n");
    
    try {
        $xml = fopen('data.php', 'w+');
        fwrite($xml, $lineXml);
        fclose($xml);
    } catch (Exception $ex) {
        print_r("ERREUR DANS CREATION DE DATA.PHP: " . $ex);
    }
}



$req = $bdd->query('SELECT COUNT(*) as cnt FROM interface');
$cnts = $req->fetch();
$req->closeCursor();
$max = $cnts['cnt'];

$getInterfaceData = $bdd->query('SELECT * FROM interface');
$line = "{";
$j = 0;
while ($data = $getInterfaceData->fetch()) {
    $line .= '"' . $data['id'] . '": {';
    $line .= '"id": "' . $data['id'] . '", ';
    $line .= '"descr": "' . $data['descr'] . '", ';
    $line .= '"type": "' . $data['type'] . '", ';
    $line .= '"value": "' . $data['value'] . '", ';
    $line .= '"depuis": ' . $data['depuis'] . ', ';
    $line .= '"isDisplay": "' . $data['isDisplay'] . '", ';
    $line .= '"descVal1": "' . $data['descVal1'] . '", ';
    $line .= '"value1": "' . $data['value1'] . '", ';
    $line .= '"descVal2": "' . $data['descVal2'] . '", ';
    $line .= '"value2": "' . $data['value2'] . '", ';
    $line .= '"commandOn": "' . $data['commandOn'] . '", ';
    $line .= '"commandOff": "' . $data['commandOff'] . '" ,';
    $line .=  '"vocal": "' . $data['vocal'] . '"';
    $j++;
    if ($j == $max) {
        $line .= '}';
    } else {
        $line .= '},';
    }
}
$line .= "}";
$getInterfaceData->closeCursor();
try {
    $json = fopen('json.json', 'w+');
    fwrite($json, $line);
    fclose($json);
} catch (Exception $ex) {
    print_r("ERREUR DANS CREATION DE JSON: " . $ex);
}