<?php

include('conf.php');
include('inc_fonction.php');



if (isset($_POST['sub'])) {
    sound('6');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $_POST['sub']);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($curl);
    curl_close($curl);
echo $result;
return;
    //print(($result));
}


if (isset($_POST['note'])) {

    $titreNote = $_POST['titre'];
    $contentNote = $_POST['content'];
    $idNote = $_POST['id'];
    $actionNote = $_POST['note'];
    switch ($actionNote) {
        case 'insert':
            insert("note", array("titre" => $titreNote, "content" => $contentNote, "date" => date("d-m-Y H:i:s")));
            $getInterfaceData = $bdd->query('SELECT * FROM note');
            print_r(json_encode($getInterfaceData->fetchAll()));
            break;
        case 'refresh':
            $getInterfaceData = $bdd->query('SELECT * FROM note');
            print_r(json_encode($getInterfaceData->fetchAll()));
            break;
        case 'delete':
            $getInterfaceData = $bdd->query('DELETE from note where id=' . $idNote);

            break;
        case 'update':
            update("note", array(
                "content" => $contentNote,
                "titre" => $titreNote,
                "date" => date("d-m-Y H:i:s")
                    ), "id", $idNote);
            break;
    }
}



if (isset($_REQUEST['type'])) {
    switch ($_REQUEST['type']) {
        case 'action':
            if ($_POST['value'] === "push") {
                 talk($_POST['descr']);
                update("interface", array(
                                    "depuis" => time()
                                        ), "id",  $_POST['id']);
                // dans le cas d'une action PUSH
                $test = 'respone : ';
                try {
                    $actionToDoList = explode(",", $_POST['commandOn']);
                    foreach ($actionToDoList as $action) {
                        $elems = explode("=", $action);
                        $state = $elems[1];
                        $idBDD = $elems[0];
                       
                        if ($state === "on") {
                            $getCOmmand = $bdd->query('SELECT commandOn FROM interface WHERE id="' . $idBDD . '"');
                            while ($row = $getCOmmand->fetch()) {
                                $test .= $row["commandOn"] . " ";
                                Execute($row["commandOn"]);
                                update("interface", array(
                                    "value" => $state,
                                    "depuis" => time()
                                        ), "id", $idBDD);
                                sleep(1);
                            }
                        } else {
                            $getCOmmand = $bdd->query('SELECT commandOff FROM interface WHERE id="' . $idBDD . '"');
                            while ($row = $getCOmmand->fetch()) {
                                $test .= $row["commandOff"] . " ";
                                Execute($row["commandOff"]);
                                update("interface", array(
                                    "value" => $state,
                                    "depuis" => time()
                                        ), "id", $idBDD);
                                sleep(1);
                            }
                        }
                    }
                    echo ("" . json_encode($test));
                } catch (Exception $ex) {
                    $commandMode = $interfaceData[$_POST['commandOn']]["commandOn"];
                    exec("sudo " . $commandMode);
                    echo ("erreur" . json_encode($cmd));
                }
            } else if ($_POST['value'] === "on" || $_POST['value'] === "off") {
                sound("51", $muteBDD);
                //  Dans le cas d'une action on/off 
                try {
                    if ($_POST['value'] === "on") {
                        Execute("sudo " . $_POST['commandOn']);
                    } else {
                        Execute("sudo " . $_POST['commandOff']);
                    }
                    update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                } catch (Exception $ex) {
                    echo "Erreur";
                }
            } else {
                echo (json_encode("Action inconnue." . $_POST['id']));
            }
            break;

        case 'reglage' :
            sound("8");
            if ($_POST['id'] === "cron") {
                exec("sudo service PHAWI reload");
                talk("crontabe relancé. scan du system");
                echo(json_encode("Lancement de la crontab"));
            } else if ($_POST['id'] === "exec") {
                update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                echo (json_encode(system($_POST['value'])));
            } else if ($_POST['id'] === "mute") {
                talk("mode silence à " . $_POST['value']);
                update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                echo (json_encode("mode silence à " . $_POST['value']));
            }else if ($_POST['id'] === "simulation") {
                talk("mode simulateur de présence à " . $_POST['value']);
                update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                echo (json_encode("mode simulateur à " . $_POST['value']));
            } else if ($_POST['id'] === "clear") {
                echo (json_encode(cleanLog()));
            } else if ($_POST['id'] === "parle") {
                update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                echo (json_encode(talk($_POST['value'])));
            } else if ($_POST['id'] === "speakIt") {
                update("interface", array("value" => $_POST['value'], "depuis" => time()), "id", $_POST['id']);
                echo (json_encode(speak($_POST['value'])));
            } else if($_POST['id'] === "autoChauffage"){
                if($_POST['value'] == 0){
                    talk("Désactivation du chauffage automatique");
                }else{
                    talk("réglage chauffage mis à jour. " . $_POST['descr'] . " à " . $_POST['value'] . " , " . $_POST['descVal1'] . " à " . $_POST['value1'] . " degré et forçage manuel à " . $_POST['value2']);
                }
                echo (json_encode(update("interface", array("value" => $_POST['value'], "value1" => $_POST['value1'], "value2" => $_POST['value2']), "id", $_POST['id'])));
            }else {
                talk("réglage mis à jour. " . $_POST['descr'] . " à " . $_POST['value'] . " , " . $_POST['descVal1'] . " à " . $_POST['value1'] . " et " . $_POST['descVal2'] . " à " . $_POST['value2']);
                echo (json_encode(update("interface", array("value" => $_POST['value'], "value1" => $_POST['value1'], "value2" => $_POST['value2']), "id", $_POST['id'])));
            }

            break;
        case 'capture' :
            
            echo (json_encode(captureStream()));
            break;
        
         case 'zero' :
             //sound(3);
        
             // /////////////////////////////////////////////////////
            //Allumage  AUTO chambre  //////////////////////////////////
            //echo(chambreAuto($_POST['zeroPir'], $_POST['ambient_temperature']));
        
            break;
         case 'room' :
             sound(3);
             // /////////////////////////////////////////////////////
            //Allumage  AUTO chambre  //////////////////////////////////
             echo(chambreAuto($_REQUEST['zeroPir']));
            break;
         case 'soundRoom' :
             sound(3);
             // /////////////////////////////////////////////////////
            //Allumage  AUTO chambre  //////////////////////////////////
             soundRoom();
             //if($GLOBALS['stateDeviceChambre'] == "on" ){
            
            break;
            
       case 'openFrontDoor' :
           talk("overture porte");
            echo openFrontDoor();
            break;
        
          case 'arrosage1' :
           talk("arrosage en marche");
            echo arrosage(1);
            break;
        
        case 'arrosage0' :
           talk("arrosage désactivé");
            echo arrosage(0);
            break;
        
        case 'stopCapture':
            stopCapture();
            break;
        case 'getGraphData':
            $retour = (getGraphData($_POST['max'], $_POST['column']));
            //talk($retour);
                 //talk(getGraphData($_POST['max'], $_POST['column']));
            echo ($retour);
            break;
        case 'cleanCapture' :
            echo (json_encode(
                    deleteHisto()
                  ));
            break;

        case 'stopRadio' :
            echo (json_encode(
                    stopRadio()
            ));
            break;
         case 'talk' :
            talk($_POST['id']);
            break;
        case 'croquette':
             echo (json_encode(
                   croquette()
            ));
            break;
        
        case 'meteo':
                     //  $GLOBALS['SalonCommandOff']
                     talk("Température intérieure de ".$GLOBALS['degre']." degré. La température dans la serre est de " . str_replace(".",",",$GLOBALS['serreTemp']) . " degrés. La météo prévue  est " . $GLOBALS['weather'] . " avec une température extèrieure de " . $GLOBALS['outdoorTemp'] . " degrés.");
            break;
        
        case 'stopAlarme':
            talk("Alarme et système de surveillance remis à zero");
            update("interface", array("value" => 0, "depuis" => time()), "id", "abs");
            update("interface", array("value" => 0, "depuis" => time()), "id", "wifi");
            echo (json_encode("Alarme et systeme de surveillance remis à zero"));
            break;
        case 'checkCookie' :
            $rtrn = 0;
            $req = 'SELECT count(*) as counter FROM user WHERE login="' . $_POST['login'] . '" AND mdp="' . md5($_POST['mdp']) . '"';
            $reqUser = $bdd->query($req);
            while ($datas = $reqUser->fetch()) {
                $rtrn = $datas['counter'];
            }
            echo (json_encode($rtrn));
            break;
        case 'histoCapture':
            talk("histo");
            echo (json_encode(dirToArray("./capture")));
            break;
        
        default:
            talk("commande " .$_POST['type']." inconnue");
            break;
    }
    // reponse pour commande vocale:
    if(isset($_POST['fromVocal'])){
       
    checkVocal($_POST['fromVocal']);
    }
}


