<?php
/**
 * Oauth User Class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class FB
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-jan-06 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class StileroOauthUser{
    
    public $token;
    public $tokenSecret;
    
    /**
     * Generates an Oauth User Class, also referred to as Consumer
     * @param string $token Oauth Access Token
     * @param string $tokenSecret Oauth Token Secret
     */
    public function __construct($token, $tokenSecret) {
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;
    }
}
