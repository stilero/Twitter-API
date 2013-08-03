<?php
/**
 * Curler handles server communication using cURL
 *
 * @version  1.3
 * @author Daniel Eliasson - joomla at stilero.com
 * @copyright  (C) 2013-aug-01 Stilero Webdesign http://www.stilero.com
 * @category Classes
 * @license	GPLv2
 */

class Curler {
    
    protected $_config;
    protected $header;
    protected $_isPost;
    protected $_curlHandler;
    protected $url;
    protected $postParams;
    protected $_response;
    protected $_responseInfoParts;
    protected $_cookieFile;
    protected $_isCustomRequest = false;
    protected $_customRequestMethod;
    const HTTP_STATUS_OK = '200';
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_GET = 'GET';
    const REQUEST_METHOD_DELETE = 'DELETE';
    const REQUEST_METHOD_CONNECT = 'CONNECT';

    /**
     * Sends requests to servers with curl methods
     * @param string $url The server url to contact
     * @param string/array $postParams The Post parameters to send. Can be either a query string or an array. For example foo=bar&baz=boom&cow=milk&php=hypertext+processor or array('foo'=>'bar')
     * @param array $config config parameter for the class
     */
    function __construct($url="", $postParams="", $config="") {
        $this->_isPost = false;
        $this->url = $url;
        $this->setPostParams($postParams);
        
        $this->_config = 
            array(
                'curlUserAgent'         =>  'Curler - www.stilero.com',
                'curlConnectTimeout'    =>  20,
                'curlTimeout'           =>  20,
                'curlReturnTransf'      =>  true, //return the handle as a string
                'curlSSLVerifyPeer'     =>  false,
                'curlFollowLocation'    =>  false,
                'curlProxy'             =>  false,
                'curlProxyPassword'     =>  false,
                'curlEncoding'          =>  false,
                'curlHeader'            =>  false, //Include the header in the output
                'curlHeaderOut'         =>  true,
                'curlUseCookies'        =>  true,
                'debug'                 =>  false,
                'eol'                   =>  "<br /><br />"
            );
        if(is_array($config)) {
            $this->_config = array_merge($this->_config, $config);
        }
    }
        
    /**
     * Sets up standard Curl settings
     */
    private function _defineStandardSettings(){
        curl_setopt_array(
            $this->_curlHandler, 
            array(
                CURLOPT_URL             =>  $this->url,
                CURLOPT_USERAGENT       =>  $this->_config['curlUserAgent'],
                CURLOPT_CONNECTTIMEOUT  =>  $this->_config['curlConnectTimeout'],
                CURLOPT_TIMEOUT         =>  $this->_config['curlTimeout'],
                CURLOPT_RETURNTRANSFER  =>  $this->_config['curlReturnTransf'],
                CURLOPT_SSL_VERIFYPEER  =>  $this->_config['curlSSLVerifyPeer'],
                CURLOPT_FOLLOWLOCATION  =>  $this->_config['curlFollowLocation'],
                CURLOPT_PROXY           =>  $this->_config['curlProxy'],
                CURLOPT_ENCODING        =>  $this->_config['curlEncoding'],
                CURLOPT_HEADER          =>  $this->_config['curlHeader'],
                CURLINFO_HEADER_OUT     =>  $this->_config['curlHeaderOut']
            )
        );
    }
    
    /**
     * Initiates custom request if custom request is defined
     */
    private function _defineCustomRequest(){
        if($this->_isCustomRequest){
            curl_setopt($this->_curlHandler, CURLOPT_CUSTOMREQUEST, $this->_customRequestMethod);
        }
    }
    
    /**
     * Defines Post mode and sets true if POST is defined.
     */
    //To-DO: Move postfields our to a separate method to allow posting using GET
    private function _definePostMode(){
        if($this->_isPost){
            curl_setopt($this->_curlHandler, CURLOPT_POST, $this->_isPost);
            curl_setopt($this->_curlHandler, CURLOPT_POSTFIELDS, $this->postParams);
        }
    }
    
    protected function _generateHTTPHeader(){
        if(isset($this->header)){
            return;
        }
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,"; 
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5"; 
        $header[] = "Cache-Control: max-age=0"; 
        $header[] = "Connection: keep-alive"; 
        $header[] = "Keep-Alive: 300"; 
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7"; 
        $header[] = "Accept-Language: en-us,en;q=0.5"; 
        $header[] = "Pragma: ";  
        $this->header = $header;
    }
    
