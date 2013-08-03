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
$params = array(
    'status' => "Hello Ladies + Gentlemen, a signed OAuth request!",
    'include_entities' => 'true',
    'oauth_consumer_key' => 'xvz1evFS4wEEPTGEFPHBog',
    'oauth_nonce' => 'kYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg',
    'oauth_signature_method' => 'HMAC-SHA1',
    'oauth_timestamp' => '1318622958',
    'oauth_token' => '370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb',
    'oauth_version' => '1.0'
);
print $actualSignature = oauthSignature::generateSignature('POST&https%3A%2F%2Fapi.twitter.com%2F1%2Fstatuses%2Fupdate.json&include_entities%3Dtrue%26oauth_consumer_key%3Dxvz1evFS4wEEPTGEFPHBog%26oauth_nonce%3DkYjzVBB8Y0ZFabxSWbWovY3uYSQ2pTgmZeNu2VS4cg%26oauth_signature_method%3DHMAC-SHA1%26oauth_timestamp%3D1318622958%26oauth_token%3D370773112-GmHxMAgYyLbNEtIKZeRNFsMKPR9EyMZeS9weJAEb%26oauth_version%3D1.0%26status%3DHello%2520Ladies%2520%252B%2520Gentlemen%252C%2520a%2520signed%2520OAuth%2520request%2521', 'kAcSOqF21Fu85e7zjz7ZN2U4ZRhfV3WpwPAoE3Z7kBw&LswwdoUaIvS8ltyTt5jkRh4J50vUPVVHtR2YPi5kE');
$expectedSignature = 'tnnArxj06cWHq44gCs1OSKk/jLY=';
if($actualSignature == $expectedSignature){
    print "   =>> ok";
}else{
    print "   =>> fel";
}
exit;
$OauthClient = new OauthClient("A7NX7yIAGXuk3CDvTEDzLg", "Nnz3HrNjNCE6inhGVTjA6FWl9lleYnL2gUu6lf2PZd0");
$OauthUser = new OauthUser("19602888-A3bNfPSzZzca6ydGmL3oN5fMyAnk0RpLtxA7vbNri", "Abkw6VwuS9rdQ54JwSchkfnCibP4gWmKkunda5dBWao");
//$Server = new OauthServer($Consumer, $User);
$Tweets = new TwitterTweets($OauthClient, $OauthUser);
$Search = new TwitterSearch($OauthClient, $OauthUser);
$json = $Tweets->update("testing false", "", "57.49", "12.22", "", "", "false");
//$json = $Tweets->destroy('363238041806471168', "1");
//$json = $Tweets->retweet('363238937315540992', "true");
//$json = $Tweets->show('123', '1', '1');
//$json = $Tweets->showRetweets('1234567890');
//$json = $Search->search("joomla");
$json_array = json_decode($json);
?>
<pre>
<?php    print_r($json_array); ?>
</pre>