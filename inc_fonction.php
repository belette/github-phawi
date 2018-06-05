<?php

include "data.php";

//
// mpg321 -a bluealsa:HCI=hci0,DEV=00:11:67:74:12:0D,PROFILE=a2dp 12.mp3 
// commandes :  bluetoothctl, scan on, agent  on, pair XX:XX:XX:XX:XX; trust 
//  afficher la device Bluetooth:  busctl tree org.bluez
//  
//
//
// //////////////////////////////////////////////////////////////////////////
// Fonction qui fait parler Cortana
// @PARAMS : Son text en brut mais en etudiÃ© pour sa prononciation tl=fr&q=%C3%A9
// principalement appelÃ© via Action.php grace a action("repond","texte a dire en clair");
// //////////////////////////////////////////////////////////////////////////
// https://translate.google.fr/translate_tts?ie=UTF-8&q=%C3%A9&tl=fr&total=1&idx=0&textlen=1&client=t&prev=input
//////////////////////////////////////////////////////////////////////////
// FONCTION Allumage / Extinction des lampes
// @Params : salon($onOff) => 'on' ou 'off'
// PS; Le relai du salon est nouvelle gÃ©nÃ©ration et est envoyÃ© par 433 utils
// Les codes sont obtenu par hacking du signal Chacon.
// Le code compilÃ© dans /var/www/html/433Utils/RPI_utils est compilÃ© 
// avec en param : Sniffer en GPIO 4 et TxD en GPIO 22. Ã  respecter sinon recompil totale... 
// 
// codes : 
// NUMBER :            ON                OFF
// 1            1381717                 1381716
//              1398103                 
// 2            1381716                 1394004
//              1394005
// 3            1397077                 1397076
// 4            1397845                 1397844
// $prise2_On = 'sudo /var/www/html/433Utils/RPi_utils/codesend 1394005';
//   $prise2_Off = 'sudo /var/www/html/433Utils/RPi_utils/codesend 1394004';
// //////////////////////////////////////////////////////////////////////////
function salon($onOff) {
    //double envoi de la commande de prise (car prise difficile a comprendre) 
    if ($onOff == "on") {
        ExecuteRF($GLOBALS['SalonCommandOn']);
        //Execute('sudo python3 cmdSender.py "sudo -b /var/www/html/433Utils/RPi_utils/codesend 1381717" ');
    } else {
        ExecuteRF($GLOBALS['SalonCommandOff']);
        // Execute('sudo python3 cmdSender.py "sudo -b /var/www/html/433Utils/RPi_utils/codesend 1381716" ');
    }
    update("interface", array("value" => $onOff, "depuis" => time()), "id", "salon");
}

function chambre($onOff) {
    if ($onOff == "on") {
        ExecuteRF($GLOBALS['ChambreCommandOn']);
    } else {
        ExecuteRF($GLOBALS['ChambreCommandOff']);
    }
    update("interface", array("value" => $onOff, "depuis" => time()), "id", "chambre");
}

function ordinateur($onOff) {
    //Execute(' python3 cmdSender.py " /var/www/html/radioEmission 22 12325261 3 ' . $onOff .'" ');
    if ($onOff === "on") {
        ExecuteRF($GLOBALS['OrdinateurCommandOn']);
    } else {
        ExecuteRF($GLOBALS['OrdinateurCommandOff']);
    }
    update("interface", array("value" => $onOff, "depuis" => time()), "id", "ordinateur");
}

function ambianceLed($onOff) {
    if ($onOff == "on") {
        ExecuteRF($GLOBALS['LedCommandOn']);
    } else {
        ExecuteRF($GLOBALS['LedCommandOff']);
    }
    update("interface", array("value" => $onOff, "depuis" => time()), "id", "led");
}

function tout($onOff) {
    chambre($onOff);
    ordinateur($onOff);
    salon($onOff);
    ambianceLed($onOff);
}

