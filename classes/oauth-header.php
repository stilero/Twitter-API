<?php
/**
 * Class OAuth Header
 * Class for creating oAuth http headers
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class oAuth
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-aug-02 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
class OauthHeader {
    
    /**
     * Generates an oAuth Header by combining the 7 parameters to a header String
     * @param string $oauth_consumer_key The oauth_consumer_key identifies which application is making the request.
     * @param string $oauth_nonce The oauth_nonce parameter is a unique token your application should generate for each unique request.
     * @param string $oauth_signature The oauth_signature parameter contains a value which is generated by the signature method of the Signature class.
     * @param string $oauth_signature_method The oauth_signature_method to use. Typically HMAC-SHA1.
     * @param string $oauth_timestamp The oauth_timestamp parameter indicates when the request was created. This value should be the number of seconds since the Unix epoch at the point the request is generated.
     * @param string $oauth_token The oauth_token parameter typically represents a user's permission to share access to their account with your application.
     * @param string $oauth_version The oauth_version parameter should typically allways be 1.0 for any request.
     * @return string header string This value should be set as the Authorization header for the request.
     */
    public static function header($oauth_consumer_key, $oauth_nonce, $oauth_signature, $oauth_signature_method, $oauth_timestamp, $oauth_token, $oauth_version){
        foreach (get_defined_vars() as $key => $value) {
            $encodedKeyValues[] = OauthHelper::safeEncode($key).'="'.OauthHelper::safeEncode($value).'"';
        }
        $headerString = 'OAuth '.implode(', ', $encodedKeyValues);
        return $headerString;
    }
}
?>
