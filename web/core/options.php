<?php

if(!isset($_GET['url']) and $_SERVER['SCRIPT_NAME'] != "/index.php") {
    header('location:/');
}