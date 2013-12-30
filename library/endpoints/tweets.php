<?php
/**
 *  Class Twitter Status
 *  Timelines are collections of Tweets, ordered with the most recent first.
 * @version  1.1
 * @package Stilero
 * @subpackage Class Twitter
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-jan-06 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
class StileroTwitterTweets extends StileroOauthServer{
    
    const API_BASE_URL = 'https://api.twitter.com/1.1/statuses/';
    const API_UPDATE_URL  = 'update.json';
    const API_DESTROY_URL  = 'destroy/';
    const API_RETWEET_URL  = 'retweet/';
    const API_SHOW_URL  = 'show/';
    const API_SHOW_RETWEETS_URL  = 'retweets/';
    const API_UPDATE_WITH_MEDIA  = 'update_with_media.json';
    const API_URL_ENDING  = '.json';
    
    public function __construct(\StileroTwitterOauthConsumer $OauthConsumer, \StileroTwitterOauthAccess $OauthAccess) {
        parent::__construct($OauthConsumer, $OauthAccess);
        return $this;
    }
    
    /**
     * Update twitter status
     * @param string $message
     * @return string JSON response
     */
    public function update($message) {
        $params = array('status' => $message);
        $apiUrl = self::API_BASE_URL.self::API_UPDATE_URL;
        $this->sendRequest($apiUrl, $params, self::REQUEST_METHOD_POST);
        return $this->getResponse();
    }
    
    /**
     * Deletes a twitter status
     * @param string $tweetID A numerical ID for the Tweet to delete
     * @return string JSON response
     */
    public function destroy($tweetID){
        $apiUrl = self::API_BASE_URL.self::API_DESTROY_URL.$tweetID.self::API_URL_ENDING;
        $this->sendRequest($apiUrl, array(),self::REQUEST_METHOD_POST);
        return $this->getResponse();
    }
    
    /**
     * Retweet a certain tweet
     * @param string $tweetID A numerical Tweet ID
     * @return string JSON response
     */
    public function retweet($tweetID){
        $apiUrl = self::API_BASE_URL.self::API_RETWEET_URL.$tweetID.self::API_URL_ENDING;
        $this->sendRequest($apiUrl);
        return $this->getResponse();
    }
    
    /**
     * Show a certain tweet
     * @param string $tweetID a numerical tweet id
     * @return string JSON response
     */
    public function show($tweetID){
        $apiUrl = self::API_BASE_URL.self::API_SHOW_URL.$tweetID.self::API_URL_ENDING;
        $this->sendRequest($apiUrl, array(), self::REQUEST_METHOD_GET);
        return $this->getResponse();
    }
    
    /**
     * Get up to 100 of the first retweets of a certain tweet
     * @param string $tweetID a numerical tweet ID
     * @return string JSON response
     */
    public function showRetweets($tweetID){
        $apiUrl = self::API_BASE_URL.self::API_SHOW_RETWEETS_URL.$tweetID.self::API_URL_ENDING;
        $this->sendRequest($apiUrl, array(), self::REQUEST_METHOD_GET);
        return $this->getResponse();
    }
    
    /**
     * Updates the authenticating user's current status and attaches media for upload. In other words, it creates a Tweet with a picture attached.
     * @param string $status The text of your status update.
     * @param string $imagefile full path to the image file
     * @param boolean $possibly_sensitive Set to true for content which may not be suitable for every audience.
     * @param string $in_reply_to_status_id The ID of an existing status that the update is in reply to. This parameter will be ignored unless the author of the tweet this parameter references is mentioned within the status text. Therefore, you must include @username, where username is the author of the referenced tweet, within the update.
     * @param long $lat The latitude of the location this tweet refers to. This parameter will be ignored unless it is inside the range -90.0 to +90.0 (North is positive) inclusive. It will also be ignored if there isn't a corresponding long parameter. Example Values: 37.7821120598956
     * @param long $long The longitude of the location this tweet refers to. The valid ranges for longitude is -180.0 to +180.0 (East is positive) inclusive. This parameter will be ignored if outside that range, not a number, geo_enabled is disabled, or if there not a corresponding lat parameter.Example Values: -122.400612831116
     * @param string $place_id A place in the world identified by a Twitter place ID. Place IDs can be retrieved from geo/reverse_geocode.Example Values: df51dec6f4ee2b2c
     * @param boolean $display_coordinates Whether or not to put a pin on the exact coordinates a tweet has been sent from.
     * @return string JSON Response
     */
    public function updateWithMedia($status, $imagefile, $possibly_sensitive=false, $in_reply_to_status_id=null, $lat=null, $long=null, $place_id=null, $display_coordinates=null){
        $apiUrl = self::API_BASE_URL.self::API_UPDATE_WITH_MEDIA;
        $params=array(
            'status' => $status,
            'media[]' => base64_encode(file_get_contents($imagefile)),
            'possibly_sensitive' => $possibly_sensitive
        );
        if(isset($in_reply_to_status_id)){
            $params['in_reply_to_status_id'] = $in_reply_to_status_id;
        }
        if(isset($lat)){
            $params['lat'] = $lat;
        }
        if(isset($long)){
            $params['long'] = $long;
        }
        if(isset($place_id)){
            $params['place_id'] = $place_id;
        }
        if(isset($display_coordinates)){
            $params['display_coordinates'] = $display_coordinates;
        }
        //$headers = 'Content-Type: multipart/form-data';
        $this->sendRequest($apiUrl, $params, self::REQUEST_METHOD_POST, true);
        return $this->getResponse();
    }
}
?>