    /**
     * Defines http header according to global header
     */
    private function _defineHeader(){
        $this->_generateHTTPHeader();
        curl_setopt($this->_curlHandler, CURLOPT_HTTPHEADER, $this->header);
    }
    
    /**
     * Defines Proxy password
     */
    private function _defineProxyPassword(){
        if ($this->_config['curlProxyPassword'] !== false) {
            curl_setopt($this->_curlHandler, CURLOPT_PROXYUSERPWD, $this->_config['curl_proxyuserpwd']);
        } 
    }
    
    /**
     * Creates Cookie File is this is defined in the config
     */
    private function _createCookieFile(){
        if(!$this->_config['curlUseCookies']){
            break;
        }
        if (!defined('DS')){
            define('DS', DIRECTORY_SEPARATOR);
        }
        try {
            $this->_cookieFile = tempnam(DS."tmp", "cookies");
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        if (!$this->_cookieFile){
            break;
        }
        curl_setopt($this->_curlHandler, CURLOPT_COOKIEFILE, $this->_cookieFile);
        curl_setopt($this->_curlHandler, CURLOPT_COOKIEJAR, $this->_cookieFile);
    }
    
    /**
     * Sets up all settings required for curling
     */
    private function _setupCurl(){
        $this->_defineStandardSettings();
        $this->_defineCustomRequest();
        $this->_definePostMode();
        $this->_defineHeader();
        $this->_defineProxyPassword();
        $this->_createCookieFile();
    }       
    
    /**
     * Resets the response to start from scratch
     */
    public function resetResponse(){
        $this->_response = '';
        $this->_responseInfoParts = array();
    }
    
    /**
     * Deletes the cookie file.
     */
    private function _deleteCookieFile(){
        if($this->_cookieFile != "" && $this->_config['curlUseCookies']){
            unlink($this->_cookieFile);
        }
    }
    
    /**
     * Executes the request and returns the raw response
     * @return string raw server response
     */
    public function doCurl(){
        $this->resetResponse();
        $this->_curlHandler = curl_init(); 
        $this->_setupCurl();
        $response = curl_exec ($this->_curlHandler);
        $this->_response = $response;
        $this->_responseInfoParts = curl_getinfo($this->_curlHandler); 
        curl_close ($this->_curlHandler);
        $this->_deleteCookieFile();
        return $response;
    }    
    
    /**
     * Sets the URL to use for the curl request
     * @param string $url The URL to Call
     */
    public function setUrl($url){
        $this->url = $url;
    }
    
    /**
     * Set a custom HTTP header
     * @param string $header The complete http header to use for the call
     */
    public function setHeader($header=''){
        $this->header = $header;
    }
    
    /**
     * Sets a custom request method.
     * @param string $requestMethod  The method to use (GET, POST, DELETE, CONNECT...). Use the constants defined for this class.
     */
    public function setCustomRequest($requestMethod){
        $this->_isCustomRequest = true;
        $this->_customRequestMethod = $requestMethod;
    }
    
    /**
     * Sets the postvars to use for the request.
     * @param string/array $postVars The Post parameters to send. Can be either a query string or an array. For example foo=bar&baz=boom&cow=milk&php=hypertext+processor or array('foo'=>'bar')
     */
    public function setPostParams($postVars){
        if(!empty($postVars)){
            if(is_array($postVars)){
                $this->_isPost = true;
                $this->postParams = http_build_query($postVars);
            }else{
                $this->postParams = $postVars;
                $this->_isPost = true;
            }
        }
    }
    
    /**
     * Get the server response
     * @return string Raw server response
     */
    public function getResponse(){
        return $this->_response;
    }
    /**
     * Get the response info after the call
     * @return array The info parts from the server
     */
    public function getResponseInfo(){
        return $this->_responseInfoParts;
    }
    
    /**
     * Get the response info HTTP code
     * @return string The response HTTP code
     */
    public function getResponseInfoHTTPCode(){
        return $this->_responseInfoParts['http_code'];
    }
    
    /**
     * Checks if the response from the server is OK = 200
     * @return boolean True or false
     */
    public function isResponseOK(){
        if ($this->_responseInfoParts['http_code'] == self::HTTP_STATUS_OK) {
            return true;
        }else{
            return false;
        }
    }
    
    public function __get($name) {
        return $this->$name;
    }
    
    public function __set($name, $value) {
        $this->$name = $value;
    }
}