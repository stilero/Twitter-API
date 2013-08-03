<?php
/**
 * Class Oauth Request
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class Oauth
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-aug-03 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
class OauthRequest extends Curler{
    private $OauthClient;
    private $OauthUser;
    private $_oauthParams;
    private $_oauthNonce;
    private $_oauthSignature;
    private $_oauthTimestamp;
    protected $url;
    private $_params;
    const OAUTH_VERSION  = '1.0';
    const SIGNATURE_METHOD = 'HMAC-SHA1';
    
    public function __construct(OauthClient $OauthClient, OauthUser $OauthUser, $url = "", $params = "", $config = "") {
        parent::__construct();
        $this->OauthClient = $OauthClient;
        $this->OauthUser = $OauthUser;
        //$this->url = $url;
        //$this->_params = $params;
    }
    
    /**
     * Generates HTTP header array
     * @param array $params
     * @param bool $useAuthorisation
     * @return array HTTP Headers
     */
    private function _headers($params, $useAuthorisation){
        //$headers = OauthHeader::defaults();
        if($useAuthorisation){
            $headers[] = 'Authorization: '.OauthHeader::authorizationHeader(
                    $this->OauthClient->key, 
                    $this->_oauthNonce, 
                    $this->_oauthSignature, 
                    self::SIGNATURE_METHOD, 
                    $this->_oauthTimestamp, 
                    $this->OauthUser->accessToken, 
                    self::OAUTH_VERSION
            );
        }
        $param = http_build_query($params);
        $paramLength = strlen($param);
        $paramLength = 0;
        $headers[] = 'Content-Length: '.$paramLength;
        return $headers;
    }
    
    /**
     * Sends the request to the server
     * @param string $url
     * @param array $params
     * @param string $httpMethod
     * @param bool $useAuthorisation
     * @return string JSON response
     */
    public function send($url, $params, $httpMethod=Curler::REQUEST_METHOD_POST, $useAuthorisation=true){
        $this->_oauthNonce = OauthHelper::nonce();
        $this->_oauthTimestamp = OauthHelper::timestamp();
        $this->_oauthSignature = OauthSignature::signature($httpMethod, $url, $this->OauthClient->secret, $this->OauthUser->tokenSecret, $params);
        $headers = $this->_headers($params, $useAuthorisation);
        $this->setHeaders($headers);
        $this->setPostParams($params);
        $this->setUrl($url);
        $response = $this->doCurl();
        return $response;
    }
    
    public function query($url){
        $this->_oauthParams = 
                array(  'oauth_consumer_key' => $this->OauthClient->key,
                        'oauth_nonce' => OauthHelper::nonce(),
                        'oauth_signature_method' => self::SIGNATURE_METHOD,
                        'oauth_token' => $this->OauthUser->accessToken,
                        'oauth_timestamp' => OauthHelper::timestamp(),
                        'oauth_version' => self::OAUTH_VERSION
                );
        $signingKey = OauthSignature::signingKey($this->OauthClient->secret, $this->OauthUser->tokenSecret);
        $signedString = OauthSignature::signParams($this->_oauthParams);
        $baseString = OauthSignature::baseString(Curler::REQUEST_METHOD_POST, $url, $signedString);
        $oauthSignature = OauthSignature::generateSignature($baseString, $signingKey);
        $this->_oauthParams['oauth_signature'] = $oauthSignature;
        $authorisationHeader = 'Authorization: OAuth '; 
        $values = array(); 
        foreach($this->_oauthParams as $key=>$value){
            $values[] = "$key=\"" . rawurlencode($value) . "\""; 
        }
        $authorisationHeader .= implode(', ', $values); 
        $header = array($authorisationHeader, 'Expect:');
        $options = array( 
                CURLOPT_HTTPHEADER => $header,
                CURLOPT_HEADER => false,
                CURLOPT_URL => $url,
                //CURLOPT_POSTFIELDS => $postvars,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false
        );
        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);
        return $json;
    }
}
?>
