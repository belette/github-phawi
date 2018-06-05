delayRefresh = 1000;
timeOutInactivity = 10000;
raspURL = "http://phawi.ddns.net/";
 countPic = 0;
function getDatas() {
    var recieved2 = [];
    try {
        $.getJSON(raspURL + "json.json", function (data) {
            console.log(data);
            $.each(data, function (keyObj, obj) {
                if (obj.isDisplay == 1) {
                    recieved2.push(data[keyObj]);
                    
                }
            });
        });
    } catch (e) {
        alert("erreur de chargement des données: " + e);
    }
    console.log(recieved2);
    return recieved2;
}

function displayPanel(num) {
    console.log("PANEL => " + num);
 

    switch (num) {
        // ACTION  
        case 0:
            $("#contentAction").show();
            $("#contentInfo").show();
            $("#contentPerso").show();
            break;

            // REGLAGES
        case 1:
            $("#contentReglage").show();
            break;

            // SUBSONIC
        case 2:
            $("#subsonicPanel").show();
            sub("nowPlaying", "");
            break

            // BLOC NOTE
        case 3:
            $("#note").show();
            break;

            // CAPTURE
        case 4:
            $("#contentCapture").show();
            break;
    }
}
function setSwipe() {
    // INITIALIZATION de Hammer pour detection swipe sur 
    //ecran tactile

    var mc = new Hammer(document);
    mc.get('pan').set({direction: Hammer.DIRECTION_ALL});
    move = 0
    mc.on("panleft panright", function (ev) {
        if (ev.pointerType === "mouse") {
            var gap = 8;
        } else {
            var gap = 2;
        }
        if (ev.type === "panleft") {
            if (move > gap) {
                displayPanel(numPanel);
                if (numPanel >= 4) {
                    numPanel = -1
                }
                numPanel++;
                move = 0;
            }
            move++;
        }

        if (ev.type === "panright") {
            if (move > gap) {
                displayPanel(numPanel);
                numPanel--;
                if (numPanel <= -1) {
                    numPanel = 4;
                }
                move = 0;
            }
            move++;
        }

    });
}
//////////////////////
$(document).ready(function () {
    //sub("nowPlaying", "");
    refreshNotes();
    numPanel = 0;
   // displayPanel(numPanel);

   // setSwipe();

    //////

    recieved = getDatas();
    chronoStart = setInterval(function () {
        init_screen("info");
        init_screen("action");
        init_screen("reglage");
    }, 1000);
    timeoutStart = setTimeout(function () {
        clearInterval(chronoStart);
    }, timeOutInactivity);
    chronoRefresh();

});

$(document).click(function () {
    clearInterval(chrono);
    clearTimeout(timeout);
    delayRefresh = 1000;
    chronoRefresh();
})

function chronoRefresh() {
    chrono = setInterval(function () {
//         recieved = getDatas();
//         console.log("recieved");
        init_screen("info");
        init_screen("action");
        
    }, delayRefresh);
    // set du time out apr�s inactivit�e:
    timeout = setTimeout(function () {
        recieved = getDatas();
        clearInterval(chrono);
        delayRefresh = 5000;
        chronoRefresh();
    }, timeOutInactivity);
}


function init_screen(type) {

    // To UNCIMMENT BFORE PROD : 
    // // getDatas();
    switch (type) {
        case "action":
            
            $("#contentAction").empty();
            $.each(recieved, function (keyObj, object) {
                if (object.type === type) {
                    displayObjetTypeACTION(object);
                }
            });
            break;

        case "info":
            $("#contentInfo").empty();
            $.each(recieved, function (keyObj, object) {
                if (object.type === type) {
                    displayObjetTypeINFO(object);
                }
            });
            break;

        case "reglage":
            $("#contentReglage").empty();
            $.each(recieved, function (keyObj, object) {
                if (object.type === type) {
                    displayObjetTypeREGLAGE(object);
                }
            });
            break;

        case "all":
            $(".refreshedContent").empty();
            init_screen("info");
            init_screen("action");
            init_screen("reglage");
            break;

        default:
            console.log("type d'Objet inconnu: " + type);
            break;
    }
}