function croquette() {
    $post = array(
        '' => '',
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.17/croquette');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    echo '$result';
    sendEmail("The cat had some croquettes :" . $result, false, "PHAWI: Croquettes !");
}

function chauffage($state) {
    gpio(23, $state);
    if ($state == 1) {
        ExecuteRF($GLOBALS['ChauffageCommandOn']);
        ExecuteRF($GLOBALS['ChauffageCommandOn']);
        ExecuteRF($GLOBALS['ChauffageCommandOn']);
    } else {
        ExecuteRF($GLOBALS['ChauffageCommandOff']);
        ExecuteRF($GLOBALS['ChauffageCommandOff']);
        ExecuteRF($GLOBALS['ChauffageCommandOff']);
    }
}

function openDoor() {
    $open = '/var/www/html/433Utils/RPi_utils/codesend 1230';
    ExecuteRF($open);
}

function soundRoom() {
    if ($GLOBALS['stateDeviceChambre'] == "on") {
        chambre("off");
    } else {
        chambre("on");
    }
}

function talk($text) {
    if ($GLOBALS['muteBDD'] != 1 &&
            date("H") < 24 &&
            date("H") > 7) {

        $rtrn = acapela($text);
    }
    return $text;
}

function Execute($cmd) {
    // gpio(23, 1);
    exec("sudo " . $cmd . " > /dev/null");
    // gpio(23, 0);
}

function ExecuteRF($cmd) {
    //  gpio(23, 1);
    exec("sudo " . $cmd . " > /dev/null");
    // gpio(23, 0);
}

function chambreAuto($statePir) {
    $tempChambre = 0;
    if ($GLOBALS['autoOnChambre'] == 1) {
        if ($statePir == 1 && $GLOBALS['stateDeviceChambre'] == "off") {
            // sound(3);
            chambre("on");
            $rtrn = "on";
        } else if ($statePir == 0 && $GLOBALS['stateDeviceChambre'] == "on" && $GLOBALS['tempsDepuisModeDodo'] < 3600) {
            chambre("off");
            $rtrn = "off";
        }
    }

    if (intval($statePir) !== intval($GLOBALS['pirChambreState'])) {
        update("interface", array("value" => $statePir . ":" . $tempChambre, "value1" => $statePir, "value2" => $tempChambre, "depuis" => time()), "id", "zero");
        update("interface", array("depuis" => time()), "id", "chambreAutoOn");
    } else {
        update("interface", array("value" => $statePir . ":" . $tempChambre, "value1" => $statePir, "value2" => $tempChambre), "id", "zero");
    }

    if ($GLOBALS['tempsDepuisModeDodo'] < 3600 && $GLOBALS['stateDeviceChambre'] == "on") {
        echo "on";
    } else {
        echo "off";
    }
}

function getSatData() {

    $post = array();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.20/sat');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function openFrontDoor() {
    $post = array();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.16/door');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function getSatData2() {
    $post = array();
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.16/sensor');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    if ($result != null && $result !== "") {
        $satDataSplit = explode(":", $result);
        $readDop = $satDataSplit[0];
        $readTemp1 = (round($satDataSplit[1], 1));
        $readHumidity = (round($satDataSplit[1], 1));
    }
    return $result;
}

