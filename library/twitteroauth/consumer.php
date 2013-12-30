<?php
/**
 * Consumer Class
 * Just for wrapping the client class up to use the same naming as twitter.
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

class StileroTwitterOauthConsumer extends StileroOauthClient{
    
    /**
     * Twitter Consumer is a wrapper for oauth Client.
     * @param string $key Consumer Key obtained from Twitter
     * @param string $secret Consumer Secret obtained from Twitter
     */
    public function __construct($key, $secret) {
        parent::__construct($key, $secret);
    }
}
