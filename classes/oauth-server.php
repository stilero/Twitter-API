<?php
/**
 * Class Server
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class Twitter
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-jan-06 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */


class OauthServer extends OauthCommunicator{
    
    private $OauthClient;
    private $OauthUser;
    private $nonce;
    private $timestamp;
    private $baseString;
    private $signature;
    private $signingKey;
    private $signingParams;
    private $authParams;
    private $requestMethod;
    private $headers;
    private $authHeader;
    protected $url;
    private $data;
    
    const OAUTH_VERSION  = '1.0';
    const SIGN_METHOD = 'HMAC-SHA1';
    const REQ_METHOD_POST = 'POST';
    const REG_METHOD_GET = 'GET';
    
    
    public function __construct($OauthClient, $OauthUser, $url = "", $postVars = "", $config = "") {
        parent::__construct($url, $postVars, $config);
        $this->OauthClient = $OauthClient;
        $this->OauthUser = $OauthUser;
    }
    
    /**
     * Generates an unique number "NONCE"
     * @param int $length
     */
    protected function generateNonce($length=12){
        $characters = array_merge(range(0,9), range('A','Z'), range('a','z'));
        $length = $length > count($characters) ? count($characters) : $length;
        shuffle($characters);
        $prefix = microtime();
        $this->nonce = md5(substr($prefix . implode('', $characters), 0, $length));
    }
    
    /**
     * Generates a Timestamp
     */
    protected function generateTimestamp(){
        $this->timestamp = time();
    }
    
    /**
     * Collects and returns all default values for oauth.
     * @return Array
     */
    private function getDefaults() {
        $defaults = array(
            'oauth_consumer_key' => $this->OauthClient->key,
            'oauth_nonce' => $this->nonce,
            'oauth_signature_method' => self::SIGN_METHOD,
            'oauth_version' => self::OAUTH_VERSION,
            'oauth_timestamp' => $this->timestamp,
        );
        if ( $this->OauthUser->accessToken ){
            $defaults['oauth_token'] = $this->OauthUser->accessToken;
        }
        foreach ($defaults as $key => $value) {
            $_defaults[$this->safeEncode($key)] = $this->safeEncode($value);
        }
        return $_defaults;
    }
    
    /**
     * Encodes data in an array and returns an encoded string
     * @param Array/string $data
     * @return string
     */
    private function safeEncode($data) {
        if (is_array($data)) {
            return array_map(array($this, 'safeEncode'), $data);
        } else if (is_scalar($data)) {
            return str_ireplace( array('+', '%7E'), array(' ', '~'), rawurlencode($data) );
        } else {
            return '';
        }
    }
    
    /**
     * Decodes data and returns an decoded string
     * @param Array/string $data
     * @return string
     */
    private function safeDecode($data) {
        if (is_array($data)) {
            return array_map(array($this, 'safe_decode'), $data);
        } else if (is_scalar($data)) {
            return rawurldecode($data);
        } else {
            return '';
        }
    }
    
    /**
     * Generates a signing key string
     */
    private function generateSigningKey() {
        $this->signingKey = $this->safeEncode($this->OauthClient->secret) . '&' . $this->safe_encode($this->OauthUser->tokenSecret);
    }
    
    /**
     * Generates a Base String
     */
    private function generateBaseString(){
        $base = array(
            $this->requestMethod,
            $this->url,
            $this->data
        );
        $this->baseString = implode('&', $this->safe_encode($base));
    }
    
    /**
     * Generates signing parameters
     * @param Array $params
     */
    private function generateSigningParams($params) {
        $this->signingParams = array_merge($this->get_defaults(), (array)$params);
        uksort($this->signingParams, 'strcmp');
        foreach ($this->signingParams as $key => $value) {
            $key = $this->safeEncode($key);
            $value = $this->safeEncode($value);
            $signingParams[$key] = $value;
            $keyValue[] = "{$key}={$value}";
        }
        $this->authParams = array_intersect_key($this->get_defaults(), $signingParams);
        $this->signingParams = implode('&', $keyValue);
    }
    
