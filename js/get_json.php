<?php

include('conf.php');
include('inc_fonction.php');

$getInterfaceData = $bdd->query('SELECT * FROM interface');
$interfaceData = array();
while ($data = $getInterfaceData->fetch()) {
    $tempArray = array(
        $data['id'] => array(
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
    var_dump($tempArray);
    array_push($interfaceData, $tempArray);
}
$getInterfaceData->closeCursor();

if (isset($_POST['id'])) {
    switch ($_POST['type']) {
        case 'action':
            if ($_POST['value'] === "push") {
                // dans le cas d'une action PUSH
                try{
                    $actionToDoList = explode(";", $_POST['commandOn']);
                    foreach($actionToDoList as $action){
                        $elems = explode("=",$action );
                         foreach($actionToDoList as $action){
                              $state = elems[1];
                              if($state === "on"){
                                  $typeCommand = 'commandOn';
                              }else{
                                  $typeCommand = 'commandOff';
                              }
                                exec("sudo " . $interfaceData[elems[0]][$typeCommand]);
                                update("interface", array(
                                                    "value" => $state,
                                                    "time" => time()
                                        ), "id", $interfaceData[elems[0]]);
                         }
                    }
                    print_r(json_encode($_POST['descr']));
                } catch (Exception $ex) {
                    $commandMode = $interfaceData[$_POST['commandOn']]["commandOn"];
                    exec("sudo " .$commandMode);
                    print_r(json_encode($ex));
                }
                
                
            } else if ($_POST['value'] === "on" || $_POST['value'] === "off") {
                //  Dans le cas d'une action on/off 
                try {
                    if ($_POST['value'] === "on") {
                        exec("sudo " . $_POST['commandOn']);
                    } else {
                        exec("sudo " . $_POST['commandOff']);
                    }
                } catch (Exception $ex) {
                    return "ok";
                }
                update("interface", array(
                    "value" => $_POST['value'],
                    "time" => time()
                        ), "id", $_POST['id']);
            } else {
                 print_r(json_encode("Action inconnue." . $_POST['id']));
            }
            break;

        case 'reglagle' :

            break;

        default:
            break;
    }
}

// //////////////////////////////////////////////////////////////////////////
// FONCTION d'update universelle pour la BDD 
// example d'appel : 
// update("pin", array("state" => "on", "time" => time()), "allias", "chambre");
// @Params : $table : nom de la table a update 
// @Params : $ArrayKV : tableau contenant les clef et valeur a updater
// @Params : $where : nom de la colonne de l'identifieur
// @Params : $equals : nom de l'element de la colonne $where Ã  update 
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
    $bdd->exec($query);
}


        