function displayObjetTypeINFO(object) {

    var className = "labelInfo";
    var d = new Date();
    var timeE = Math.round((d.getTime()) / 1000);
    var depuis = '';
    var champAdd = "";
    var mainValue = object.value;
    if (object.depuis !== 0) {
        depuis = '<p class="depuis">depuis: ' + ((timeE - object.depuis)) + " s<p>"
    }
    if (object.id == "alarme") {
        if (object.value2 > 0) {
            $("#message").html('<br><a href="/capture/"> ' + object.value2 + ' alarmes en cours. Cliquez pour voir</a>');
        }
    }
    if (object.id == "wifi") {
        champAdd = "<hr/>" + object.value1;
    }
     if (object.id == "serre") {
        mainValue =   object.value +"*C sec:"+ object.value1 +" pluie:"+ object.value2 ;
    }
    if (object.id == "tempOut" || object.id == "tempIn" || object.id == "tempPi") {
        mainValue = object.value + "°C";
        champAdd = "<hr/>" + object.value1;
    }

    switch (object.value) {
        case 0:
            className = className + "Off";
            break
        case 1:
            className = className + "On";
            break;
        case "alarme1":
            className = className + "Alrm1";
            break
        case "alarme2":
            className = className + "Alrm2";
            break;
        default:
            break
    }
    $("#contentInfo").append(
            '<div class="infoCube ' + className + '" >' +
            '<label >' + object.descr + '</label>' +
            mainValue + depuis + champAdd +
            '</div>' +
            '</div>'
            );
}

function displayObjetTypeREGLAGE(object) {
    switch (object.id) {
        case "wifikey":
            displayReglageTextInput(object);
            break
        case "abs1":
            displayReglageTextInput(object);
            break;
        case "abs2":
            displayReglageTextInput(object);
            break;
        case "exec":
            displayReglageTextInput(object);
            break;
        case "historique":
            displayReglageButtonInput(object);
            break;
        case "clear":
            displayReglageButtonInput(object);
            break;
        case "cron":
            displayReglageButtonInput(object);
            break;
        case "parle":
            displayReglageTextInput(object);
            break;
        case "speakIt":
            displayReglageTextInput(object);
            break;
        case "flash":
            displayReglageSwitchWithNoSupp(object);
            break;
        case "autoChauffage":
            displayReglage(object, "switch", "input", "switch");
            break;
        case "popup":
            displayReglageButtonInput(object);
            break;

            // PAR DEFAUT 
        default:
            displayReglageSwitch(object);
            break
    }
}

function nextState(stateOrigine) {
    if (stateOrigine === "on") {
        return "off";
    } else if (stateOrigine === "off") {
        return "on";
    } else if (stateOrigine == 1) {
        return 0;
    } else if (stateOrigine == 0) {
        return 1;
    } else {
        return stateOrigine;
    }
}

function action(id, state) {
    $.each(recieved, function (keyObj, object) {
        if (object.id === id) {
            if (object.value !== 'push') {
                recieved[keyObj].value = state;
            }
            sendData(recieved[keyObj], state);
        }
    });
    init_screen("action");
    //getDatas();
}

function sendData(object, valChanged) {
    $.ajax({
        type: 'POST',
        url: raspURL + 'get_json.php',
        data: object,
        dataType: 'json'
    }).done(function (data) {
        $("#return").html("Done" + object.descr + " " + valChanged + " : " + data);
    }).fail(function (err) {
        $("#return").html("erreur : " + err);
    }).always(function (arg) {
        console.log("A : " + object.descr + " " + valChanged + " :  " + arg);
        $("#return").html("A : " + object.descr + " " + valChanged + " :  " + arg);
    });
}

