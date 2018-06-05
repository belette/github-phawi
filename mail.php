<?php

if (isset($_REQUEST ['msg'])) {
    $msg = $_REQUEST ['msg'];
}
if (isset($_REQUEST ['title'])) {
    $title = $_REQUEST ['title'];
} else {
    $title = "P.H.A.W.I No TITLE";
}

if (isset($_REQUEST ['capture'])) {
    $capture = $_REQUEST ['capture'];
}
$mail = 'fable2france@gmail.com'; // Déclaration de l'adresse de destination.
$passage_ligne = "\n";
//=====Déclaration des messages au format texte et au format HTML.
$message_txt = $title;
$message_html = '<html><head></head><body style=" height:100%;background-color:rgba(0,0,0,.7);color:white; background-size: cover;" > '
        . '' . $msg . ''
        . '</body></html>';
//==========
//=====Création de la boundary.
$boundary = "-----=" . md5(rand());
$boundary_alt = "-----=" . md5(rand());
//==========
//=====Définition du sujet.
$sujet = $title;
//=========
//=====Création du header de l'e-mail.
$header = "From: \"P.H.A.W.I\"<P.H.A.W.I>" . $passage_ligne;
$header.= "Reply-to: \"P.H.A.W.I\" <wP.H.A.W.I>" . $passage_ligne;
$header.= "MIME-Version: 1.0" . $passage_ligne;
$header .= "X-Priority: 1" . $passage_ligne;
$header.= "Content-Type: multipart/mixed;" . $passage_ligne . " boundary=\"$boundary\"" . $passage_ligne;
//==========
//=====Création du message.
$message = $passage_ligne . "--" . $boundary . $passage_ligne;
$message.= "Content-Type: multipart/alternative;" . $passage_ligne . " boundary=\"$boundary_alt\"" . $passage_ligne;
$message.= $passage_ligne . "--" . $boundary_alt . $passage_ligne;
//=====Ajout du message au format texte.
$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"" . $passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
$message.= $passage_ligne . $message_txt . $passage_ligne;
//==========

$message.= $passage_ligne . "--" . $boundary_alt . $passage_ligne;

//=====Ajout du message au format HTML.
$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"" . $passage_ligne;
$message.= "Content-Transfer-Encoding: 8bit" . $passage_ligne;
$message.= $passage_ligne . $message_html . $passage_ligne;
//==========
//=====On ferme la boundary alternative.
$message.= $passage_ligne . "--" . $boundary_alt . "--" . $passage_ligne;
//==========
$message.= $passage_ligne . "--" . $boundary . $passage_ligne;

if ($capture == "true") {
      exec("sudo raspistill -t  300 -o /var/www/html/capture/stream.jpg -vf  -w 1920 -h 1080");
      sleep(1);
    //exec("sudo wget -q http://192.168.1.69/capture/stream.jpg -O /var/www/streamcp.jpg");
    //=====Lecture et mise en forme de la pièce jointe.
    $fichier = fopen("capture/stream.jpg", "r");
    $attachement = fread($fichier, filesize("capture/firstAlarme.jpg"));
    $attachement = chunk_split(base64_encode($attachement));
    fclose($fichier);
    $message.= "Content-Type: image/jpeg; name=\"image.jpg\"" . $passage_ligne;
    $message.= "Content-Transfer-Encoding: base64" . $passage_ligne;
    $message.= "Content-Disposition: attachment; filename=\"image.jpg\"" . $passage_ligne;
    $message.= $passage_ligne . $attachement . $passage_ligne . $passage_ligne;
    $message.= $passage_ligne . "--" . $boundary . "--" . $passage_ligne;
}

//=====Ajout de la pièce jointe.
//========== 
//=====Envoi de l'e-mail.
mail($mail, $sujet, $message, $header);
?>


