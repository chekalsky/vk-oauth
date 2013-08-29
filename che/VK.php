<?php
namespace che;

/**
* A powerfull PHP library for the VK OAuth API (vk.com social network).
* 
* @link https://github.com/chekalskiy/vk-oauth
* @author Ilya Chekalskiy <ilya@chekalskiy.ru>
*/
class VK {
    protected $apiUrl         = 'https://api.vk.com/method/';
    protected $authorizeUrl   = 'https://oauth.vk.com/authorize';
    protected $accessTokenUrl = 'https://oauth.vk.com/access_token';
    protected $apiVersion     = '5.0';
    protected $forceHttps     = false;
    protected $clientId       = null;
    protected $clientSecret   = null;
    protected $accessToken    = null;


    public function __construct($clientId, $clientSecret, $accessToken = null) {
        if (!extension_loaded('curl')) {
            throw new VKException('Curl must be installed to use this library.', VKException::CURL_NOT_FOUND);
        }

        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        if (!empty($accessToken))
            $this->setAccessToken($accessToken);
    }

    /**
     * Get Authentication URL
     *
     * @param string $redirect_uri  This URL will get the code parameter
     * @param string $scope  Scope of the access delimited by comma
     * @param string $display  Can be 'page', 'popup' or 'mobile'
     * @return string URL for user redirect
     */
    public function getAuthenticationUrl($redirect_uri, $scope = '', $display = 'page') {
        $parameters = array(
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $redirect_uri,
            'display'       => $display,
            'scope'         => $scope
        );

        return $this->authorizeUrl . '?' . http_build_query($parameters, null, '&');
    }

    /**
    * Authorization Code Flow
    * 
    * @return array
    */
    public function getAccessToken($code, $redirect_uri) {
        $response = $this->getGrantAccessToken(array('code' => $code, 'redirect_uri' => $redirect_uri, 'grant_type'=>'authorization_code'));

        return $response;
    }

    /**
    * Client Credentials Flow authorization
    * 
    * @return string
    */
    public function getServerAccessToken() {
        $response = $this->getGrantAccessToken(array('grant_type' => 'client_credentials'));

        return $response;
    }

    /**
     * Get Aceess Token
     *
     * @param array $parameters
     * @return array
     */
    private function getGrantAccessToken($parameters = array()) {
        switch ($parameters['grant_type']) {
            case 'client_credentials': break;
            default:
                if (!isset($parameters['code']) || empty($parameters['code']))
                    throw new VKException('Empty code parameter.', VKException::CODE_NOT_DEFINED);
        }
        
        $parameters['client_id'] = $this->clientId;
        $parameters['client_secret'] = $this->clientSecret;
        $parameters['v'] = $this->apiVersion;

        $response = $this->executeRequest($this->accessTokenUrl, $parameters, 'POST');
        
        return $response;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return void
     */
    public function setAccessToken($token) {
        $this->accessToken = $token;
    }

    /**
     * Set API version
     *
     * @param string $apiVersion
     * @return void
     */
    public function setApiVersion($apiVersion) {
        $this->apiVersion = $apiVersion;
    }
    
    /**
    * Magic call of API function
    *    
    * @return void
    */
    public function __call($name, $arguments) {
        $method = str_replace('_', '.', $name);
        $parameters = (is_array($arguments[0])) ? $arguments[0] : array();
        
        return $this->get($method, $parameters);
    }
    
    /**
    * GET-call of VK API
    * 
    * @param string $method  Method name, e.g. "wall.get"
    * @param array $parameters  Request parameters
    * @return array
    */
    public function get($method, $parameters = array()) {
        $parameters['https'] = intval($this->forceHttps);
        $parameters['v'] = $this->apiVersion;

        // If method is secure (Client Credentials Flow) then attach clientSecret
        if (substr($method, 0, 7) === 'secure.') {
            $parameters['client_secret'] = $this->clientSecret;
        }

        $url = $this->getUrl($method);
        $result = $this->fetch($url, $parameters, "GET");
        
        return $result['response'];
    }

    /**
    * POST-call of VK API
    * 
    * @param string $method  Method name, e.g. "users.get"
    * @param array $parameters  Request parameters
    * @return array
    */
    public function post($method, $parameters = array()) {
        $parameters['https'] = intval($this->forceHttps);
        $parameters['v'] = $this->apiVersion;

        // If method is secure (Client Credentials Flow) then attach clientSecret
        if (substr($method, 0, 7) === 'secure.') {
            $parameters['client_secret'] = $this->clientSecret;
        }

        $url = $this->getUrl($method);
        $result = $this->fetch($url, $parameters, "POST");

        return $result['response'];
    }
    
    /**
    * Get full URL for API request
    * 
    * @param string $method Method name
    * @return string
    */
    private function getUrl($method) {
        if (preg_match('/https?:\/\//i', $method) == 0) {
            $method = preg_replace('/^\//', '', $method, 1);
            return $this->apiUrl . $method;
        } return $method;
    }

    /**
     * Fetch a resource
     *
     * @param string $url Resource URL
     * @param array  $parameters Array of parameters
     * @param string $http_method HTTP Method to use (POST, PUT, GET, HEAD, DELETE)
     * @return array
     */
    public function fetch($url, $parameters = array(), $http_method = 'GET') {
        if ($this->accessToken) {
            if (is_array($parameters)) {
                $parameters['access_token'] = $this->accessToken;
            } else {
                throw new VKException('You need to give parameters as array if you want to give the token within the URI.', VKException::REQUIRE_PARAMS_AS_ARRAY);
            }
        }

        return $this->executeRequest($url, $parameters, $http_method);
    }

    /**
     * Execute a request using curl
     *
     * @param string $url URL
     * @param mixed  $parameters Array of parameters
     * @param string $http_method HTTP Method
     * @param array  $http_headers HTTP Headers
     * @return array
     */
    private function executeRequest($url, $parameters = array(), $http_method = 'GET') {
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CUSTOMREQUEST  => $http_method
        );

        switch ($http_method) {
            case 'POST':
                $curl_options[CURLOPT_POST] = true;
                $curl_options[CURLOPT_POSTFIELDS] = $parameters;
                break;
            default:
                if (is_array($parameters)) {
                    $url .= '?' . http_build_query($parameters, null, '&');
                } elseif ($parameters) {
                    $url .= '?' . $parameters;
                }
                break;
        }

        $curl_options[CURLOPT_URL] = $url;

        $ch = curl_init();
        curl_setopt_array($ch, $curl_options);
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        
        if ($curl_error = curl_error($ch)) {
            throw new VKException($curl_error, VKException::CURL_ERROR);
        } else {
            $json_decode = json_decode($result, true);
        }
        curl_close($ch);

        // Handling API errors
        if ($http_code === 200 && json_last_error() !== \JSON_ERROR_NONE)
            throw new VKException('JSON decoding error', json_last_error());
        if ($http_code !== 200 || isset($json_decode['result']['error']))
            VKException::raise(array('result' => $json_decode, 'code' => $http_code));

        return $json_decode;
    }
}