function SendReglageSwitch(id, state) {
    $.each(recieved, function (keyObj, object) {
        if (object.id === id) {
            recieved[keyObj].value = state;
            sendData(recieved[keyObj], state);
        }
    });
    init_screen("reglage");
    //getDatas();
}
function SendReglageVal(id) {
    var temVal = $("#" + id).val();
    $.each(recieved, function (keyObj, object) {
        if (object.id === id) {
            recieved[keyObj].value = temVal;
            sendData(recieved[keyObj], temVal);
        }
    });
    init_screen("reglage");
    //getDatas();
}
// =CONCATENER("INSERT INTO interface VALUES ('";E12;"', '";F12;"', '";G12;"', ";H12;", ";I12;", '";J12;"', '";K12;"', '";L12;"', '";M12;"', '";N12;"','";O12;"');")

function SendInputValSup(id) {
    var values1 = document.getElementById("input_" + id + "1").value | '';
    var values2 = "";
    try {
        var values2 = document.getElementById("input_" + id + "2").value | '';
    } catch (e) {
        var values2 = "";
    }
    $.each(recieved, function (keyObj, object) {
        if (object.id === id) {
            recieved[keyObj].value1 = values1;
            recieved[keyObj].value2 = values2;
            sendData(recieved[keyObj], (values1.values2));
        }
    });
    init_screen("reglage");
}

function SendInputValSwitchSup(id) {
    var values1 = document.getElementById("input_" + id + "1").value | '';
    var values2 = "";
    try {
        var values2 = document.getElementById("input_" + id + "2").value | '';
    } catch (e) {
        var values2 = "";
    }
    $.each(recieved, function (keyObj, object) {
        if (object.id === id) {
            recieved[keyObj].value1 = nextState(values1);
            recieved[keyObj].value2 = nextState(values2);
            sendData(recieved[keyObj], (values1.values2));
        }
    });
    init_screen("reglage");

}

function displayReglage(object, value, value1, value2){
    // value peut etre : null  pour non display, "switch" , "input"
    // argument dans un ordre précis :object , value ,value1, value2.
    var rtrn = "";
    // VALUE
    if(value === "switch"){
       rtrn += (
            '<div class="reglageCube">' +
            '<label>' + object.descr + ':</label>' +
            ' <div class="switchOut switchOut' + object.value + '">' +
            '<div id="' + object.id + '" onclick="SendReglageSwitch(\'' + object.id + '\', \'' + nextState(object.value) + '\' )" class="switchIn switchIn' + object.value + '" >' +
            '</div></div>' ); 
        
    }else if(value === "input"){
        var typeInput = "text";
        var reg = new RegExp('^[0-9]+$');
        if (object.value !== '') {
            if (reg.test(object.value)) {
                typeInput = "number";
            }
            rtrn += ' <div class="descVal">' + object.descr + '<input type="' + typeInput + '" id="input_' + object.id + '" value="' + object.value + '"></input></div>'+
                    '<input type="button" onclick="SendReglageVal(\'' + object.id + '\')" value="Enregistrer"></input> </div><br>';
        }
    }
    
    
    // VALUE 1
   if(value1 === "switch"){
       rtrn += (
            '<div class="reglageCube">' +
            '<label>' + object.descVal1 + ':</label>' +
            ' <div class="switchOut switchOut' + object.value1 + '">' +
            '<div onclick="SendInputValSwitchSup(\'' + (object.id)+ '\')"  id="input_' + object.id + '1" class="switchIn switchIn' + object.value1 + '" >' +
            '</div></div>' +
            ''); 
        
    }else if(value1 === "input"){
        var typeInput = "text";
        var reg = new RegExp('^[0-9]+$');
        if (object.value1 !== '') {
            if (reg.test(object.value1)) {
                typeInput = "number"
            }
            ;
            rtrn += ' <div class="descVal">' + object.descVal1 + '<input type="' + typeInput + '" id="input_' + object.id + '1" value="' + object.value1 + '"></input></div>'+
                    '<input type="button" value="Save" onclick="SendInputValSup(\'' + object.id + '\')" ></input> ';
        }
    }
    
    
    //VALUE2
    if(value2 === "switch"){
       rtrn += (
            '<div class="reglageCube">' +
            '<label>' + object.descVal2 + ':</label>' +
            ' <div class="switchOut switchOut' + object.value2 + '">' +
            '<div onclick="SendInputValSwitchSup(\'' + (object.id)+ '\')"  id="input_' + object.id + '2" class="switchIn switchIn' + object.value2 + '" >' +
            '</div></div>' +
            ''); 
        
    }else if(value2 === "input"){
        var typeInput = "text";
        var reg = new RegExp('^[0-9]+$');
        if (object.value2 !== '') {
            if (reg.test(object.value2)) {
                typeInput = "number"
            }
            ;
            rtrn += ' <div class="descVal">' + object.descVal2 + '<input type="' + typeInput + '" id="input_' + object.id + '2" value="' + object.value2 + '"></input></div>'+
                    '<input type="button" value="Save" onclick="SendInputValSup(\'' + object.id + '\')" ></input> ';
        }
    }
        
        $("#contentReglage").append(rtrn +'</div><br>');
}

