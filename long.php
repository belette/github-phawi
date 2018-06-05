<?php

//error_reporting(0);

require 'conf.php';
include 'variable1.php';

// MAJ du compteur de cron ////////////
update("interface", array("depuis" => time()), "id", "cron");
// pour exec la commande : su root -c 'sudo python /var/www/html/longTask.py >> /var/www/html/phawi.log 2>&1 &'

update("interface", array("value" => $temppiSize), "id", "tempPi");
/////////////////////////////////////
// setup des gpio : *****************
exec("gpio mode 23 out"); // LED chauffage
exec("gpio mode 24 out"); // LED PIR
exec("gpio mode 25 out"); // LED WIFI
exec("gpio mode 6 out"); // gpio du relai 5v / 230 
///////////////////////////////////////////////////////
// INSCIPTION EN BDD DU MOUVEMENT + TRAITEMENT LED ROUGE
///////////////////////////////////////////////////////
 
//satelite 2  tous les 60 secondes
if (exec("ping -c1 -W1 192.168.1.16 | grep -cw '1 received' ") === "1" && $tempsDepuishumidity > 60) {
    $satData2 = getSatData2();
    if ($satData2 != null && $satData2 !== "") {
        $satData2Split = explode(":", $satData2);
        $readDopler = $satData2Split[0];
        $temp2 = (round($satData2Split[1], 1) );
        $humidity = (round($satData2Split[2], 1) );
        update("interface", array("value" => $humidity, "depuis" => time()), "id", "humidity");
    }
}

// Satelite 1 
if (exec("ping -c1 -W1 192.168.1.20 | grep -cw '1 received' ") === "1") {
    $satData = getSatData();
    if ($satData != null && $satData !== "") {
        $satDataSplit = explode(":", $satData);
        $readPir = $satDataSplit[0];
        $tempIn = (round($satDataSplit[1], 1) - 1);
    }
    
    if ( $readPir != $lastDopActiveState) {
        // LEd  Mouvement 
        gpio(24, $readPir);
        update("interface", array("value" => $readPir, "depuis" => time()), "id", "move");
    }
    /////////////////////////////////////sudo python /var/www/sensor/examples/simpletest.py
    // RENTRAGE DES DONNEES DANS LA BDD:
    if ($tempIn > 0) {
        update("interface", array("value" => $tempIn), "id", "tempIn");
    }
}else{
   // sound(43);
}
 


// update data de la serre
//readSerre(false);
// getChambreData(); 
//////////////////////////////////////
///// Traitement meteo  et serre //////////////
// on ne refresh la meteo que toutes les heures.
    if ($tempsDepuisWeather > 3600) {
        if (exec("ping -c1 -W1 192.168.1.14 | grep -cw '1 received' ") === "1") {
          Weather($outdoorTemp, $weather);
          readSerre(true);
        }
    }
    if ($tempsDepuisSerre > 300) {
        if (exec("ping -c1 -W1 192.168.1.14 | grep -cw '1 received' ") === "1") {
           readSerre(false);
        }
    }
/////////////////////////////////////
///////////////////REVEIL////////////
reveil($reveilheure, $reveilMinute, $stateReveil, $radioReveil);

///////////////Flash INFO/////////////
if ($stateFlash === 1) {
    lastNews($dateNews);
}



///////////////////////////////////////
////LECTURE NMAP WIFI/////////////////
if($readPir !== 1 && $absenceLvl == 0){
$nbrCon = 0;

$userNameList = "";
$getUserInfoReq = $bdd->query('SELECT * FROM user WHERE master="1"');
while ($dataUser = $getUserInfoReq->fetch()) {
    $readWifi = 0;
    $cmdNmap = ("sudo nmap -n -sn " . $dataUser['ip'] . " -oG - | grep -cw '1 host up' ");
    $scanMe = exec($cmdNmap);
    if ($scanMe === "1") {
        $readWifi = 1;
        $userNameList .= $dataUser['login'] . " ";
        $nbrCon++;
    }

    // si l'état du wifi du user est != 
    if ($dataUser['state'] != $readWifi) {
        // temps en seconde avant le changement 
        $tempsAvantretour = time() - $dataUser['depuis'];

        // message de bienvenue
        if ($tempsAvantretour > 3600 && $readWifi == 1 && $dataUser['state'] == 0) {
            talk("Bienvenue à la maison " . $dataUser['login'] . "");
        }
        update("user", array("state" => $readWifi, "depuis" => time()), "login", $dataUser['login']);
    }
}


if ($lastWifiActiveState != $nbrCon) {
    update("interface", array("value" => $nbrCon, "depuis" => time()), "id", "wifi");
    gpio(25, $readWifi);
}
update("interface", array("value1" => $userNameList), "id", "wifi");

}
////////////Gestion include INTERNE///////////
include 'inc_absence.php';
require_once 'inc_lumiere.php';
require_once 'inc_diver.php';
require_once 'inc_chauffage.php';

///////////////////////////////////// 
/////////////////////////////////////
// LED BLEU pour WIFI  ///////////////////
/////////////////////////////////////
/////////////////////////////////////
////////////////////////////////////////////////////////////////
// DATA GRAPHIQUE TEMP
// on selection le temps de la derniere entrée de donnée
// et on conditionne à 600 (soit 10 mn)  l'entrée d'une nouvelle donnée. 
//$req = "SELECT depuis FROM `interne` ORDER by id desc LIMIT 1;";
//$result = $bdd->query($req);
//$f = $result->fetch();
//$lastInsertData = time() - ($f[0]);
//if ($lastInsertData > 1200) {
//    insert("interne", array(
//        "depuis" => time(),
//        "theDate" => $theDateFull,
//        "inTemp" => $degre,
//        "outTemp" => $outdoorTemp,
//        "humidity" => $humidity,
//        "pression" => $pression,
//        "thermostatTemp" => $TemperatureThermostat
//    ));
//}
//vérification crontab en cours
if ($tempsDepuisCron > 300) {
    
     sound(9);
//    exec("sudo killall python");
//    sleep(3);
//    exec("sudo python /var/www/html/longTask.py &");
//    talk("la maison est maintenant sous surveillance");
}
?>