///////////////////
//fonction qui appel via CURL l'api de Voice RSS. Peut etre 
// deprecated par le proprietaire dans l'avenir. on ne sais jamais. 
//wget -q -U Mozilla -O output.mp3 "http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=tw-ob&q=champignon&tl=Fr-fr"
//wget -q -U Mozilla -O acapea.mp3 "http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=tw-ob&q=champignon&tl=Fr-fr
//echo curl "http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=tw-ob&q=champignon&tl=Fr-fr"
//key=769978c9c740482b898f7f377422cfbb&hl
//curl --data http://translate.google.com/translate_tts?ie=UTF-8&total=1&idx=0&textlen=32&client=tw-ob&q=champignon&tl=Fr-fr"
function acapela($textToSpeak) {
//    Execute("  gpio write 21 1");
//    $fileName = "acapela" . md5($textToSpeak) . ".mp3";
//    if (!file_exists('/var/www/html/sound/' . $fileName)) {
//        Execute(' curl -o /var/www/html/sound/' . $fileName . ' '
//                . '"https://api.voicerss.org?key=769978c9c740482b898f7f377422cfbb&hl=fr-fr&src=' . urlencode($textToSpeak) . '&r=0&c=mp3&f=44khz_16bit_stereo&ssml=false&b64=false" >/dev/null 2>&1');
//    }
//
//    Execute('mpg321 -q /var/www/html/sound/' . $fileName . '  >/dev/null 2>&1');
//    Execute("  gpio write 21 0");
    $post = array(
        'text' => $textToSpeak,
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.71/get_phawi.php');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function tts($text) {
    $fileName = "acapela" . md5($text);
    if (!file_exists("/var/www/html/sound/" . $fileName)) {
        Execute(' curl -o /var/www/html/sound/' . $fileName . '.mp3 "https://api.voicerss.org?key=769978c9c740482b898f7f377422cfbb&hl=fr-fr&src=' . urlencode($text) . '&r=0&c=mp3&f=44khz_16bit_stereo&ssml=false&b64=false" ');
    }
    $url = 'sound/' . $fileName . '.mp3';
    Execute(' mpg321 -q' . $url);
    return $url;
}

function talkEng($textToSpeak) {
    $fileName = "acapela" . md5($textToSpeak);
    if (!file_exists("/var/www/html/sound/" . $fileName)) {
        Execute(' curl -o /var/www/html/sound/' . $fileName . '.mp3 "https://api.voicerss.org?key=769978c9c740482b898f7f377422cfbb&hl=en-us&src=' . urlencode($textToSpeak) . '&r=0&c=mp3&f=44khz_16bit_stereo&ssml=false&b64=false" ');
    }
    Execute(' mpg321 -q /var/www/html/sound/' . $fileName . '.mp3 > /dev/null &');
}

// DEPRECATED !!!
function speak($text) {
    $fileName = "acapela" . md5($textToSpeak);
    Execute("  gpio write 21 1");
    if (!file_exists("/var/www/html/sound/" . $fileName)) {
        Execute(' curl -o /var/www/html/sound/' . $fileName . '.mp3 "https://api.voicerss.org?key=769978c9c740482b898f7f377422cfbb&hl=en-us&src=' . urlencode($textToSpeak) . '&r=0&c=mp3&f=44khz_16bit_stereo&ssml=false&b64=false');
    }
    Execute(' mpg321 -q  /var/www/html/sound/' . $fileName . '.mp3 > /dev/null &');
    Execute("  gpio write 21 0");
}

// fonction utilisé pour enregistrer un snapshot qui se nomme automatiquement 
// sous la forme date +"%Y-%m-%d_%H%M%S" (avec seconde pour éviter l'overwrite)
function capturePicture() {
    //Execute(" raspistill -w 900 -h 800 -o /var/www/html/capture/$DATE.jpg -co 60 -br 50 -sa 50 -ex auto -awb incandescent -q 100 -t 10");
    $namePic = date("d-m-Y_H:i:s");
    exec("sudo raspistill -t  500 -o /var/www/html/capture/capture" . $namePic . ".jpg -vf  -w 1920 -h 1080");
    return "capture" . $namePic . ".jpg";
}

// fonction utilisé pour enregistrer un snapshot qui s'overwrite (renommé chaque fois stream.jpg
function captureStream() {
    //Execute(" raspistill -w 900 -h 800 -o /var/www/html/capture/test.jpg -co 60 -br 50 -sa 50 -ex auto -awb incandescent -q 100 -t 10");
    // pour forcer le  rechargement de l'image on lui donne un nom uniue. 
    // mais pour éviter la surcharge de données, on supprmie toutes les photos présentes dans le dossier Stream.
    Execute("rm /var/www/html/capture/stream/*.jpg");
    $namePic = date("d-m-Y_H:i:s");
    //exec("sudo raspistill -t  500 -o /var/www/html/capture/stream/capture" . $namePic . ".jpg   -w 1920 -h 1080");
    exec("sudo raspistill -t 200 -o /var/www/html/capture/stream/capture" . $namePic . ".jpg   -w 1920 -h 1080");
    return "stream/capture" . $namePic . ".jpg";
}

// fonction qui retourne les fichier présents dans le dossier capture/
// doit etre appelé depuis un fichier en root www/html/
function dirToArray($dir) {
    $result = array();
    $cdir = scandir($dir);
    $rtrn = '<div id="contentHisto">';
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                $rtrn .= ('<li><a id="imgCapture" onClick="(bigSizePhoto(\'capture/' . $value . '\'))" href="capture/' . $value . '">' . $value . '"</a></li>');
            } else {
                $result[] = $value;
                $rtrn .= ('<li onClick="(bigSizeHisto(\'capture/' . $value . '\'))" href="capture/' . $value . '">' . $value . '"</li>');
            }
        }
    }
    $rtrn .=("</div>");
    return $rtrn;
}

