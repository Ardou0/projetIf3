<?php

setlocale(LC_TIME, 'fr_FR.utf8');
define('URL', str_replace("index.php", "", (isset($_SERVER['HTTPS']) ? "https" : "http"). "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]")); // URL utilisé par le client
define('PATH', str_replace("index.php", "", $_SERVER['DOCUMENT_ROOT'].$_SERVER['PHP_SELF'])); // path de l'app
//define('CONF', json_decode(file_get_contents("./config.json"), true));

$cookie_duration = time() + (60*60);
if(isset($_COOKIE['lang'])) {
    define("LANG", $_COOKIE['lang']."/");
} else {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    
    $supported_langs = ['fr', 'en'];

    if (!in_array($lang, $supported_langs)) {
        $lang = 'en';
    }

    setcookie('lang', $lang, $cookie_duration, "/");
    define("LANG", $lang."/");
}
session_start();

require_once('core/model.php');
require_once('core/view.php');
require_once('core/controller.php');
new controller(); 