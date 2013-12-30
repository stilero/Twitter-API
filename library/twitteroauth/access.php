<?php
/**
 * Access Class
 * Just a wrapper for the oauth User class
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Twitter-API
 * @author Daniel Eliasson <daniel at stilero.com>
 * @copyright  (C) 2013-dec-30 Stilero Webdesign (http://www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class StileroTwitterOauthAccess extends StileroOauthUser{
    
    /**
     * Just a wrapper for Oauth User class for naming conventions
     * @param string $token Access Token obtained from Twitter
     * @param string $tokenSecret Access Token secret obtained from Twitter
     */
    public function __construct($token, $tokenSecret) {
        parent::__construct($token, $tokenSecret);
    }
}