// fonction utilisé pour retourner l'historique des enregistrement de capture stream
function getAlarmeHisto() {
    $dir = "./capture";
    echo '<input class="settingInputButton"type="button" onClick="deleteHisto()" value="Effacer historique" > '
    . ' <input type="button" class="settingInputButton" onClick="hideHisto()" value="Fermer" ><br><hr><div class="displayImg"></div>';
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                // affiche le nom et le type si ce n'est pas un element du systeme
                if ($file != '.' && $file != '..' && $file != '.jpg' && preg_match('#\.(jpe?g|gif|png)$#i', $file)) {
                    echo ' <span class="linkImg" onClick="openImg(\'capture/' . $file . '\'); " rel="catpure/' . $file . '">' . $file . ' </span> <br />';
                }
            }
            closedir($dh);
        }
    }
}

function execIt($cmd) {
    sound("1");
    system(" " . $cmd);
    talk("Commande éxécuté");
}

function deleteHisto() {
    //update("interface", array("value2" => 0), "id", "alarme");
    Execute(" rm /var/www/html/capture/*.jpg");
    sound("2");
    return "Nettoyage fait.";
}

function cleanLog() {
    sound("2");
    exec("sudo rm /var/log/apache2/*.gz");
    sleep(1);
    exec("sudo rm /var/log/apache2/*.1");
    sleep(1);
    exec("sudo rm /var/log/*.gz");
    sleep(1);
    exec("sudo rm /var/log/*.1");
    sleep(1);
    sound("2");
}

/**
 * Seconds to human readable text
 * Eg: for 36545627 seconds => 1 year, 57 days, 23 hours and 33 minutes
 * 
 * @return string Text
 */
function getHumanTime($seconds) {
    $units = array(
        'an' => 365 * 86400,
        'j' => 86400,
        'h' => 3600,
        'm' => 60,
        's' => 1,
    );
    $parts = array();
    foreach ($units as $name => $divisor) {
        $div = floor($seconds / $divisor);
        if ($div == 0)
            continue;
        else
        if ($div == 1)
            $parts[] = $div . '' . $name;
        else
            $parts[] = $div . '' . $name;
        $seconds %= $divisor;
    }
    $last = array_pop($parts);
    if (empty($parts)) {
        $rtrn = $last;
    } else {
        $rtrn = join('', $parts) . '' . $last;
    }
    if (is_null($rtrn)) {
        return "0s";
    } else {
        return $rtrn;
    }
}

/**
 * Returns human size
 *
 * @param  float $filesize   File size
 * @param  int   $precision  Number of decimals
 * @return string            Human size
 */