function displayReglageTestInputSupp(object) {
    var valueSupp = "";
    var typeInput = "text";
    var reg = new RegExp('^[0-9]+$');
    if (object.value1 !== '') {
        if (reg.test(object.value1)) {
            typeInput = "number"
        }
        ;
        valueSupp += ' <div class="descVal">' + object.descVal1 + '<input type="' + typeInput + '" id="input_' + object.id + '1" value="' + object.value1 + '"></input></div> ';
    }
    if (object.value2 !== '') {
        if (typeof parseInt(object.value2) === 'number') {
            typeInput = "number"
        }
        ;
        valueSupp += ' <div class="descVal">' + object.descVal2 + ' <input type="' + typeInput + '" id="input_' + object.id + '2" value="' + object.value2 + '"></input></div>';
    }
    if (object.value2 !== '' || object.value1 !== '') {
        valueSupp += ' <input type="button" value="Save" onclick="SendInputValSup(\'' + object.id + '\')"></input> ';
    }
    return  valueSupp;
}


function displayReglageSwitch(object) {
    var valueSupp = displayReglageTestInputSupp(object);
    $("#contentReglage").append(
            '<div class="reglageCube">' +
            '<label>' + object.descr + ':</label>' +
            ' <div class="switchOut switchOut' + object.value + '">' +
            '<div id="' + object.id + '" onclick="SendReglageSwitch(\'' + object.id + '\', \'' + nextState(object.value) + '\' )" class="switchIn switchIn' + object.value + '" >' +
            '</div></div>' +
            valueSupp + '</div><br>');
}

function displayReglageSwitchWithNoSupp(object) {
    $("#contentReglage").append(
            '<div class="reglageCube">' +
            '<label>' + object.descr + ':</label>' +
            ' <div class="switchOut switchOut' + object.value + '">' +
            '<div id="' + object.id + '" onclick="SendReglageSwitch(\'' + object.id + '\', \'' + nextState(object.value) + '\' )" class="switchIn switchIn' + object.value + '" >' +
            '</div></div>' +
            '</div><br>');
}

function displayReglageTextInput(object) {
    var valueSupp = displayReglageTestInputSupp(object);
    if (object.id === "wifikey") {
        $("#contentReglage").append(
                '<div class="reglageCube"><label>' + object.descr + ':</label>');
        try {
            var keySplit = (object.value).split(";");
            $.each(keySplit, function (k, v) {
                $("#contentReglage").append('<input type="text" value="' + v + '" id="' + object.id + '"></input><br>');
            });
        } catch (e) {
            $("#contentReglage").append('<input type="text" value="' + object.value + '" id="' + object.id + '"></input><br>');
        }

        $("#contentReglage").append('<input type="button" onclick="SendReglageVal(\'' + object.id + '\')" value="Enregistrer"></input> ' +
                ' </div><br>');
    } else {
        $("#contentReglage").append(
                '<div class="reglageCube">' +
                '<label>' + object.descr + ':</label>' +
                '<input type="text" value="' + object.value + '" id="' + object.id + '"></input>' +
                '<input type="button" onclick="SendReglageVal(\'' + object.id + '\')" value="Enregistrer"></input> ' +
                valueSupp +
                ' </div><br>');
    }


}
function displayReglageButtonInput(object) {
    $("#contentReglage").append(
            '<div class="reglageCubeButton">' +
            '<label>' + object.descr + ':</label>' +
            '<input type="button" onclick="SendReglageSwitch(\'' + object.id + '\' , \'\')" value="Executer"></input> ' +
            ' </div><br>');

}



