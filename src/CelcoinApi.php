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
use Celcoin\Helpers\Cache;

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

        $this->getToken();
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
        $client = new Client();
        $this->response = $client->request($method, $uri, $options);

        if ($this->response->getStatusCode() == 200) {
            return json_decode($this->response->getBody(), true);
        }

        return false;
    }

    /**
     * Get Token access
     *
     * @return array|bool|mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken()
    {
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['paths']['Token'];

        $options = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->secretKey,
            ]
        ];

        $response = $this->sendRequest('POST', $uri, $options);

        if ($response === false) {
            return '';
        }

        $this->token = $response;

        $cache = new Cache();
        $cache->saveTokenInCache($this->token, 200);

        return $this->token;

    }

    /**
     * Get details of the current Merchant
     *
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMerchantInfo()
    {
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['version'] . $this->configs['paths']['Merchant'];

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
    public function getProviders()
    {
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['version'] . $this->configs['paths']['Providers'];

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
     * Get a list of all provider values
     *
     * @param $regionalCode Code of regional
     * @param $providerId   Provider unique identifier
     * @return bool|mixed|string
     */
    public function getProvidersValues($regionalCode, $providerId)
    {
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['version'] . $this->configs['paths']['ProviderValues'];

        $options = [
            'headers' => [
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token'],
            ],
            'query' => [
                'regional_code' => $regionalCode,
                'providerId' => $providerId
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
     * @param $countryCode    Country identifier code
     * @param $providerId     Provider unique identifier
     * @param bool $capture Auto-commit (Default is true)
     * @return bool|mixed|string
     */
    public function topups($value, $regionalCode, $phoneNumber, $countryCode, $providerId, $capture = true)
    {
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['version'] . $this->configs['paths']['Topup'];

        $options = [
            'headers' => [
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token'],
            ],
            'form_params' => [
                'valor' => $value,
                'regional_code' => $regionalCode,
                'phone_number' => $phoneNumber,
                'country_code' => $countryCode,
                'providerId' => $providerId,
                'capture' => $capture,
            ]
        ];

        $response = $this->sendRequest('POST', $uri, $options);

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
        $uri = $this->debug ? $this->configs['api_homologation'] : $this->configs['api'];
        $uri .= $this->configs['version'] . $this->configs['paths']['Transaction'];

        $options = [
            'headers' => [
                'Authorization' => $this->token['token_type'] . ' ' . $this->token['access_token'],
            ],
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