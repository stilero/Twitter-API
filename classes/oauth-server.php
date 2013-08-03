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


class OauthServer extends Curler{
    
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
    protected $headers;
    private $authHeader;
    protected $url;
    private $data;
    
    const OAUTH_VERSION  = '1.0';
    const SIGN_METHOD = 'HMAC-SHA1';
    
    
    public function __construct(OauthClient $OauthClient, OauthUser $OauthUser, $url = "", $postParams = "", $config = "") {
        parent::__construct($url, $postParams, $config);
        $this->OauthClient = $OauthClient;
        $this->OauthUser = $OauthUser;
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
            $_defaults[OauthHelper::safeEncode($key)] = OauthHelper::safeEncode($value);
        }
        return $_defaults;
    }
    
    /**
     * Generates a signing key string
     */
    private function generateSigningKey() {
        $this->signingKey = OauthHelper::safeEncode($this->OauthClient->secret) . '&' . OauthHelper::safeEncode($this->OauthUser->tokenSecret);
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
            $key = OauthHelper::safeEncode($key);
            $value = OauthHelper::safeEncode($value);
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
        $this->url = OauthHelper::sanitizeURL($url);
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
            $key = OauthHelper::safeEncode($key);
            $value = OauthHelper::safeEncode($value);
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
        $this->baseString = implode('&', OauthHelper::safeEncode($base));
    }
    
    private function setSigningKey() {
        $this->signingKey = OauthHelper::safeEncode($this->OauthClient->secret) 
                . '&' . OauthHelper::safeEncode($this->OauthUser->tokenSecret);
    }
    
    private function setAuthHeader() {
        $this->_headers = array();
        uksort($this->authParams, 'strcmp');
        foreach ($this->authParams as $key => $value) {
          $keyvalue[] = "{$key}=\"{$value}\"";
        }
        $this->authHeader = 'OAuth ' . implode(', ', $keyvalue);
    }
    
    public function setHeaders($header=''){
        $this->_headers['Authorization'] = $this->authHeader;
        foreach ($this->_headers as $key => $value) {
            $headers[] = trim($key . ': ' . $value);
        }
        $this->_headers = $headers;
    }
    
    private function generateOauthSignature(){
        $this->authParams['oauth_signature'] = OauthHelper::safeEncode(
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
            $this->setHeaders();
        }
    }
    
    public function request($url, $params=array(), $method="POST", $useauth=true, $headers=array()) {
        $this->setPostParams($params);
        $this->nonce = OauthHelper::nonce();
        $this->timestamp = OauthHelper::timestamp();
        if (!empty($headers)){
            $this->_headers = array_merge((array)$this->_headers, (array)$headers);
        }
        $this->sign($method, $url, $this->postParams, $useauth);
        //$this->postVars = $this->signingParams;
        /**
        if($method == 'POST'){
            $this->_isPost = true;
        }
         * 
         */
        //$this->setPostParams($params);
        //$this->postVars = $params;
        return $this->doCurl();
    }
}
