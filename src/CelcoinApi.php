<?php
/**
 * @version    CVS: 1.0.0
 * @package    Celcoin
 * @author     Jean Barbosa <programmer.jean@gmail.com>
 * @copyright  2019 Toolstore
 * @license    MIT
 */

namespace Celcoin;

use GuzzleHttp\Client;

class CelcoinApi
{

    /**
     * access credentials (user)
     * @var string
     */
    private $clientId;

    /**
     * access credentials (secret key)
     * @var string
     */
    private $secretKey;

    /**
     * token provided by the API at the time of login.
     * @var array
     */
    private $token = [];

    /**
     * Configuracoes básicas
     * @var array
     */
    private $configs = [];

    /**
     * stores the API response when a request is made.
     * @var array
     */
    protected $response;

    protected $debug = false;

    protected $countryCode = 55;

    /**
     * celcoin API constructor.
     *
     * @param string $clientId
     * @param string $secretKey
     */
    public function __construct($clientId = '', $secretKey = '')
    {
        $this->configs = include('Config.php');

        if (empty($clientId) || empty($secretKey)) {

            $this->debug = true;
            $this->clientId = $this->configs['client_id'];
            $this->secretKey = $this->configs['client_secret'];

        } else {

            $this->clientId = $clientId;
            $this->secretKey = $secretKey;

        }

    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param array $configs
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }


    /**
     * Send request to server
     *
     * @param $method
     * @param string $uri
     * @param array $options
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @see http://docs.guzzlephp.org/en/stable/psr7.html#body
     */
    public function sendRequest($method, $uri = '', array $options = [])
    {

        $url = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $url .= $this->configs['version'] . $uri;

        $headers = [
            'headers' => [
                "Authorization" => "Basic " . base64_encode($this->clientId . ':' . $this->secretKey),
                "Content-Type" => "application/json"
            ]
        ];

        $options = array_merge($headers, $options);

        $client = new Client(['verify' => false]);
        $this->response = $client->request($method, $url, $options);

        if ($this->response->getStatusCode() == 200) {
            return json_decode($this->response->getBody(), true);
        }

        return false;
    }

    /**
     * Get details of the current Merchant
     *
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMerchantInfo()
    {
        $uri = $this->configs['paths']['Merchant'];

        $options = [
            'headers' => [
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token'],
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return false;
        }

        return $response;
    }

    /**
     * Get an array with the providers
     *
     * @return bool|mixed
     */
    public function getProviders($ddd = "31")
    {
        $uri = $this->configs['paths']['Providers'];

        $options = [
            'query' => [
                "stateCode" => $ddd, 
                "type" => "0",
                "category" => "1"
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return false;
        }

        return $response;
    }

    /**
     * Get a list of all provider values
     *
     * @param $regionalCode Code of regional
     * @param $providerId   Provider unique identifier
     * @return bool|mixed|string
     */
    public function getProvidersValues($regionalCode, $providerId)
    {
        $uri = $this->configs['paths']['ProviderValues'];

        $options = [
            'query' => [
                'stateCode' => $regionalCode,
                'providerId' => $providerId
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return '';
        }

        return $response;
    }

    public function findProviders($stateCode, $PhoneNumber)
    {
        $uri = $this->configs['paths']['FindProvider'];

        $options = [
            'query' => [
                'stateCode' => $stateCode,
                'PhoneNumber' => $PhoneNumber
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return '';
        }

        return $response;
    }

    /**
     * Resource to make a new top up.
     *
     * @param $value          Value of your topup
     * @param $regionalCode   Code of regional
     * @param $phoneNumber    Phone number
     * @param $providerId     Provider unique identifier
     * @param $signerCode     
     * @param $cpfCnpj        
     * @param bool $capture   Auto-commit (Default is true)
     * @return bool|mixed|string
     */
    public function topups($value, $regionalCode, $phoneNumber, $providerId, $signerCode, $cpfCnpj, $capture = true)
    {
        $uri = $this->configs['paths']['Topup'];

        $options = [
            'json' => [
                "externalTerminal" => "JCDiniz",
                // "externalNsu" => int, // referencia do sistema do cliente
                "topupData" => [
                    "value" => (float)$value
                ],
                "cpfCnpj" => $cpfCnpj,
                "signerCode" => $signerCode,
                "providerId" => (int)$providerId,
                "phone" => [
                    "countryCode" => $this->countryCode,
                    "stateCode" => (int)$regionalCode,
                    "number" => (int)$phoneNumber
                ]
            ]
        ];
        $response = $this->sendRequest('POST', $uri, $options);

        // captura o valor
        if ($capture && !empty($response['transactionId'])) {
            $response['capture'] = $this->capture($response['transactionId']);
        }
        
        if ($response === false) {
            return '';
        }

        return $response;
    }

    public function capture($transactionId)
    {
        $uri = $this->configs['paths']['Topup'] . "/$transactionId/capture";
        
        $options = [
            'json' => [
                // "externalNSU" => 0,
                "externalTerminal" => "JCDiniz"
            ]
        ];

        $response = $this->sendRequest('PUT', $uri, $options);
        if ($response === false) {
            return '';
        }

        // verifica erro na captura
        if ($response['errorCode'] != '000') {
            throw new Exception($response['message'] ? : "Não foi possível realizar a recarga.");
        }

        return $response;
    }
    
    public function getStatusTransaction($transactionId)
    {
        $uri = $this->configs['paths']['StatusTransaction'];

        $options = [
            'query' => [
                'transactionId' => $transactionId,
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return '';
        }

        return $response;

    }

    /**
     * Get details of a specified transaction
     *
     * @param $transactionId    Transaction unique identifier
     * @return bool|mixed|string
     */
    public function getTransaction($transactionId)
    {
        $uri = $this->configs['paths']['Transaction'];

        $options = [
            'query' => [
                'transactionId' => $transactionId,
            ]
        ];

        $response = $this->sendRequest('GET', $uri, $options);

        if ($response === false) {
            return '';
        }

        return $response;

    }

}