function getSize($filesize, $precision = 2) {
    $units = array('', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y');

    foreach ($units as $idUnit => $unit) {
        if ($filesize > 1024) {
            $filesize /= 1024;
        } else {
            break;
        }
    }
    return round($filesize, $precision) . ' ' . $units[$idUnit] . 'B';
}

function getMemPourcent() {
    $datas = explode(" ", shell_exec("df"));
    return str_replace("%", "", $datas[27]);
}

// ///////////////////////////////////////////////////////////////////////////
// Fonction PushOver => areil que messageNotif mais pour php
// doit etre executÃ© cotÃ© serveur et non client
// @params : $messageString , type string . juste une texte normal
// //////////////////////////////////////////////////////////////////////////
//ex: curl -s --form-string "msg=contenu message"  --form-string "title=titre email" --form-string "capture=true" "192.168.1.69/mail.php" 
function sendEmail($messageString, $capture = "false", $title) {
    // TO FIX //
    $cmd = 'echo "' . $messageString . '" |  mail -F "P.H.A.W.I" -s "' . $title . '" fable2france@gmail.com';
    Execute($cmd);
    sleep(1);
    $cmd = 'echo "' . $messageString . '" |  mail -F "P.H.A.W.I" -s "' . $title . '" pawitra.leudet@gmail.com';
    Execute($cmd);

    //Execute(' curl -s --form-string "msg=' . $messageString . '"  --form-string "title=' . $title . '" --form-string "capture=' . $capture . '" "192.168.1.69/mail.php"');
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION pour calculer le temps en heure et minutes. et seconde
// //////////////////////////////////////////////////////////////////////////
function time_elapsed_A($secs) {
    $bit = array(
        'An' => $secs / 31556926 % 12,
        'sem' => $secs / 604800 % 52,
        'jour' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'mn' => $secs / 60 % 60,
        'sec' => $secs % 60
    );
    foreach ($bit as $k => $v)
        if ($v > 0)
            $ret [] = $v . $k;
    return join(' ', $ret);
}

// //////////////////////////////////////////////////////////////////////////
// FONCTIONNEXTSTATE, dÃ©termine l'Ã©tat suivant d'un device.
// @Params : $state => l'Ã©tat actuel du device (prend en charge 1 / 0 ou ON/OFF
// //////////////////////////////////////////////////////////////////////////
function nextState($state) {
    if ($state == "on") {
        $nextState = "off";
    } elseif ($state == "off") {
        $nextState = "on";
    } elseif ($state == 1) {
        $nextState = 0;
    } elseif ($state == 0) {
        $nextState = 1;
    }
    echo $nextState;
}

// ///////////////////////////////////////////////////////////////////////////////
// FONCTION REVEIL :
// ///////////////////////////////////////////////////////////////////////////////
function reveil($heure, $minute, $state, $radioReveil) {
    $jourDeLaSemaine = date('N');
    if ($jourDeLaSemaine != 6 && $jourDeLaSemaine != 7) {
        $currentHour = date("H");
        $currentMinut = date("i");
        $currentSecond = date("s");
        if ($heure == $currentHour && $minute == $currentMinut && $currentSecond < 06 && $state == 1) {
            update("interface", array("depuis" => time()), "id", "reveil");
            sound("beep-06");
            chambre("on");
            sendEmail("REVEIL: Il est " . $heure . " heure et " . $minute . " minute.", false, "PHAWI: Reveil");
            radio(1, $radioReveil);
            croquette();

            // On désactive aussi le chauffage chambre au cas ou ..
            update("interface", array("value" => 0), "id", "chauffagechambre");
        }
    }
}

// ///////////////////////////////////////////////////////////////////////////////
// FONCTION RADIO :
// ///////////////////////////////////////////////////////////////////////////////
function radio($state, $radioReveil) {
    if ($radioReveil == 1) {
        update("interface", array("value" => $state, "depuis" => time()), "id", "radio");
        if ($state == 1) {
            $cmd = "mpg321 http://chai5she.lb.vip.cdn.dvmr.fr/franceinter-midfi.mp3  >/dev/null 2>&1";
            Execute($cmd);
        }
    }
}

function stopRadio() {
    Execute("killall mpg321");
}

/////////////////////////////////////////////////////////////////////////////
/// Fonction Sound : pour jouer un sond en le parametrant avant.
//Param: $filenum: numÃ©roo (nom) du mp3 a jouer
//Param : $mute: 1 ou 0 si l'application est en mode silence ou non 
/////////////////////////////////////////////////////////////////////////////
function sound($fileName) {
    if ($GLOBALS['muteBDD'] != 1) {

        $post = array(
            "sound" => $fileName
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.71/get_phawi.php');
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        echo $result;

//         gpio(21, 1);
//        Execute(" mpg321 -q /var/www/html/sound/" . $fileName . ".mp3 &");
//       gpio(21, 0);
    }
}

//mpg321 -q /var/www/html/sound/4.mp3 > /dev/null &
/////////////////////////////////////////////////////////////////////////////
/// Fonction Sound : pour jouer un sond en le parametrant avant.
//Param: $filenum: numÃ©roo (nom) du mp3 a jouer
//Param : $mute: 1 ou 0 si l'application est en mode silence ou non 
/////////////////////////////////////////////////////////////////////////////
function gpio($gpio, $state) {

//    if ($gpio == 23) {
//        $gpio = 13;
//    }
//    if ($gpio == 24) {
//        $gpio = 19;
//    }
//    if ($gpio == 25) {
//        $gpio = 26;
//    }
//    if ($GLOBALS['muteBDD'] != 1) {
//        exec('sudo python /var/www/html/fade.py ' . $gpio . ' ' . $state);
//    } else {
//        exec('gpio python /var/www/html/fade.py ' . $gpio . ' 0 ');
//    }
    Execute(" gpio write " . $gpio . " " . $state);
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION Action / Envoi une donnÃ©e POST a Action.php pour appeler une action du Switch
// @Params : $action => l'action a executer : voir dans action.php
// @Params : $state => "on" ou "off" ou ""
// EXEMPLE: action("tout", "on");
// //////////////////////////////////////////////////////////////////////////
function readSerre($talk) {

    $post = array(
        '' => '',
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.14/');
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
    if ($result == "") {
        sound(4);
        return false;
    }
    $serreDatas = explode(":", $result);
    $SerreTemp = $serreDatas[0];
    $SerreArrosage = $serreDatas[1]; //sec
    $serrePluie = $serreDatas[2];



    if ($SerreArrosage !== $GLOBALS['serreArrosage']) {
        if ($SerreArrosage == "1") {
            //sendEmail("Arrosage nécéssaire dans la serre", false, "PHAWI: Arrosage nécéssaire");
        } else {
            //sendEmail("Il n'est plus nécéssaire d'arroser la serre. la terre est humide.", false, "PHAWI: Terre humide");
        }
    }
    if ($talk) {
        $rtrn = "La température dans la serre est de " . str_replace(".", ",", $SerreTemp) . " degrés.";
        talk($rtrn);
    }
    update("interface", array("depuis" => time(), "value" => $SerreTemp, "value1" => $SerreArrosage, "value2" => $serrePluie), "id", "serre");
}

// l'argumeng $onOff doit etre 1 ou 0 pour envoyer la requet d'arrosage a NodeMCU de la serre
function arrosage($onOff) {
    update("interface", array("value" => $onOff, "depuis" => time()), "id", "arrosage");

   // sendEmail("Arrosage à " . $onOff, false, "PHAWI: Arrosage . $onOff");
    $post = array(
        '' => '',
    );
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.14/arrosage' . $onOff);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    echo $result;
}

function AlarmStop() {
    Execute(" mpg321 -q /var/www/html/sound/button-37.mp3");
    update("interface", array("value" => 0, "depuis" => time(), "value1" => 0, "value2" => 0), "id", "alarme");
    ordinateur("on");
}

function writeLCD($string) {
    Execute(' -b python /var/www/html/lcd.py "' . $string . '"');
}

// affecté par les arguments : $outdoorTemp et $weather 
function Weather($BDD_outdoorTemp, $BDD_weather) {
    update("interface", array("depuis" => time()), "id", "tempOut");
    $file = "http://www.meteofrance.com/previsions-meteo-france/bordeaux/33000";
    $doc = new DOMDocument();
    if (!$doc->loadHTMLFile($file)) {
        
    } else {
        $outdoorTemp = null;
        $weather = null;
        $elements = $doc->getElementsByTagName('li');
//          for ($i = 1; $i <= 500; $i++) {
//                echo $i. ":".$elements[$i]->nodeValue ."<br>";
//            }
        if (!is_null($elements)) {
            $weather = trim(($elements[76]->nodeValue));
            $outdoorTemp = intval(str_replace("°C", "", $elements[77]->nodeValue));
//                $weather = trim(($elements[77]->nodeValue));
//                $outdoorTemp = intval(str_replace("°C", "", $elements[78]->nodeValue));
        }
    }
    if (!is_null($outdoorTemp) && !is_null($weather)) {
        if (($BDD_outdoorTemp . $BDD_weather ) != ($outdoorTemp . $weather)) {
            talk("La météo prévue  est " . $weather . " avec une température de " . $outdoorTemp . " degrés. ");
        }
        update("interface", array("value" => $outdoorTemp, "value1" => $weather, "depuis" => time()), "id", "tempOut");
    }
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION Action / Envoi une donnÃ©e POST a Action.php pour appeler une action du Switch
// @Params : $action => l'action a executer : voir dans action.php
// @Params : $state => "on" ou "off" ou ""
// EXEMPLE: action("tout", "on");
// //////////////////////////////////////////////////////////////////////////
// //////////////////////////////////////////////////////////////////////////
// FONCTION d'update universelle pour la BDD 
// example d'appel : 
// update("pin", array("state" => "on", "time" => time()), "allias", "chambre");
// @Params : $table : nom de la table a update 
// @Params : $ArrayKV : tableau contenant les clef et valeur a updater
// @Params : $where : nom de la colonne de l'identifieur
// @Params : $equals : nom de l'element de la colonne $where Ã  update 
// //////////////////////////////////////////////////////////////////////////
function update($table, $ArrayKV, $where, $equals) {
    // example d'appel : 
    // update("pin", array("state" => "on", "time" => time()), "allias", "chambre");
    require 'conf.php';
    $query = "UPDATE `" . $table . "` SET ";
    $count = 1;
    foreach ($ArrayKV as $field => $value) {
        $query .= $field . ' = "' . $value . '"';
        if (count($ArrayKV) != $count) {
            $query .=", ";
        }
        $count++;
    }
    $query .= ' WHERE ' . $where . ' = "' . $equals . '"';
    $bdd->exec($query);
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION de select universelle pour la BDD 
// exemple d'utilisation : 
//  select("timeS", "interne", "name", "acapela");
function select($field, $from, $whereThis, $egalhis) {
    require 'conf.php';
    $req = "SELECT " . $field . " FROM `" . $from . "` WHERE `" . $whereThis . "` = '" . $egalhis . "';";
    echo $req;
    $result = $bdd->query($req);
    $f = $result->fetch();
    return($f[$field]);
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION d'insert universelle pour la BDD 
// example d'appel : 
// update("pin", array("state" => "on", "time" => time()), "allias", "chambre");
// @Params : $table : nom de la table a update 
// @Params : $ArrayKV : tableau contenant les clef et valeur a updater
// @Params : $where : nom de la colonne de l'identifieur
// @Params : $equals : nom de l'element de la colonne $where Ã  update 
// //////////////////////////////////////////////////////////////////////////
function insert($table, $ArrayKV) {
    // example d'appel : 
    // INSERT("captor", array("int" => "20", "time" => time()));
    require 'conf.php';
    $query = "INSERT INTO `" . $table . "` (";
    $count1 = 1;
    $count = 1;
    foreach ($ArrayKV as $field => $value) {
        $query .= " " . $field . "";
        if (count($ArrayKV) != $count) {
            $query .=", ";
        }
        $count++;
    }
    $query .= " ) VALUES (";
    foreach ($ArrayKV as $field => $value) {
        $query .= '  "' . $value . '"';
        if (count($ArrayKV) != $count1) {
            $query .=", ";
        }
        $count1++;
    }
    $query .= " )";

    $bdd->exec($query);
}

function Led($r, $g, $b, $state) {

    if ($GLOBALS['muteBDD'] != 1) {
        if ($state === 1) {
            $post = array(
                "Led" => "led",
                "r" => $r,
                "g" => $g,
                "b" => $b
            );
        } else {
            $post = array(
                "Led" => "led",
                "r" => 255,
                "g" => 255,
                "b" => 255
            );
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://192.168.1.71/get_phawi.php');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_exec($curl);
        curl_close($curl);
    }
}

 

function lit_rss($fichier, $objets) {

    // on lit tout le fichier
    if ($chaine = @implode("", @file($fichier))) {

        // on découpe la chaine obtenue en items
        $tmp = preg_split("/<\/?" . "item" . ">/", $chaine);

        // pour chaque item
        for ($i = 1; $i < sizeof($tmp) - 1; $i+=2) {

            // on lit chaque objet de l'item
            foreach ($objets as $objet) {

                // on découpe la chaine pour obtenir le contenu de l'objet
                $tmp2 = preg_split("/<\/?" . $objet . ">/", $tmp[$i]);

                // on ajoute le contenu de l'objet au tableau resultat
                $resultat[$i - 1][] = @$tmp2[1];
            }

            // on retourne le tableau resultat
            return $resultat[0][0];
        }
    }
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION Pour lire les flux RSS de news 
// //////////////////////////////////////////////////////////////////////////
function lastNews($dateNews) {
    $dateFlux = lit_rss("http://www.franceinfo.fr/rss.xml", array("pubDate"));
    if ($dateNews != $dateFlux) {
        $setDesc = lit_rss("http://www.franceinfo.fr/rss.xml", array("description")) . str_replace('"', "") . str_replace("'", "") . str_replace("</br>", "");
        update("interface", array("value1" => $dateFlux, "depuis" => time()), "id", "flash");
        sound("51", $muteBDD);
        sound("51", $muteBDD);
        acapela("flash information. " . $setDesc);
        sleep(1);
    }
}

function getGraphData($max, $column) {
    include("conf.php");
    $getInterfaceData = $bdd->query('SELECT * FROM `interne` ORDER by id desc LIMIT ' . $max);
    $line = "[";
    $j = 0;
    while ($data = $getInterfaceData->fetch()) {
        $line .= '"' . $data[$column] . '"';
        $j++;
        if ($j == $max) {
            $line .= '';
        } else {
            $line .= ',';
        }
    }
    $line .= "]";
    $getInterfaceData->closeCursor();
    return $line;
}

function writeLog($stringToWrite, $fileName) {
    $fp = fopen("var/www/log/" . $fileName, 'w');
    fwrite($fp, $stringToWrite . "/ \n ");
    fclose($fp);
}

function getRamUsed() {
    $free = 0;
    if (shell_exec('cat /proc/meminfo')) {
        $free = shell_exec('grep MemFree /proc/meminfo | awk \'{print $2}\'');
        $buffers = shell_exec('grep Buffers /proc/meminfo | awk \'{print $2}\'');
        $cached = shell_exec('grep Cached /proc/meminfo | awk \'{print $2}\'');
        $free = (int) $free + (int) $buffers + (int) $cached;
    }
// Total
    if (!($total = shell_exec('grep MemTotal /proc/meminfo | awk \'{print $2}\''))) {
        $total = 0;
    }
// Used
    $used = $total - $free;
// Percent used
    $percent_used = 0;
    if ($total > 0) {
        $percent_used = 100 - (round($free / $total * 100, 1));
    }
    $datas = array(
        'used' => getSize($used * 1024),
        'free' => getSize($free * 1024),
        'total' => getSize($total * 1024),
        'percent_used' => $percent_used,
    );
    return $percent_used;
}
