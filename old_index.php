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

$Consumer = new OauthClient("A7NX7yIAGXuk3CDvTEDzLg", "Nnz3HrNjNCE6inhGVTjA6FWl9lleYnL2gUu6lf2PZd0");
$User = new OauthUser("19602888-A3bNfPSzZzca6ydGmL3oN5fMyAnk0RpLtxA7vbNri", "Abkw6VwuS9rdQ54JwSchkfnCibP4gWmKkunda5dBWao");
//$Server = new OauthServer($Consumer, $User);
$Tweets = new TwitterTweets($Consumer, $User);
$Search = new TwitterSearch($Consumer, $User);
//$json = $Search->search('joomla');
$json = $Tweets->update("Testing again #mytag");
//$json = $Tweets->destroy('363735944530116608');
//$json = $Tweets->retweet('1234567890');
//$json = $Tweets->showRetweets('1234567890');
//$json = $Tweets->home();
$json_array = json_decode($json);
?>
<pre>
<?php    print_r($json_array); ?>
</pre>