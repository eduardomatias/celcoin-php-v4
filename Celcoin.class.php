<?php

// Autoload files using Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

use Celcoin\CelcoinApi;

class Celcoin extends CelcoinApi
{

    // retorna a operadora e os valores disponiveis para recarga
    public function valoresOperadora($ddd, $numero)
    {
        try {

            // Operadora
            $operadora = $this->findProviders($ddd, $numero);
            if (!$operadora) {
                throw new Exception("Não foi possível encontrar a operadora do número informado, tente novamente.");
            }
            $providerId = $operadora['providerId'];
            $nameProvider = $operadora['nameProvider'];
    
            // Valores
            $valores = $this->getProvidersValues($ddd, $providerId)['value'];
            if (!$valores) {
                throw new Exception("Não foi possível encontrar os valores de recarga para a operadora $nameProvider, tente novamente.");
            }

            $valoresChaveValor = [];
            foreach ($valores as $v) {
                $valoresChaveValor[$v['maxValue']] = $v['productName'];
            }

            return [
                "cod_operadora" => $providerId,
                "nome_operadora" => $nameProvider,
                "valores_recarga" => $valoresChaveValor
            ];

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    // realiza recarga
    public function recargar($valor, $ddd, $numero, $cod_operadora, $assinante, $cpf_cnpj, $capturar = true)
    {
        try {

            $recarga = $this->topups($valor, $ddd, $numero, $cod_operadora, $assinante, $cpf_cnpj, $capturar);
            if (!$recarga) {
                throw new Exception("Não foi possível realizar a recarga, tente novamente.");
            }

            return $recarga;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // captura recarga
    public function capturar($transaction_id)
    {
        try {

            $captura = $this->capture($transaction_id);
            if (!$captura) {
                throw new Exception("Não foi possível realizar a recarga, tente novamente.");
            }

            return $captura;

        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}