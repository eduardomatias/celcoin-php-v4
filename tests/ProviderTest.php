<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Autoload files using Composer autoload
require_once '../vendor/autoload.php';
use Celcoin\CelcoinApi;

$celcoin = new CelcoinApi();
print_r($celcoin->getProviders());
