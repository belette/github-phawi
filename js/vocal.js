/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final_span = document.getElementById("message");
interim_span = document.getElementById("return");
command_off = ['éteins','désactive','éteint','éteindre','etam','stop','est dans','est un','coupe'];
command_on=['allume','active','démarre','allumer'];
command_quel = ['quel','quelle','combien','comment','dit'];
command_control=['contrôle','paris','ah oui'];  
//command_on=['',];

var final_transcript = '';
var recognizing = false;
var ignore_onend;
var start_timestamp;
if (!('webkitSpeechRecognition' in window)) {
    upgrade();
} else {
    //start_button.style.display = 'inline-block';
    var recognition = new webkitSpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = true;

    recognition.onstart = function () {
        recognizing = true;
        //$("#frequency").html('<img class="frequency" src="img/sound-frecuency.png" />');
        console.log("ecoute...");
    };

    recognition.onerror = function (event) {
        if (event.error == 'no-speech') {
            ignore_onend = true;
        }
        if (event.error == 'audio-capture') {
            ignore_onend = true;
        }
        if (event.error == 'not-allowed') {

            ignore_onend = true;
        }
    };

    recognition.onend = function () {
        recognition.start();
        // recognizing = false;
        if (ignore_onend) {
            return;
        }
        if (!final_transcript) {
            return;
        }


    };

    recognition.onresult = function (event) {
        $("#frequency").html('<img class="frequency" style="display:none;" src="img/sound-frecuency-on.png" />');
        var interim_transcript = '';
        if (typeof (event.results) == 'undefined') {
            recognition.onend = null;
            //recognition.stop();
            upgrade();
            return;
        }
        for (var i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                final_transcript += event.results[i][0].transcript;
                $("#order").append(event.results[i][0].transcript);
                $("#popup").append(event.results[i][0].transcript);
            } else {
                interim_transcript += event.results[i][0].transcript;
            }
        }
        final_transcript = capitalize(final_transcript);
        final_span.innerHTML = linebreak(final_transcript);

        ///////////////////   ///////////////////////////////////////////////////////////////////////////////////////////////
        analyseSentense();
        final_span.innerHTML = "";
        final_transcript = "";
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  

        interim_span.innerHTML = linebreak(interim_transcript);
        if (final_transcript || interim_transcript) {
            // showButtons('inline-block');

        }

    };

}
function senToAnalisis(sentence) {
    var urlVoc = "vocalAnalisis";

    $.ajax({
        url: "./" + urlVoc + ".php",
        type: "POST",
        data: {
            sentenceSent: sentence,
        }
    }).done(function (arg) {
        alert(arg);
    })
}

function upgrade() {
    //start_button.style.visibility = 'hidden';
}

var two_line = /\n\n/g;
var one_line = /\n/g;
function linebreak(s) {
    return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
}

var first_char = /\S/;
function capitalize(s) {
    return s.replace(first_char, function (m) {
        return m.toUpperCase();
    });
}
startButton(event);
function startButton(event) {
    if (recognizing) {
        recognition.stop();
        return;
    }
    final_transcript = '';
    recognition.lang = 'fr-FR';
    recognition.start();
    ignore_onend = false;
    final_span.innerHTML = '';
    interim_span.innerHTML = '';
    showButtons('none');
    //start_timestamp = event.timeStamp;
}



var current_style;
function showButtons(style) {
    if (style == current_style) {
        return;
    }
    current_style = style;
}



function analyseSentense() {
    var getfinal_transcript = document.getElementById('message').innerHTML;
    var final_transcriptLow = (getfinal_transcript.toLowerCase().trim());
    var state = "";
    var stateFound = false;command_control
    var done = false;
    if (final_transcriptLow.includes("contrôle") ||
            final_transcriptLow.includes("paris") ||
            final_transcriptLow.includes("ah oui")) {
        
        $.each(command_off, function( index, value ) {
            var reg = new RegExp(value);
            if ( reg.test(final_transcriptLow)) {
                state = "off";
                stateFound = true;
            }
          });
           $.each(command_on, function( index, value ) {
             var reg = new RegExp(value);  
            if (reg.test(final_transcriptLow)) {
                state = "on";
                stateFound = true;
            }
          });
           $.each(command_quel, function( index, value ) {
             var reg = new RegExp(value);
            if ( reg.test(final_transcriptLow)) {
                stateFound = true;
            }
          });
 
        // une fois l'état reccupere , on envois la donne apres un controle de coherance
        if (stateFound) {
           console.log(recieved);
            $.each(recieved, function (keyObj, object) {

                var arrayVocal = (object.vocal).split(";");
                //console.log(arrayVocal);
                $.each(arrayVocal, function (keyVoc, vocal) {
                    if (final_transcriptLow.includes(arrayVocal[keyVoc]) && arrayVocal[keyVoc] !== "") {
                           console.log(arrayVocal[keyVoc]);
                        
                        if (recieved[keyObj].value === "push") {
                            recieved[keyObj].fromVocal = "push";
                            
                        } else if (recieved[keyObj].value === "on" || recieved[keyObj].value === "off"){
                            recieved[keyObj].value = state;
                            recieved[keyObj].fromVocal = "affirmatif";
                            
                        }else if(recieved[keyObj].type === "info" ){    
                            console.log("s info :::"); 
                            recieved[keyObj].fromVocal = "info";
                            
                        }else if(recieved[keyObj].id === "radio"){
                            alert("reasiooo");
                             console.log("s RADIO :::"); 
                            recieved[keyObj].fromVocal = "radio";
                        }else{
                            console.log("oops else... :::" +recieved[keyObj].type);
                        }
                        console.log(recieved[keyObj]); 
                       sendData(recieved[keyObj], state);
                        return false;
                    } 

                });



//                if (final_transcriptLow.includes(object.id) || final_transcriptLow.includes((object.descr).toLowerCase().replace("é", "e")) ) {
//                    
//                }
            });
        }
    }
    if (done) {
        $("#message").empty();
    }
}

//||

function démonstration() {
    $.ajax({
        url: "./action.php",
        type: "POST",
        data: {
            action: "demonstration"
        }
    }).done(function (arg) {

    });
}