function displayObjetTypeACTION(object) {
    if (object.value === "on" || object.value === "off") {
        $("#contentAction").append(
                '<div class="btnContent" onclick="action(\'' + object.id + '\', \'' + nextState(object.value) + '\' )" >' +
                '<label>' + object.descr + '</label>' +
                '<div id="' + object.value + '"  class="btnOut ' + object.value + '">' +
                '<div class="btnIn">' +
                '<div class="orbit-spinner"> <div class="orbit"></div> <div class="orbit"></div> <div class="orbit"></div> </div>'+

                '</div>' +
                '</div>' +
                '</div>'
                );
    } else if (object.value === "push") {
        $("#contentAction").append(
                '<div class="btnContent" onclick="action(\'' + object.id + '\', \'\' )" >' +
                '<label>' + object.descr + '</label>' +
                '<div id="' + object.value + '"  class="btnOutPush ' + object.value + '">' +
                '<div class="btnIn">' +
                '<div class="orbit-spinner"> <div class="orbit"></div> <div class="orbit"></div> <div class="orbit"></div> </div>'+
                '</div>' +
                '</div>' +
                '</div>'
                );
    } else {
        console.log('erreur')
        $("#contentAction").append(
                '<div class="btnContent" >' +
                '<label>Error</label>' +
                '</div>'
                );
    }
}


function vocalOn() {
    $("#vocal").toggleClass("vocalOn");
    console.log("Commande vocale activ�e.");
}

function popup() {

}

function ActionPerso(action, type) {
    console.log("action : " + action + " /type: " + type);
    $.ajax({
        type: 'POST',
        url: raspURL + 'get_json.php',
        data: {type: type, id: action},
        dataType: 'json'
    }).done(function (data) {
        if (type === "capture") {
            if (login === "belette") {
                $("#capture").append('<img id="imgCapture" style="z-index:'+countPic+';" class="imgCapture" onclick="bigSizePhoto(\'capture/' + data + '\')" src="' + raspURL + 'capture/' + data + '">');
                countPic++;
               // $("#imgCapture").load(function () {
                    console.log("capture on");
                    timeoutCapture = setTimeout(function () {
                        console.log("photo" + countPic);
                        ActionPerso("autre", "capture");
                    }, 500);
              //  })
            }
        } else {
           // console.log(data);
            console.log("poutainnn");
            $("#return").html(data);
        }
        console.log(data);
    }).fail(function (err) {
        $("#return").html("erreur de capture; " + err);
        console.log(err);
    });
}

function bigSizePhoto(url) {
    //clearInterval(timeoutCapture);
    $("#capture").append('<div class="bigSize"> <img onclick="closePic()" src="' + raspURL + url + '" > </div>');
}
function bigSizeHisto(url) {
    $("#capture").append('<div class="bigSize"> <img onclick="closePic()" src="' + raspURL + url + '" > </div>');
}
function closePic() {
    $(".bigSize").fadeOut();
}
function stopCapture() {
    clearTimeout(timeoutCapture);
    console.log("capture stop");
}

/////////////////////////////////////////////////////////////////////////////
// API gestion des NOTES :
 