function checkVocal($sentence) {

    if (isset($_POST['fromVocal'])) {


        if ($_POST['fromVocal'] === "confirme") {
            $vocalAffirmativeReply = array(
                "Bien entendu.",
                "je m'éxécute.",
                "c'est comme si c'était fait.",
                "et voici.",
                "à vos ordre.",
                "puis-je faire autre chose pour vous?",
                "voilà!",
                "C'est fait!",
                "terminé!"
            );
            $random_keys = array_rand($vocalAffirmativeReply, 2);
            talk($vocalAffirmativeReply[$random_keys[0]]);
        } else if ($_POST['fromVocal'] === "push") {
            talk("activation " . $_POST['descr']);
        } else if ($_POST['fromVocal'] === "info") {
            switch ($_POST['id']) {
                case 'tempIn':
                    $rep = str_replace(".", " virgule ", $_POST['value']);
                    talk("La température intérieure est de " . $rep . " degrés.");
                    break;

                default:
                    $rep = "valeur de " . $_POST['descr'] . " est de " . str_replace(".", " virgule ", $_POST['value']);
                    talk($rep);
                    print_r($rep);
                    break;
            }
        } else if ($_POST['fromVocal'] === "radio") {
            $cmd = "mpg321 http://chai5she.lb.vip.cdn.dvmr.fr/franceinter-midfi.mp3  >/dev/null 2>&1";
            Execute($cmd);
        }else{
            talk($_POST['fromVocal']);
        }
    }
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION d'update universelle pour la BDD 
// example d'appel : 
// update("pin", array("state" => "on", "time" => time()), "allias", "chambre");
// @Params : $table : nom de la table a update 
// @Params : $ArrayKV : tableau contenant les clef et valeur a updater
// @Params : $where : nom de la colonne de l'identifieur
// @Params : $equals : nom de l'element de la colonne $where �?  update 
// //////////////////////////////////////////////////////////////////////////
function updateFromJson($table, $ArrayKV, $where, $equals) {
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
    echo $query;
    $bdd->exec($query);
}
