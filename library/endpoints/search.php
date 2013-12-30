<?php
/**
 * Class Twitter Search
 *
 * @version  1.1
 * @package Stilero
 * @subpackage Class Twitter
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-aug-01 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
class StileroTwitterSearch extends StileroOauthServer{
    
    const API_RESOURCE_URL = 'https://api.twitter.com/1.1/search/tweets.json';
    
    /**
     * Class for finding relevant Tweets based on queries
     * @param \StileroTwitterOauthConsumer $OauthConsumer
     * @param \StileroTwitterOauthAccess $OauthAccess
     */
    public function __construct(\StileroTwitterOauthConsumer $OauthConsumer, \StileroTwitterOauthAccess $OauthAccess) {
        parent::__construct($OauthConsumer, $OauthAccess);
    }
    
    /**
     * Returns a collection of relevant Tweets matching a specified query.
     * @param string $query A UTF-8, URL-encoded search query of 1,000 characters 
     *                      maximum, including operators. Example Values: @noradio
     * @return string JSON Response
     */
    public function search($query){
        $params = array(
            'q' => urlencode($query)
        );
        $this->sendRequest(self::API_RESOURCE_URL, $params, self::REQUEST_METHOD_GET);
        return $this->getResponse();
    }
}
?>
