<?php
/**
 * Twitter-API
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

class StileroTwitter{
    public $Tweets;
    public $Search;
    public $Timelines;
    
    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        $OauthConsumer = new StileroTwitterOauthConsumer($consumer_key, $consumer_secret);
        $OauthAccess = new StileroTwitterOauthAccess($access_token, $access_token_secret);
        $this->Tweets = new StileroTwitterTweets($OauthConsumer, $OauthAccess);
        $this->Search = new StileroTwitterSearch($OauthConsumer, $OauthAccess);
        $this->Timelines = new StileroTwitterTimelines($OauthConsumer, $OauthAccess);
    }
}