function subNote(action, id) {
    if (action === "insert") {
        var titre = document.getElementById("inputTitre").value;
        var content = document.getElementById("inputContent").value;
        $.ajax({
            type: 'POST',
            url: raspURL + 'get_json.php',
            data: {titre: titre, content: content, note: action}
        }).done(function (data) {
            refreshNotes();
        });
    } else if (action === "update") {
        var titre = document.getElementById("titre" + id).value;
        var content = document.getElementById("content" + id).value;
        $.ajax({
            type: 'POST',
            url: raspURL + 'get_json.php',
            data: {titre: titre, content: content, note: action, id: id}
        }).done(function (data) {
            refreshNotes();
        });
    }
}
function deleteNote(id) {
    $.ajax({
        type: 'POST',
        url: raspURL + 'get_json.php',
        data: {note: "delete", id: id}
    }).done(function (data) {
        refreshNotes();
    });
}

function refreshNotes() {
    $("#displayNote").empty();
    $.ajax({
        type: 'POST',
        url: raspURL + 'get_json.php',
        data: {note: "refresh"}
    }).done(function (data) {
        $.each(JSON.parse(data), function (keyObj, note) {
            $("#displayNote").append('<input id="titre' + note.id + '" type="text" value="' + note.titre + '" /> <br><br><textarea id="content' + note.id + '">' + note.content + '</textarea>' +
                    '<input type="button" onclick="deleteNote(' + note.id + ')" value="X" /> ' +
                    '<br><input type="button" onclick="subNote(\'update\',' + note.id + ')" value="modify" /> <br><hr>');
        });
    }).fail(function (err) {
        $("#displayNote").append('chargement des notes impossible.: ' + err);
    });
}
/////////////////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////
// api SIBSONIC ===

function displaySub() {
    displayPanel(2);
    sub('nowPlaying', '');
}