    /**
     * Sanitizes and sets the url to use for the query
     * @param string $url
     */
    public function setURL($url) {
        $parts = parse_url($url);
        $port = isset($parts['port']) ? $parts['port'] : '';
        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $path = isset($parts['path']) ? $parts['path'] : '';
        $port or $port = ($scheme == 'https') ? '443' : '80';
        if(($scheme == 'https' && $port != '443') || ($scheme == 'http' && $port != '80')) {
            $host = "$host:$port";
        }
        $this->url = strtolower("$scheme://$host");
        $this->url .= $path;
    }
    
    /**
     * Prepares and sets the parameters for the call
     * @param type $params
     */
    private function generateParams($params) {
        $defaultParams = $this->getDefaults();
        $this->signingParams = array_merge($defaultParams, (array)$params);
        if (isset($this->signingParams['oauth_signature'])) {
            unset($this->signingParams['oauth_signature']);
        }
        //$this->generateOauthSignature();
        //$this->signingParams['oauth_signature'] = $this->authParams['oauth_signature'];
        uksort($this->signingParams, 'strcmp');
        foreach ($this->signingParams as $key => $value) {
            $key = $this->safeEncode($key);
            $value = $this->safeEncode($value);
            $_signing_params[$key] = $value;
            $kv[] = "{$key}={$value}";
        }
        $this->authParams = array_intersect_key($defaultParams, $_signing_params);
        if (isset($_signing_params['oauth_callback'])) {
            $this->authParams['oauth_callback'] = $_signing_params['oauth_callback'];
            unset($_signing_params['oauth_callback']);
        }
        if (isset($_signing_params['oauth_verifier'])) {
            $this->authParams['oauth_verifier'] = $_signing_params['oauth_verifier'];
            unset($_signing_params['oauth_verifier']);
        }
        $this->signingParams = implode('&', $kv);
    }
    
    /**
     * Prepares and sets the base string
     */
    private function setBaseString() {
        $base = array(
            $this->requestMethod,
            $this->url,
            $this->signingParams
        );
        $this->baseString = implode('&', $this->safeEncode($base));
    }
    
    private function setSigningKey() {
        $this->signingKey = $this->safeEncode($this->OauthClient->secret) 
                . '&' . $this->safeEncode($this->OauthUser->tokenSecret);
    }
    
    private function setAuthHeader() {
        $this->headers = array();
        uksort($this->authParams, 'strcmp');
        foreach ($this->authParams as $key => $value) {
          $keyvalue[] = "{$key}=\"{$value}\"";
        }
        $this->authHeader = 'OAuth ' . implode(', ', $keyvalue);
    }
    
    public function setHeader($header=''){
        $this->headers['Authorization'] = $this->authHeader;
        foreach ($this->headers as $key => $value) {
            $headers[] = trim($key . ': ' . $value);
        }
        $this->header = $headers;
    }
    
    private function generateOauthSignature(){
        $this->authParams['oauth_signature'] = $this->safeEncode(
                base64_encode(
                    hash_hmac(
                        'sha1', $this->baseString, $this->signingKey, true
                    )
                )
            );
    }
    
    private function sign($method, $url, $params, $useauth=true) {
        $this->requestMethod = $method;
        $this->setURL($url);
        $this->generateParams($params);
        if ($useauth) {
            $this->setBaseString();
            $this->setSigningKey();
            $this->generateOauthSignature();
            $this->setAuthHeader();
            $this->setHeader();
        }
    }
    
    public function request($url, $params=array(), $method="POST", $useauth=true, $headers=array()) {
        $this->generateNonce();
        $this->generateTimestamp();
        if (!empty($headers)){
            $this->headers = array_merge((array)$this->headers, (array)$headers);
        }
        $this->sign($method, $url, $params, $useauth);
        //$this->postVars = $this->signingParams;
        if($method == 'POST'){
            $this->_isPost = true;
        }
        $this->setPostVars($params);
        //$this->postVars = $params;
        return $this->query();
    }
}
