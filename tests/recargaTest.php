<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);
echo '<pre>';

require_once __DIR__ . '/../Celcoin.class.php';
$celcoin = new Celcoin();

$ddd = "53";
$numero = "99991233";
$valor = 20;
$assinante = "Eduardo Matias";
$cpf_cnpj = "00332599566";


$valore_operadora = $celcoin->valoresOperadora($ddd, $numero);

$recarga = $celcoin->recarregar($valor, $ddd, $numero, $valore_operadora['cod_operadora'], $assinante, $cpf_cnpj);

var_dump($valore_operadora, $recarga['capture']);
