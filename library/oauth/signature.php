<?php
/**
 * Class OAuth Signature Class
 * Class for creating and generating oAuth Signatures.
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class oAuth
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-aug-02 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class StileroOauthSignature {
    
    const SIGNATURE_METHOD = 'HMAC-SHA1';
    const OAUTH_VERSION = '1.0';
    
    /**
     * Collects an array with params and returns a signature string.
     * @param array $params all the parameters to sign
     * @return string signature
     */
    public static function signParams($params){
        uksort($params, 'strcmp');
        foreach ($params as $key => $value) {
            $key = StileroOauthEncryption::safeEncode($key);
            $value = StileroOauthEncryption::safeEncode($value);
            $keyValues[] = "{$key}={$value}";
        }
        $signature = implode('&', $keyValues);
        return $signature;
    }
    
    /**
     * Creates the Signature Base String. The three values collected must be 
     * joined to make a single string, from which the signature will be generated. 
     * This is called the signature base string by the OAuth specification.
     * @param string $httpMethod The http method to use, for example: POST,GET,DELETE,CONNECT etc.
     * @param string $baseUrl The base server url to call, for example: http://www.myserver.com
     * @param string $paramString The signed parameter string to use. Typically generated by sending an array or params to the sign method of this class.
     * @return string The base string
     */
    public static function baseString($httpMethod, $url, $paramString){
        $bases = array(
            $httpMethod,
            $url,
            $paramString
        );
        $baseString = implode('&', StileroOauthEncryption::safeEncode($bases));
        return $baseString;
    }
    
    /**
     * Generates and returns a signing key based on the Oauth Consumer Secret
     * and the Oauth Token Secret.
     * @param string $oauthConsumerSecret Oauth Consumer Secret
     * @param string $oauthTokenSecret Oauth Token Secret
     * @return string Signing Key
     */
    public static function signingKey($oauthConsumerSecret, $oauthTokenSecret){
        $signingKey = StileroOauthEncryption::safeEncode($oauthConsumerSecret) . 
                '&' . StileroOauthEncryption::safeEncode($oauthTokenSecret);
        return $signingKey;
    }
    
    /**
     * Generates a oauth Signature by combining and encoding the base string and
     * the signing key
     * @param string $baseString
     * @param string $signingKey
     * @return string signature
     */
    public static function generateSignature($baseString, $signingKey){
        $signature = StileroOauthEncryption::safeEncode(
                base64_encode(
                    hash_hmac('sha1', $baseString, $signingKey, true)
                )
            );
        return $signature;
    }
    
    /**
     * Generates Oauth Defaults 
     * @param string $oauthConsumerKey
     * @param string $oauthAccessToken
     * @return array OauthDefaults
     */
    public static function oauthDefaults($oauthConsumerKey, $oauthAccessToken) {
        $oauthDefaults = array(
            'oauth_consumer_key' => $oauthConsumerKey,
            'oauth_nonce' => StileroOauthEncryption::nonce(),
            'oauth_signature_method' => self::SIGNATURE_METHOD,
            'oauth_version' => self::OAUTH_VERSION,
            'oauth_timestamp' => StileroOauthEncryption::timestamp(),
            'oauth_token' => $oauthAccessToken
        );
        foreach ($oauthDefaults as $key => $value) {
            $encodedOauthDefaults[StileroOauthEncryption::safeEncode($key)] = StileroOauthEncryption::safeEncode($value);
        }
        return $encodedOauthDefaults;
    }
}
?>
