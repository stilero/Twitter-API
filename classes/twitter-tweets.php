<?php

/**
 * Class Twitter Status
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Class Twitter
 * @author Daniel Eliasson (joomla@stilero.com)
 * @copyright  (C) 2013-jan-06 Stilero Webdesign (www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
class TwitterTweets extends OauthRequest{
    
    const API_BASE_URL = 'https://api.twitter.com/1.1/statuses/';
    //const API_BASE_URL = 'http://www.stilero.com/';
    const API_UPDATE_URL  = 'update.json';
    //const API_UPDATE_URL  = 'pingtest.php';
    const API_DESTROY_URL  = 'destroy/';
    const API_RETWEET_URL  = 'retweet/';
    const API_SHOW_URL  = 'show/';
    const API_SHOW_RETWEETS_URL  = 'retweets/';
    const API_UPDATE_WITH_MEDIA_URL  = 'update_with_media/';
    const API_URL_ENDING  = '.json';
    
    public function __construct($OauthClient, $OauthUser) {
        parent::__construct($OauthClient, $OauthUser);
    }
    
    /**
     * Updates the authenticating user's current status, also known as tweeting. 
     * For each update attempt, the update text is compared with the 
     * authenticating user's recent tweets. Any attempt that would result in 
     * duplication will be blocked, resulting in a 403 error. 
     * Therefore, a user cannot submit the same status twice in a row.
     * @param string $status The text of your status update, up to 140 characters.
     * @param string $in_reply_to_status_id The ID of an existing status that the update is in reply to. This parameter will be ignored unless the author of the tweet this parameter references is mentioned within the status text. Therefore, you must include @username, where username is the author of the referenced tweet, within the update.
     * @param string $lat The latitude of the location this tweet refers to. This parameter will be ignored unless it is inside the range -90.0 to +90.0 (North is positive) inclusive. It will also be ignored if there isn't a corresponding long parameter. Example Values: 37.7821120598956
     * @param string $long The longitude of the location this tweet refers to. The valid ranges for longitude is -180.0 to +180.0 (East is positive) inclusive. This parameter will be ignored if outside that range, if it is not a number, if geo_enabled is disabled, or if there not a corresponding lat parameter. Example Values: -122.400612831116
     * @param string $place_id A place in the world. These IDs can be retrieved from GET geo/reverse_geocode. Example Values: df51dec6f4ee2b2c
     * @param string $display_coordinates Whether or not to put a pin on the exact coordinates a tweet has been sent from.
     * @param string $trim_user When set to either true, t or 1, each tweet returned in a timeline will include a user object including only the status authors numerical ID. Omit this parameter to receive the complete user object.
     * @return string JSON response
     */
    public function update($status, $in_reply_to_status_id="", $lat="", $long="", $place_id="", $display_coordinates="", $trim_user="") {
        foreach (get_defined_vars() as $key => $value) {
           if($value != ""){
               $params[$key] = $value;
           } 
        }
        $url = self::API_BASE_URL.self::API_UPDATE_URL;
        return $this->query($url);
        //return $this->send($url, $params, Curler::REQUEST_METHOD_POST, true);
        //$this->request($apiUrl, $params, self::REQUEST_METHOD_POST);
        //return $this->getResponse();
    }
    
    /**
     * Destroys the status specified by the required ID parameter. 
     * The authenticating user must be the author of the specified status. 
     * Returns the destroyed status if successful.
     * @param string $id The numerical ID of the desired status. Example Values: 123
     * @param string $trim_user When set to either true, t or 1, each tweet returned in a timeline will include a user object including only the status authors numerical ID. Omit this parameter to receive the complete user object. Example Values: true
     * @return string JSON response. Returns the destroyed status if successful.
     */
    public function destroy($id, $trim_user=""){
        if($trim_user != ""){
            $params['trim_user'] = $trim_user;
        } 
        $apiUrl = self::API_BASE_URL.self::API_DESTROY_URL.$id.self::API_URL_ENDING;
        $this->request($apiUrl, $params);
        return $this->getResponse();
    }
    
    /**
     * Retweets a tweet. Returns the original tweet with retweet details embedded.
     * @param string $id The numerical ID of the desired status. Example Values: 123
     * @param string $trim_user When set to either true, t or 1, each tweet returned in a timeline will include a user object including only the status authors numerical ID. Omit this parameter to receive the complete user object. Example Values: true
     * @return string JSON Response
     */
    public function retweet($id, $trim_user=""){
        if($trim_user != ""){
            $params['trim_user'] = $trim_user;
        } 
        $apiUrl = self::API_BASE_URL.self::API_RETWEET_URL.$id.self::API_URL_ENDING;
        $this->request($apiUrl, $params);
        return $this->getResponse();
    }
    
    /**
     * Returns a single Tweet, specified by the id parameter. 
     * The Tweet's author will also be embedded within the tweet.
     * @param string $id The numerical ID of the desired Tweet. Example Values: 123
     * @param string $trim_user When set to either true, t or 1, each tweet returned in a timeline will include a user object including only the status authors numerical ID. Omit this parameter to receive the complete user object. Example Values: true
     * @param string $include_my_retweet When set to either true, t or 1, any Tweets returned that have been retweeted by the authenticating user will include an additional current_user_retweet node, containing the ID of the source status for the retweet. Example Values: true
     * @param string $include_entities The entities node will be disincluded when set to false. Example Values: false
     * @return string JSON Response
     */
    public function show($id, $trim_user="", $include_my_retweet="", $include_entities=""){
        //$params = array();
        foreach (get_defined_vars() as $key => $value) {
           if($value != "" && $key!='id'){
               //$param = array($key => $value);
               //$params = array_merge($params, $param);
               $params[$key] = $value;
           } 
        }
        //print_r($params);exit;
        $params = array();
        //var_dump($params);exit;
        $apiUrl = self::API_BASE_URL.self::API_SHOW_URL.$id.self::API_URL_ENDING;
        $this->request($apiUrl, $params, self::REQUEST_METHOD_GET);
        return $this->getResponse();
    }
    
    /**
     * Get up to 100 of the first retweets of a certain tweet
     * @param string $tweetID a numerical tweet ID
     * @return string JSON response
     */
    public function showRetweets($tweetID){
        $apiUrl = self::API_BASE_URL.self::API_SHOW_RETWEETS_URL.$tweetID.self::API_URL_ENDING;
        $this->request($apiUrl, array(), self::REQUEST_METHOD_GET);
        return $this->getResponse();
    }
    
    /**
     * Updates the authenticating user's current status and attaches media for upload. In other words, it creates a Tweet with a picture attached.
     * @param string $message
     * @return string JSON response
     */
    public function updateWithMedia($message, $mediaUrls) {
        $statuses = array('status' => $message);
        $medias = array('media[]' => $mediaUrls );
        $params = array_merge($statuses, $medias);
        $apiUrl = self::API_BASE_URL.self::API_UPDATE_WITH_MEDIA_URL;
        $this->request($apiUrl, $params, self::REQUEST_METHOD_POST);
        return $this->getResponse();
    }
}
?>
