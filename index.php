<?php
/*
require_once './classes/oauth-client.php';
require_once './classes/oauth-communicator.php';
require_once './classes/oauth-server.php';
require_once './classes/oauth-user.php';
require_once './classes/twitter-status.php';
 * 
 */
foreach (glob("./classes/*.php") as $filename)
{
    require_once $filename;
}

$OauthClient = new OauthClient("A7NX7yIAGXuk3CDvTEDzLg", "Nnz3HrNjNCE6inhGVTjA6FWl9lleYnL2gUu6lf2PZd0");
$OauthUser = new OauthUser("19602888-A3bNfPSzZzca6ydGmL3oN5fMyAnk0RpLtxA7vbNri", "Abkw6VwuS9rdQ54JwSchkfnCibP4gWmKkunda5dBWao");
//$Server = new OauthServer($Consumer, $User);
$Tweets = new TwitterTweets($OauthClient, $OauthUser);
$Search = new TwitterSearch($OauthClient, $OauthUser);
//$json = $Tweets->update("testing false", "", "57.49", "12.22", "", "", "false");
//$json = $Tweets->destroy('363238041806471168', "1");
//$json = $Tweets->retweet('363238937315540992', "true");
//$json = $Tweets->show('123', '1', '1');
//$json = $Tweets->showRetweets('1234567890');
$json = $Search->search("joomla");
$json_array = json_decode($json);
?>
<pre>
<?php    print_r($json_array); ?>
</pre>