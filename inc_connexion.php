<html xmlns="http://www.w3.org/1999/xhtml">
    <head> 
        <meta content="text/html; charset=iso-8859-1"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/> 
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript">

            function setCookie() {
                var login = document.getElementById('login').value;
                var mdp = document.getElementById('mdp').value;
                var d = new Date();
                d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = "PHAWIuser=" + login + ";" + expires + ";path=/";

                var d = new Date();
                d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
                var expires = "expires=" + d.toUTCString();
                document.cookie = "PHAWImdp=" + mdp + ";" + expires + ";path=/";
                
                checkCookie();
            }

            function getCookie(cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }

            function checkCookie() {
                var login = getCookie("PHAWIuser");
                var mdp = getCookie("PHAWImdp");
                if (login != "" && mdp != "") {
                    verifyCookie(login, mdp);
                } else {
                    $("h3").append("Pas de cookies");
                    alert("non autorise");
                }
            }


            $(document).ready(function () {
                checkCookie();
            })



            function verifyCookie(login, mdp) {
                
                $.ajax({
                    type: 'POST',
                    url: 'get_json.php',
                    data: {
                        id: 'checkCookie',
                        type: 'checkCookie',
                        login: login,
                        mdp: mdp
                    },
                    dataType: 'json'
                }).done(function (data) {
                    alert("verifyCookie : " + data) ;
                    console.log(data);
                    if (parseInt(data) > 0) {
                        var part4 = ".html";
                        console.log("Autorisé");
                        var part1 = "control";
                        document.location.href = (part1 + part4);
                    } else {
                        $("h3").append("Acces refusé");
                    }
                });
            }
        </script>
        <title>P.H.A.W.I</title>     
        <link rel="icon" type="image/png" href="img/piHomeIcon.png"/>
        <link rel="shortcut icon" type="image/x-icon" href="img/piHomeIcon.png" />
        <link rel="apple-touch-icon" href="img/iconiphone.png" />
        <meta name="viewport" content="width=device-width"/> 
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="rgba(0,0,0,0.6)">
            <meta name="apple-mobile-web-app-capable" content="yes" />
            <meta name="apple-mobile-web-app-status-bar-style" content="black" />
            <meta name="apple-mobile-web-app-capable" content="yes" />
            <meta name="mobile-web-app-capable" content="yes" />

            <link rel="stylesheet" type="text/css" href="img/fontIcon/flaticon.css" /> 
            <link rel="stylesheet" type="text/css" href="img/149629-essential-compilation/font/flaticon.css"/> 
            <link rel="stylesheet" type="text/css" href="css/style_json.css"/> 
            <link href='https://fonts.googleapis.com/css?family=Cuprum:400,400italic,700,700italic' rel='stylesheet' type='text/css'/>
            <link rel="stylesheet" type="text/css" href="css/font-awesome.css" /> 
            <style>
                input{
                    border-radius: 7px;
                    border: none;
                    background-color: rgba(255,255,255, .8);
                    margin-bottom: 21px;
                    padding: 10px;
                    width: 70%;
                    text-align: center;
                    color: grey;
                }
                legend{
                    color: white;
                    font-weight: normal;
                }
                h2{color: white;
                   margin-bottom: 41px;
                }
                h3{
                    color:red;
                }

            </style>

    </head>
    <body> 

        <center><h2> Connexion</h2> 
            <h3></h3>
            <input name="login" id="login" type="text" value="" size="20" placeholder="LOGIN" />
            <br/>
            <input name="password" id="mdp" type="text" value="" size="20" placeholder="PASSWORD"/>
            <br/><br/><br/>

            <input onclick="setCookie()" name="" value="Sign In" type="button" />

        </center>
    </body>
</html>