function sub(action, option) {
    var user = "admin";
    var mdp = "rienrien";
    var app = "myapp";
    var url = "http://192.168.1.15:4040/rest/";
    var module = "jukeboxControl.view";
    var allurl = url + module + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&";
    if (action === "pause") {
        var actionurl = "action=stop";
        $.post("get_json.php", {sub: allurl + actionurl})
                .done(function (data) {
                    console.log(data);
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(data, "text/xml");
                    console.log(xmlDoc.children[0].attributes.status.value);
                });


    } else if (action === "random") {
        var actionurl = "getRandomSongs";
        $.post("get_json.php", {sub: url + actionurl + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app})
                .done(function (data) {
                    console.log(data);
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(data, "text/xml");
                    var listSongRandom = xmlDoc.children[0].children[0].children;
                    $.each(listSongRandom, function (keyObj, object) {
                        $("#playlistPanel").append('<li class="subElem" onclick="sub(\'play\',\'' + object.attributes.id.value + '\')" >' + object.attributes.artist.value + ' : ' + object.attributes.title.value + ' </li>')
                    });
                });


    } else if (action === "play") {
        idSong = "";
        if (option !== "") {
            idSong = option;
        }
        if (idSong == "") {
            var actionurl = "action=start";
            $.post(raspURL + "get_json.php", {sub: allurl + actionurl})
                    .done(function (data) {
                        parser = new DOMParser();
                        xmlDoc = parser.parseFromString(data, "text/xml");
                    });


        } else {
            var actionurl = "action=set&id=" + idSong;
            $.post(raspURL + "get_json.php", {sub: allurl + actionurl})
                    .done(function (data) {
                        parser = new DOMParser();
                        xmlDoc = parser.parseFromString(data, "text/xml");
                    });
            actionurl = "action=start";
            $.post(raspURL + "get_json.php", {sub: allurl + actionurl})
                    .done(function (data) {
                        parser = new DOMParser();
                        xmlDoc = parser.parseFromString(data, "text/xml");
                    });
        }

        sub("nowPlaying", "");
    } else if (action === "recent") {
        var urlPost = (url + "getAlbumList" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&type=newest&size=30");
        console.log(urlPost);

        $.post("get_json.php", {sub: urlPost})
                .done(function (data) {
                    console.log(data);
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(data, "text/xml");
                    var listSongRandom = xmlDoc.children[0].children[0].children;
                    $("#playlistPanel").empty();
                    $("#playlistPanel").append("Liste des albums récents:<hr>");
                    $.each(listSongRandom, function (keyObj, object) {
                        $("#playlistPanel").append('<li class="subElem" onclick="sub(\'getAlbum\',\'' + object.attributes.id.value + '\')" >' + object.attributes.title.value +
                                ' <span onclick="sub(\'playAlbum\',' + object.attributes.id.value + ')" > ▶PLAY▶</span></li>')
                    });
                });


    } else if (action === "playAlbum") {
        var urlPost = (url + "jukeboxControl" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&action=set&id=" + option);
        console.log(urlPost);
        $.post("get_json.php", {sub: urlPost})
                .done(function (data) {
                    console.log(data);
                });
        sub('play', '');


    } else if (action == "getAlbum") {
        var urlPost = (url + "getMusicDirectory" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&id=" + option);
        console.log(urlPost);
        $.post("get_json.php", {sub: urlPost})
                .done(function (data) {
                    console.log(data);
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(data, "text/xml");
                    var listSongRandom = xmlDoc.children[0].children[0].children;
                    $("#playlistPanel").empty();
                    $("#playlistPanel").append('Liste des chansons:    <span onclick="sub(\'playAlbum\',' + option + ')"> ▶</span> <hr>');

                    $.each(listSongRandom, function (keyObj, object) {
                        $("#playlistPanel").append('<li class="subElem" onclick="sub(\'play\',\'' + object.attributes.id.value + '\')" >' + keyObj + " : " + object.attributes.title.value + ' </li>')
                    });
                });

    } else if (action == "randomRecent") {
        var urlPost = (url + "getAlbumList" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&type=newest&size=10");
        console.log("encoule");
        $.post("get_json.php", {sub: urlPost})
                .done(function (data) {
                    parser = new DOMParser();
                    xmlDoc = parser.parseFromString(data, "text/xml");
                    var listAlbumRandom = xmlDoc.children[0].children[0].children;
                    var randAlbum = Math.floor(Math.random() * (listAlbumRandom.length + 1));
                    var urlPost = (url + "getMusicDirectory" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&id=" + listAlbumRandom[randAlbum].attributes.id.value);
                    $.post("get_json.php", {sub: urlPost})
                            .done(function (data) {
                                parser = new DOMParser();
                                xmlDoc = parser.parseFromString(data, "text/xml");
                                var listSongRandom = xmlDoc.children[0].children[0].children;
                                var randSong = Math.floor(Math.random() * (listSongRandom.length + 1));
                                sub('play', listSongRandom[randSong].attributes.id.value);
                            });
                });


    } else if (action == "nowPlaying") {

        var urlPost = (url + "jukeboxControl" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&action=get");
        $.post("get_json.php", {sub: urlPost}).done(function (data) {
            parser = new DOMParser();
            xmlDoc = parser.parseFromString(data, "text/xml");
            idSong = xmlDoc.children[0].children[0].children[0].attributes.id.value;
            currentAlbumId = xmlDoc.children[0].children[0].children[0].attributes.parent.value;
            currentTitle = xmlDoc.children[0].children[0].children[0].attributes.title.value;
            currentAlbum = xmlDoc.children[0].children[0].children[0].attributes.album.value;
            currentlyPlaying = xmlDoc.children[0].children[0].attributes.playing.value;
            var nowPLaying = '<span onclick="sub(\'getAlbum\', ' + currentAlbumId + ')" >' + currentTitle + " - by " + currentAlbum;
            if (currentlyPlaying === "true") {
                $("#nowPlaying").html(nowPLaying);
            } else {
                $("#nowPlaying").html("Pas de musique");
            }
        });
    } else if (action == "volume") {
        var level = option;
        var urlPost = (url + "jukeboxControl" + "?u=" + user + "&p=" + mdp + "&v=1.15.0&c=" + app + "&action=setGain&gain=" + level);
        $.post("get_json.php", {sub: urlPost})
                .done(function (data) {
                    console.log("volume réglé à " + level);
                });

    }

}
/////////////////////////////////////////////////////////////////////////////