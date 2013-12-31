<?php
/**
 * Twitter-API: Index file for testing
 *
 * @version  1.0
 * @package Stilero
 * @subpackage Twitter-API
 * @author Daniel Eliasson <daniel at stilero.com>
 * @copyright  (C) 2013-dec-30 Stilero Webdesign (http://www.stilero.com)
 * @license	GNU General Public License version 2 or later.
 * @link http://www.stilero.com
 */
define('_JEXEC', 1);
define('PATH_TWITTER_LIBRARY', dirname(__FILE__).'/library/');

define('PATH_TWITTER_ENDPOINTS', PATH_TWITTER_LIBRARY.'endpoints/');
define('PATH_TWITTER_HELPERS', PATH_TWITTER_LIBRARY.'helpers/');
define('PATH_TWITTER_OAUTH', PATH_TWITTER_LIBRARY.'oauth/');
define('PATH_TWITTER_TWITTEROAUTH', PATH_TWITTER_LIBRARY.'twitteroauth/');

foreach (glob(PATH_TWITTER_LIBRARY."*.php") as $filename){
    require_once $filename;
}
foreach (glob(PATH_TWITTER_OAUTH."*.php") as $filename){
    require_once $filename;
}
foreach (glob(PATH_TWITTER_TWITTEROAUTH."*.php") as $filename){
    require_once $filename;
}
foreach (glob(PATH_TWITTER_ENDPOINTS."*.php") as $filename){
    require_once $filename;
}
foreach (glob(PATH_TWITTER_HELPERS."*.php") as $filename){
    require_once $filename;
}
require_once dirname(__FILE__).'/jerror.php';
$consumerKey = 'zWUbW6ozHQPnPidtIWXjyw';
$consumerSecret = 'xHV2gQGjmyDCX2iwBRKLq1sQ6TWJxNfqhj7Le62G26A';
$token = '19602888-zGpIeTYRIkeXPiRYXK5uJUyrfx67pSstMnkeG9Q1Q';
$tokenSecret = 'HmnBI1xX0ERQxHV9vjTO79XdCxi8LgUOYpDIihbDeDo';
$Twitter = new StileroTwitter($consumerKey, $consumerSecret, $token, $tokenSecret);
$json = $Twitter->Tweets->update('Testing with images');
$response = StileroTwitterResponse::handle($json);
$imagefile = dirname(__FILE__).'/joomla_logo_black.jpg';
var_dump($response);exit;
?>